<?php

namespace Mii\Taxonomy\Entity;

trait TermsTrait
{
    /*
     * This const need to be declared on your entity using the trait
     * Const name should be VOCABULARY_NAME
     * Value of the const is the vocabulary machine name you want to use for this entity
     *
    const VOCABULARY_NAME = 'tag';
    **/

    /*
     * This const need to be declared on your entity using the trait
     * Const name should be ENTITY_NAME
     * Value of the const is the entity machine name you want to use for this entity
     *
    const ENTITY_NAME = 'question';
    **/

    /** @ManyToMany(targetEntity="Term", keyFrom="id", keyTo="id", tableThrough="@miiqa_question_tag", keyThroughFrom="question_id", keyThroughTo="tag_id") */
    protected $terms;

    public function addTag(Term $term)
    {
        $this->terms->add($term);
    }

    public function setTerms($terms){
        $this->terms = $terms;
    }

    public function getTerms(){
        return (array) $this->terms;
    }

    public function hasTerm($tagId){
        foreach ($this->getTerms() as $term) {
            if($term->getId() == $tagId) return true;
        }
        return false;
    }

    /**
     * @PostSave
     */
    public function postSave(EntityEvent $event)
    {
        $connection = $event->getConnection();
        $connection->delete('@miitaxonomy_index', ['entity_id' => $this->getId(), 'entity' => self::ENTITY_NAME]);

        if (is_array($this->terms)) {
            foreach ($this->terms as $term) {
                $connection->insert('@miitaxonomy_index', ['entity_id' => $this->getId(), 'entity' => self::ENTITY_NAME, 'tid' => $term->getId()]);
            }
        }
    }

    /**
     * @PostDelete
     */
    public function postDelete(EntityEvent $event)
    {
        $connection = $event->getConnection();
        $connection->delete('@miitaxonomy_index', ['entity_id' => $this->getId(), 'entity' => self::ENTITY_NAME]);
    }

}
