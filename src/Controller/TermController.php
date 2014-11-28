<?php

namespace Mii\Taxonomy\Controller;

use Mii\Taxonomy\MiiTaxonomyExtension;
use Mii\Taxonomy\Entity\Term;
use Pagekit\Component\Database\ORM\Repository;
use Pagekit\Framework\Controller\Controller;
use Pagekit\Framework\Controller\Exception;

/**
* @Route(name="@miiTaxonomy/admin/term")
* @Access("miiTaxonomy: manage terms", admin=true)
*/
class TermController extends Controller
{
  /**
  * @var MiiTaxonomyExtension
  */
  protected $extension;

  /**
  * @var Repository
  */
  protected $vocabularies;

  /**
  * @var Repository
  */
  protected $terms;

  /**
  * Constructor.
  */
  public function __construct(MiiTaxonomyExtension $extension)
  {
    $this->extension      = $extension;
    $this->vocabularies 	= $this['db.em']->getRepository('Mii\Taxonomy\Entity\Vocabulary');
    $this->terms          = $this['db.em']->getRepository('Mii\Taxonomy\Entity\Term');
  }

  /**
  * @Request({"vocabulary":"int", "filter": "array", "page":"int"})
  * @Response("extension://miitaxonomy/views/admin/term/index.razr")
  */
  public function indexAction($vid = 0, $filter = null, $page = 0)
  {
    //CHECK VOCABULARY
    if (!$vocabulary = $this->vocabularies->find($vid)) {

      $this['message']->error(__('Select a valid vocabulary first.'));
      return $this->redirect('@miiTaxonomy/admin/vocabulary');

    }

    // self::$app['miiTaxonomy.api']->taxonomy_term_load(2);

    $query = $this->terms->query();

    $query->where(['vid' => $vid]);

    if (isset($filter['status']) && is_numeric($filter['status'])) {

      $query->where(['status' => intval($filter['status'])]);

    }

    if (isset($filter['search']) && strlen($filter['search'])) {

      $query->where(['name LIKE :search OR description LIKE :search'], ['search' => "%{$filter['search']}%"]);

    }

    $limit = $this->extension->getConfig('defaults.index_items_per_page', 15);
    $count = $query->count();
    $total = ceil($count / $limit);
    $page  = max(0, min($total - 1, $page));
    $terms = $query->related(['vocabulary'])->offset($page * $limit)->limit($limit)->orderBy('id', 'ASC')->get();

    if ($this['request']->isXmlHttpRequest()) {

      return $this['response']->json([
      'table' => $this['view']->render('extension://miitaxonomy/views/admin/term/table.razr', compact('count', 'terms')),
      'total' => $total
      ]);

    }

    return [
      'head.title' => __('Terms'),
      'terms' => $terms,
      'vocabulary' => $vocabulary,
      'statuses' => Term::getStatuses(),
      'filter' => $filter,
      'total' => $total,
      'count' => $count,
    ];
  }

  /**
  * @Request({"vocabulary": "int"})
  * @Response("extension://miitaxonomy/views/admin/term/edit.razr")
  */
  public function addAction($vid)
  {
    //CHECK VOCABULARY
    if(!$vocabulary = $this->vocabularies->find($vid)) {
      
      $this['message']->error(__('Select a valid vocabulary first.'));
      return $this->redirect('@miiTaxonomy/admin/vocabulary');

    }

    $term = new Term;

    return [
      'head.title' => __('Add Term'),
      'term' => $term,
      'vocabulary' => $vocabulary,
      'statuses' => Term::getStatuses(),
    ];
  }


  /**
  * @Request({"id": "int", "term": "array"}, csrf=true)
  * @Response("json")
  */
  public function saveAction($id, $data)
  {
    try {

      if (!$term = $this->terms->find($id)) {

        $term = new Term;

      }

      $this->terms->save($term, $data);

      return ['message' => $id ? __('Term saved.') : __('Term created.'), 'id' => $term->getId()];

    } catch (Exception $e) {

      return ['message' => $e->getMessage(), 'error' => true];

    }
  }

  /**
  * @Request({"id": "int"})
  * @Response("extension://miitaxonomy/views/admin/term/edit.razr")
  */
  public function editAction($id)
  {

    try {

      if (!$term = $this->terms->query()->where(compact('id'))->related(['vocabulary'])->first()) {
        throw new Exception(__('Invalid term id.'));
      }

    } catch (Exception $e) {

      $this['message']->error($e->getMessage());
      return $this->redirect('@miiTaxonomy/admin/vocabulary');

    }

    return [
      'head.title'  => __('Edit Term'),
      'term'         => $term,
      'vocabulary'  => $term->getVocabulary(),
      'statuses'    => Term::getStatuses(),
    ];
  }


  /**
  * @Request({"ids": "int[]"}, csrf=true)
  * @Response("json")
  */
  public function deleteAction($ids = [])
  {
    foreach ($ids as $id) {
      if ($term = $this->terms->find($id)) {
        $this->terms->delete($term);
      }
    }

    return ['message' => _c('{0} No term deleted.|{1} Term deleted.|]1,Inf[ Terms deleted.', count($ids))];
  }

  /**
  * @Request({"status": "int", "ids": "int[]"}, csrf=true)
  * @Response("json")
  */
  public function statusAction($status, $ids = [])
  {
    $statuses = Term::getStatuses();
    if(array_key_exists($status, $statuses)) {
      foreach ($ids as $id) {
        if ($term = $this->terms->find($id) and $term->getStatus() != $status) {
          $term->setStatus($status);
          $this->terms->save($term);
        }
      }
      $message = _c('{0} No term '. $statuses[$status] .'.|{1} Term '. $statuses[$status] .'.|]1,Inf[ Terms '. $statuses[$status] .'.', count($ids));
    }
    else {
      $message = __('Status unavailable');
    }

    return compact('message');

  }

}
