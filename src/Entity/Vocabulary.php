<?php

namespace Mii\Tag\Entity;

use Pagekit\System\Entity\DataTrait;
use Pagekit\Framework\Database\Event\EntityEvent;

/**
* @Entity(tableClass="@miitag_vocabularies")
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

  /** @Column */
  protected $name;

  /** @Column */
  protected $description;

  /** @Column(type="integer") */
  protected $status;

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
    $this->content = $name;
  }

  public function getDescription()
  {
    return $this->name;
  }

  public function setDescription($description)
  {
    $this->content = $description;
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
      self::STATUS_ACTIVATED        => __('Activated'),
    ];
  }

}
