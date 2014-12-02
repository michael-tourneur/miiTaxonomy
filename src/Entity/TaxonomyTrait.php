<?php

namespace Mii\Taxonomy\Entity;

trait TaxonomyTrait
{
    /** @Column(type="integer") @Id */
    protected $id;

    /** @Column */
    protected $name;

    /** @Column */
    protected $description = '';

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
        return $this->label;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @PostDelete
     */
    public function postDelete(EntityEvent $event)
    {
        $connection = $event->getConnection();
        $connection->delete('@miitaxonomy_index', ['tid' => $this->getId()]);
    }

}
