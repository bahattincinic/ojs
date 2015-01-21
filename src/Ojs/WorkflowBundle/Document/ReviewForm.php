<?php

namespace Ojs\WorkflowBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 *
 * @MongoDb\Document(collection="review_forms",repositoryClass="Ojs\WorkflowBundle\Repository\ReviewFormRepository")
 */
class ReviewForm
{

    /**
     * @MongoDb\Id
     */
    protected $id;

    /** @MongoDb\Int */
    protected $journalid;

    /** @MongoDb\String */
    protected $title;

    /** @MongoDb\Boolean */
    protected $mandotary;

    /**
     * @MongoDb\String 
     *  
     *  - textbox
     *  - textarea
     *  - checkboxe 
     *  - radio
     *  - dropdown
     *  - scale_1_5
     */
    protected $inputType;

    /** @MongoDb\Boolean */
    protected $onlyreply;

    /** @MongoDb\Hash */
    protected $fields;

    /**
     * Get id
     *
     * @return id $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set journalid
     *
     * @param int $journalid
     * @return self
     */
    public function setJournalid($journalid)
    {
        $this->journalid = $journalid;
        return $this;
    }

    /**
     * Get journalid
     *
     * @return int $journalid
     */
    public function getJournalid()
    {
        return $this->journalid;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return self
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Get title
     *
     * @return string $title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set mandotary
     *
     * @param boolean $mandotary
     * @return self
     */
    public function setMandotary($mandotary)
    {
        $this->mandotary = $mandotary;
        return $this;
    }

    /**
     * Get mandotary
     *
     * @return boolean $mandotary
     */
    public function getMandotary()
    {
        return $this->mandotary;
    }

    /**
     * Set inputType
     *
     * @param string $inputType
     * @return self
     */
    public function setInputType($inputType)
    {
        $this->inputType = $inputType;
        return $this;
    }

    /**
     * Get inputType
     *
     * @return string $inputType
     */
    public function getInputType()
    {
        return $this->inputType;
    }

    /**
     * Set onlyreply
     *
     * @param boolean $onlyreply
     * @return self
     */
    public function setOnlyreply($onlyreply)
    {
        $this->onlyreply = $onlyreply;
        return $this;
    }

    /**
     * Get onlyreply
     *
     * @return boolean $onlyreply
     */
    public function getOnlyreply()
    {
        return $this->onlyreply;
    }

    /**
     * Set fields
     *
     * @param hash $fields
     * @return self
     */
    public function setFields($fields)
    {
        $this->fields = $fields;
        return $this;
    }

    /**
     * Get fields
     *
     * @return hash $fields
     */
    public function getFields()
    {
        return $this->fields;
    }

}