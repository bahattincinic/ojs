<?php

namespace Ojs\JournalBundle\Entity;

use Ojs\Common\Entity\GenericExtendedEntity;
/**
 * JournalSection
 */
class JournalSection extends GenericExtendedEntity
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $title;

    /**
     * @var boolean
     */
    private $allowIndex = true;

    /**
     * @var boolean
     */
    private $hideTitle = false;

    /**
     * @var integer
     */
    private $journalId;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $articles;

    /**
     *
     * @var \Ojs\JournalBundle\Entity\Journal
     */
    private $journal;

    public function __construct()
    {
        $this->articles = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Add articles
     *
     * @param  \Ojs\JournalBundle\Entity\Article $articles
     * @return Journal
     */
    public function addArticle(\Ojs\JournalBundle\Entity\Article $article)
    {
        $this->articles[] = $article;

        return $this;
    }

    /**
     * Remove articles
     *
     * @param \Ojs\JournalBundle\Entity\Article $article
     */
    public function removeArticle(\Ojs\JournalBundle\Entity\Article $article)
    {
        $this->articles->removeElement($article);
    }

    /**
     * Get articles
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getArticles()
    {
        return $this->articles;
    }

    /**
     * Set title
     *
     * @param  string         $title
     * @return JournalSection
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set allowIndex
     *
     * @param  boolean        $allowIndex
     * @return JournalSection
     */
    public function setAllowIndex($allowIndex)
    {
        $this->allowIndex = $allowIndex;

        return $this;
    }

    /**
     * Get allowIndex
     *
     * @return boolean
     */
    public function getAllowIndex()
    {
        return $this->allowIndex;
    }

    /**
     * Set hideTitle
     *
     * @param  boolean        $hideTitle
     * @return JournalSection
     */
    public function setHideTitle($hideTitle)
    {
        $this->hideTitle = $hideTitle;

        return $this;
    }

    /**
     * Get hideTitle
     *
     * @return boolean
     */
    public function getHideTitle()
    {
        return $this->hideTitle;
    }

    /**
     * Set journalId
     *
     * @param  integer        $journalId
     * @return JournalSection
     */
    public function setJournalId($journalId)
    {
        $this->journalId = $journalId;

        return $this;
    }

    /**
     * Get journalId
     *
     * @return integer
     */
    public function getJournalId()
    {
        return $this->journalId;
    }

    /**
     * Set journal
     *
     * @param  Journal        $journal
     * @return JournalSection
     */
    public function setJournal($journal)
    {
        $this->journal = $journal;

        return $this;
    }

    /**
     * Get journal
     *
     * @return integer
     */
    public function getJournal()
    {
        return $this->journal;
    }

}
