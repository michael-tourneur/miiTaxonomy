<?php

namespace Mii\Taxonomy\Controller;

use Mii\Taxonomy\MiiTaxonomyExtension;
use Mii\Taxonomy\Entity\Vocabulary;
use Pagekit\Component\Database\ORM\Repository;
use Pagekit\Framework\Controller\Controller;
use Pagekit\Framework\Controller\Exception;
use Doctrine\DBAL\DBALException;

/**
* @Route(name="@miiTaxonomy/admin/vocabulary")
* @Access("miiTaxonomy: manage vocabularies", admin=true)
*/
class VocabularyController extends Controller
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
  * Constructor.
  */
  public function __construct(MiiTaxonomyExtension $extension)
  {
    $this->extension    = $extension;
    $this->vocabularies  = $this['db.em']->getRepository('Mii\Taxonomy\Entity\Vocabulary');
  }

  /**
  * @Request({"filter": "array", "page":"int"})
  * @Response("extension://miitaxonomy/views/admin/vocabulary/index.razr")
  */
  public function indexAction($filter = null, $page = 0)
  {
    $query = $this->vocabularies->query();

    if (isset($filter['status']) && is_numeric($filter['status'])) {

      $query->where(['status' => intval($filter['status'])]);

    }

    if (isset($filter['search']) && strlen($filter['search'])) {

      $query->where(['name LIKE :search OR description LIKE :search'], ['search' => "%{$filter['search']}%"]);
      
    }


    $limit = $this->extension->getConfig('index_items_per_page', 15);
    $count = $query->count();
    $total = ceil($count / $limit);
    $page  = max(0, min($total - 1, $page));
    $vocabularies = $query->related(['tags'])->offset($page * $limit)->limit($limit)->orderBy('id', 'ASC')->get();

    $vocabularies = $this['miiTaxonomy.api']->taxonomy_vocabulary_load_multiple([1,2]);

    if ($this['request']->isXmlHttpRequest()) {
      return $this['response']->json([
        'table' => $this['view']->render('extension://miitaxonomy/views/admin/vocabulary/table.razr', compact('count', 'vocabularies')),
        'total' => $total
      ]);
    }
    return [
      'head.title' => __('Vocabularies'),
      'vocabularies' => $vocabularies,
      'statuses' => Vocabulary::getStatuses(),
      'filter' => $filter,
      'total' => $total,
      'count' => $count,
    ];
  }

  /**
  * @Response("extension://miitaxonomy/views/admin/vocabulary/edit.razr")
  */
  public function addAction()
  {
    $vocabulary = new Vocabulary;

    return [
      'head.title' => __('Add Vocabulary'),
      'vocabulary' => $vocabulary,
      'statuses' => Vocabulary::getStatuses(),
    ];
  }


  /**
  * @Request({"id": "int", "vocabulary": "array"}, csrf=true)
  * @Response("json")
  */
  public function saveAction($id, $data)
  {


    try {

      if (!$vocabulary = $this->vocabularies->find($id)) {

        $vocabulary = new Vocabulary;

      }

      try {
        $data['machine_name'] = $this->mechanize($data['name']);
        $this->vocabularies->save($vocabulary, $data);
      } catch (DBALException $e) {
        return ['message' => 'Something happens. Vocabulary cannot be saved.', 'error' => true];
      }

      return ['message' => $id ? __('Vocabulary saved.') : __('Vocabulary created.'), 'id' => $vocabulary->getId()];

    } catch (Exception $e) {

      return ['message' => $e->getMessage(), 'error' => true];

    }
  }

  /**
  * @Request({"id": "int"})
  * @Response("extension://miitaxonomy/views/admin/vocabulary/edit.razr")
  */
  public function editAction($id)
  {
    try {

      if (!$vocabulary = $this->vocabularies->query()->where(compact('id'))->first()) {
        throw new Exception(__('Invalid vocabulary id.'));
      }

    } catch (Exception $e) {

      $this['message']->error($e->getMessage());

      return $this->redirect('@miiTaxonomy/admin/vocabulary');
    }

    return [
      'head.title' => __('Edit Vocabulary'),
      'vocabulary' => $vocabulary,
      'statuses' => Vocabulary::getStatuses(),
    ];
  }


  /**
  * @Request({"ids": "int[]"}, csrf=true)
  * @Response("json")
  */
  public function deleteAction($ids = [])
  {
    foreach ($ids as $id) {
      if ($vocabulary = $this->vocabularies->find($id)) {
        $this->vocabularies->delete($vocabulary);
      }
    }

    return ['message' => _c('{0} No vocabulary deleted.|{1} Vocabulary deleted.|]1,Inf[ Vocabularies deleted.', count($ids))];
  }

  /**
  * @Request({"status": "int", "ids": "int[]"}, csrf=true)
  * @Response("json")
  */
  public function statusAction($status, $ids = [])
  {
    $statuses = Vocabulary::getStatuses();
    if(array_key_exists($status, $statuses)) {
      foreach ($ids as $id) {
        if ($vocabulary = $this->vocabularies->find($id) and $vocabulary->getStatus() != $status) {
          $vocabulary->setStatus($status);
          $this->vocabularies->save($vocabulary);
        }
      }
      $message = _c('{0} No vocabulary '. $statuses[$status] .'.|{1} Vocabulary '. $statuses[$status] .'.|]1,Inf[ Vocabularies '. $statuses[$status] .'.', count($ids));
    }
    else {
      $message = __('Status unavailable');
    }

    return compact('message');

  }

  protected function mechanize($name)
  {
      $name = preg_replace('/\xE3\x80\x80/', ' ', $name);
      $name = str_replace('-', ' ', $name);
      $name = preg_replace('#[:\#\*"@+=;!><&\.%()\]\/\'\\\\|\[]#', "\x20", $name);
      $name = str_replace('?', '', $name);
      $name = trim(mb_strtolower($name, 'UTF-8'));
      $name = preg_replace('#\x20+#', '-', $name);

      return $name;
  }
}
