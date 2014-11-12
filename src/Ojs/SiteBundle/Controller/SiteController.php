<?php

namespace Ojs\SiteBundle\Controller;

use Ojs\Common\Controller\OjsController as Controller;

class SiteController extends Controller {

    /**
     * Global index page
     * @return type
     */
    public function indexAction() {
        /* @var $journalDomain \Ojs\Common\Model\JournalDomain */
        $journalDomain = $this->container->get('journal_domain');
        $em = $this->getDoctrine()->getManager();
        $journals = $em->getRepository('OjsJournalBundle:Journal')->findAll();

        $data['entity'] = $journalDomain->getCurrentJournal();
        $data['page'] = 'index';
        $data["journals"] = $journals;

        if ($data['entity']) {
            return $this->journalIndexAction($data['entity']->getId());
        }
        // anything else is anonym main page
        return $this->render('OjsSiteBundle::Site/anonymous_index.html.twig', $data);
    }

    public function userIndexAction() {
        $data['page'] = 'user';
        return $this->render('OjsManagerBundle:User:userwelcome.html.twig', $data);
    }

    public function browseIndexAction() {
        $data['page'] = 'browse';
        return $this->render('OjsSiteBundle::Site/browse_index.html.twig', $data);
    }

    public function institutionsIndexAction() {
        $em = $this->getDoctrine()->getManager();
        $data['entities'] = $em->getRepository('OjsJournalBundle:Institution')->findAll();
        $data['page'] = 'institution';
        return $this->render('OjsSiteBundle::Site/institutions_index.html.twig', $data);
    }

    public function institutionPageAction($institution_id) {
        $em = $this->getDoctrine()->getManager();
        $data['entity'] = $em->getRepository('OjsJournalBundle:Institution')->find($institution_id);
        $data['page'] = 'organizations';
        return $this->render('OjsSiteBundle::Site/institution_index.html.twig', $data);
    }

    public function categoriesIndexAction() {
        $data['page'] = 'categories';
        return $this->render('OjsSiteBundle::Site/categories_index.html.twig', $data);
    }

    public function topicsIndexAction() {
        $data['page'] = 'topics';
        return $this->render('OjsSiteBundle::Site/topics_index.html.twig', $data);
    }

    public function profileIndexAction() {
        $data['page'] = 'profile';
        return $this->render('OjsSiteBundle::Site/profile_index.html.twig', $data);
    }

    public function staticPagesAction($page = 'static') {
        $data['page'] = $page;
        return $this->render('OjsSiteBundle:Site:static/tos.html.twig', $data);
    }

    public function journalsIndexAction() {
        $journalDomain = $this->container->get('journal_domain');
        $em = $this->getDoctrine()->getManager();
        $data["journals"] = $em->getRepository('OjsJournalBundle:Journal')->findAll();
        $data["institution_types"] = $em->getRepository('OjsJournalBundle:InstitutionTypes')->findAll();
        
        // TODO find only has journal(s) and count them
        $subjects = $em->getRepository('OjsJournalBundle:Subject')->findAll();
        foreach ($subjects as $subject) {
            if ($subject->hasJournals()) {
                $data["subjects"][] = $subject;
            }
        }
        $data['entity'] = $journalDomain->getCurrentJournal();
        $data['page'] = 'journals';
        return $this->render('OjsSiteBundle::Site/journals_index.html.twig', $data);
    }

    public function journalIndexAction($journal_id) {
        $em = $this->getDoctrine()->getManager();
        $data['journal'] = $em->getRepository('OjsJournalBundle:Journal')->find($journal_id);
        $this->throw404IfNotFound($data['journal']);
        $data['page'] = 'journal';
        return $this->render('OjsSiteBundle::Site/journal_index.html.twig', $data);
    }

    /**
     * Also means last issue's articles
     * @param integer $journal_id 
     */
    public function lastArticlesIndexAction($journal_id) {
        $em = $this->getDoctrine()->getManager();
        $data['journal'] = $em->getRepository('OjsJournalBundle:Journal')->find($journal_id);
        $this->throw404IfNotFound($data['journal']);
        $data['articles'] = $em->getRepository('OjsJournalBundle:Article')->findByJournalId($journal_id);
        $data['page'] = 'articles';
        return $this->render('OjsSiteBundle::Site/last_articles_index.html.twig', $data);
    }

    public function articlePageAction($article_id) {
        $em = $this->getDoctrine()->getManager();
        /* @var $entity \Ojs\JournalBundle\Entity\Article  */
        $data['article'] = $em->getRepository('OjsJournalBundle:Article')->find($article_id);
        if(!$data['article'] ){
            throw $this->createNotFoundException($this->get('translator')->trans('Article Not Found'));
        }
        $data['journal'] = $data['article']->getJournal();
        $data['page'] = 'journals';
        return $this->render('OjsSiteBundle::Site/article_page.html.twig', $data);
    }

    public function archiveIndexAction($journal_id) {
        $em = $this->getDoctrine()->getManager();
        $data['journal'] = $em->getRepository('OjsJournalBundle:Journal')->find($journal_id);
        $this->throw404IfNotFound($data['journal']);
        // get all issues
        $data['issues'] = $em->getRepository('OjsJournalBundle:Issue')->findBy(array('journalId'=>$journal_id)); 
        foreach($data['issues'] as $issue){
            $data['issues_grouped'][$issue->getYear()][] = $issue;
        }
        $data['page'] = 'archive';
        return $this->render('OjsSiteBundle::Site/archive_index.html.twig', $data);
    }

}
