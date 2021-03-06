<?php

namespace Mii\Tag\Controller;

use Mii\Tag\MiiTagExtension;
use Mii\Tag\Entity\Tag;
use Pagekit\Component\Database\ORM\Repository;
use Pagekit\Framework\Controller\Controller;
use Pagekit\Framework\Controller\Exception;

/**
* @Route(name="@miiTag/admin/tag")
* @Access("miiTag: manage tags", admin=true)
*/
class TagController extends Controller
{
  /**
  * @var MiiTagExtension
  */
  protected $extension;

  /**
  * @var Repository
  */
  protected $tags;

  /**
  * Constructor.
  */
  public function __construct(MiiTagExtension $extension)
  {
    $this->extension    = $extension;
    $this->tags 	= $this['db.em']->getRepository('Mii\Tag\Entity\Tag');
  }

  /**
  * @Request({"filter": "array", "page":"int"})
  * @Response("extension://miiqa/views/admin/tag/index.razr")
  */
  public function indexAction($filter = null, $page = 0)
  {
    $query = $this->tags->query();

    if (isset($filter['search']) && strlen($filter['search'])) {
      $query->where(['term LIKE :search OR description LIKE :search'], ['search' => "%{$filter['search']}%"]);
    }


    $limit = $this->extension->getConfig('index.items_per_page');
    $count = $query->count();
    $total = ceil($count / $limit);
    $page  = max(0, min($total - 1, $page));
    $tags = $query->offset($page * $limit)->limit($limit)->orderBy('term', 'ASC')->get();

    if ($this['request']->isXmlHttpRequest()) {
      return $this['response']->json([
      'table' => $this['view']->render('extension://miitag/views/admin/tag/table.razr', compact('count', 'tags')),
      'total' => $total
      ]);
    }
    return [
    'head.title' => __('Tags'),
    'tags' => $tags,
    'filter' => $filter,
    'total' => $total,
    'count' => $count,
    ];
  }


  /**
  * @Request({"id": "int", "tag": "array"}, csrf=true)
  * @Response("json")
  */
  public function saveAction($id, $data)
  {
    try {

      if (!$tag = $this->tags->find($id)) {

        $tag = new Tag;

      }

      $this->tags->save($tag, $data);

      return ['message' => $id ? __('Tag saved.') : __('Tag created.'), 'id' => $tag->getId()];

    } catch (Exception $e) {

      return ['message' => $e->getMessage(), 'error' => true];

    }
  }


  /**
  * @Request({"ids": "int[]"}, csrf=true)
  * @Response("json")
  */
  public function deleteAction($ids = [])
  {
    foreach ($ids as $id) {
      if ($tag = $this->tags->find($id)) {
        $this->tags->delete($tag);
      }
    }

    return ['message' => _c('{0} No tag deleted.|{1} Tag deleted.|]1,Inf[ Tags deleted.', count($ids))];
  }

}
