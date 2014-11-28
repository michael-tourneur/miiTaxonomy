<?php

namespace Mii\Taxonomy;

use Mii\Taxonomy\MiiTaxonomyExtension;
use Mii\Taxonomy\Entity\Term;
use Mii\Taxonomy\Entity\Vocabulary;

use Pagekit\Component\Database\ORM\Repository;
use Pagekit\Framework\ApplicationTrait;

class Api
{
  use ApplicationTrait;

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
  public function __construct()
  {
    $this->vocabularies 	= self::$app['db.em']->getRepository('Mii\Taxonomy\Entity\Vocabulary');
    $this->terms          = self::$app['db.em']->getRepository('Mii\Taxonomy\Entity\Term');
  }

  /**
   * Load multiple taxonomy vocabularies based on certain conditions.
   *
   * This function should be used whenever you need to load more than one
   * vocabulary from the database. 
   *
   * @param $vids
   *  An array of taxonomy vocabulary IDs, or FALSE to load all vocabularies.
   * @param $conditions
   *  An array of conditions to add to the query.
   * @param $related
   *  An array of related entity to return.
   *
   * @return
   *  An array of vocabulary objects, indexed by ID.
   */
  public function taxonomy_vocabulary_load_multiple($vids = array(), $conditions = array()) {
    static $vocabularies = array();

    $token = serialize(array($vids, $conditions));

    if(!array_key_exists($token, $vocabularies)) {

      $query = $this->vocabularies->query();

      if($vids) $query->whereIn('id', (array)$vids);

      if(is_array($conditions)) {
        foreach ($conditions as $column => $value) {
          $query->where("{$column} = :value", [":value" => "\"{$value}\""]);
        }
      }

      $vocabularies[$token] = $query->get();

    }

    return $vocabularies[$token];
  } 

  /**
   * Return the vocabulary object matching a vocabulary ID.
   *
   * @param $vid
   *   The vocabulary's ID.
   *
   * @return
   *   The vocabulary object with all of its metadata, if exists, FALSE otherwise.
   *   Results are statically cached.
   *
   * @see taxonomy_vocabulary_machine_name_load()
   */
  public function taxonomy_vocabulary_load($vid) {
    $vocabularies = $this->taxonomy_vocabulary_load_multiple(array((int)$vid));
    return reset($vocabularies);
  }

  /**
   * Return the vocabulary object matching a vocabulary name.
   *
   * @param $name
   *   The vocabulary's name.
   *
   * @return
   *   The vocabulary object with all of its metadata, if exists, FALSE otherwise.
   *   Results are statically cached.
   *
   */
  public function taxonomy_get_vocabulary_by_name($name) {
    $vocabularies = $this->taxonomy_vocabulary_load_multiple(NULL, array('name' => $name));
    return reset($vocabularies);
  }

  /**
   * Return the term object matching a term ID.
   *
   * @param $tid
   *   A term's ID
   *
   * @return
   *   A taxonomy term object, or FALSE if the term was not found. Results are
   *   statically cached.
   */
  public function taxonomy_term_load($tid) {
    if (!is_numeric($tid)) {
      return FALSE;
    }
    $term = $this->taxonomy_term_load_multiple(array($tid), array());
    return $term ? $term[$tid] : FALSE;
  }

  /**
   * Try to map a string to an existing term, as for glossary use.
   *
   * Provides a case-insensitive and trimmed mapping, to maximize the
   * likelihood of a successful match.
   *
   * @param $name
   *   Name of the term to search for.
   * @param $vid
   *   (optional) Vocabulary ID to limit the search. Defaults to NULL.
   *
   * @return
   *   An array of matching term objects.
   */
  public function taxonomy_get_term_by_name($name, $vid = NULL) {
    $conditions = array('name' => trim($name));
    if (isset($vid)) {
      $conditions['vid'] = (int)$vid;
    }
    return $this->taxonomy_term_load_multiple(array(), $conditions);
  }


  /**
   * Load multiple taxonomy terms based on certain conditions.
   *
   * This function should be used whenever you need to load more than one term
   * from the database. Terms are loaded into memory and will not require
   * database access if loaded again during the same page request.
   *
   * @param $tids
   *   An array of taxonomy term IDs.
   * @param $conditions
   *   An associative array of conditions on the @miitaxonomy_terms
   *   table, where the keys are the database fields and the values are the
   *   values those fields must have.
   *
   * @return
   *   An array of term objects, indexed by tid. When no results are found, an
   *   empty array is returned.
   *
   */
  public function taxonomy_term_load_multiple($tids = array(), $conditions = array()) {
    static $terms = array();

    $token = serialize(array($tids, $conditions));

    if(!array_key_exists($token, $terms)) {

      $query = $this->terms->query();

      if(is_array($tids) && count($tids)) $query->whereIn('id', (array)$tids);

      if(is_array($conditions)) {
        foreach ($conditions as $column => $value) {
          $query->where([$column => $value]);
        }
      }

      $terms[$token] = $query->get();

    }//var_dump($terms[$token]); die();
    return $terms[$token];
  }

}