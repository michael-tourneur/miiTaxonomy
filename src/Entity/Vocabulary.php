<?php

namespace Mii\Taxonomy\Entity;

use Pagekit\System\Entity\DataTrait;
use Pagekit\Framework\Database\Event\EntityEvent;

/**
* @Entity(tableClass="@miitaxonomy_vocabularies")
*/
class Vocabulary
{
  use DataTrait;

  /* vocabulary deactivated status. */
  const STATUS_DEACTIVATED = 0;

  /* vocabulary activated status. */
  const STATUS_ACTIVATED = 1;

  /** @Column(type="integer") @Id */
  protected $id;

  /** @Column(type="string") */
  protected $name;

  /** @Column(type="string") */
  protected $machine_name;

  /** @Column(type="string") */
  protected $description;

  /**
   * @HasMany(targetEntity="Term", keyFrom="id", keyTo="vid")
   */
  protected $tags;

  /** @Column(type="integer") */
  protected $status = 0;

  public function getId()
  {
    return $this->id;
  }

  public function setId($id)
  {
    $this->id = $id;
  }

  public function getName()
  {
    return $this->name;
  }

  public function setName($name)
  {
    $this->name = $name;
  }

  public function getMachineName()
  {
    return $this->machine_name;
  }

  public function setMachineName($machine_name)
  {
    $this->machine_name = $machine_name;
  }

  public function getDescription()
  {
    return $this->description;
  }

  public function setDescription($description)
  {
    $this->description = $description;
  }

  public function getExcerpt()
  {
    $description = $this->getDescription();
    return (strlen($description) > 150) ? substr($description, 0, 150).'...' : $description;
  }

  public function getStatus()
  {
    return $this->status;
  }

  public function setStatus($status)
  {
    $this->status = $status;
  }

  public function getStatusText()
  {
    $statuses = self::getStatuses();

    return isset($statuses[$this->status]) ? $statuses[$this->status] : __('Unknown');
  }

  public static function getStatuses()
  {
    return [
      self::STATUS_DEACTIVATED    => __('Deactivated'),
      self::STATUS_ACTIVATED      => __('Activated'),
    ];
  }

  public function getTags()
  {
    return $this->tags;
  }

}
