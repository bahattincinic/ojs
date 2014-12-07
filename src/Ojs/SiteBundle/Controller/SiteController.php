<?php

namespace Ojs\SiteBundle\Controller;

use Ojs\Common\Controller\OjsController as Controller;
use Ojs\JournalBundle\Entity\Journal;
use Ojs\JournalBundle\Entity\JournalRepository;
use Symfony\Component\HttpFoundation\Request;

class SiteController extends Controller {

    /**
     * Global index page
     * @return type
     */
    public function indexAction() {
        /* @var $journalDomain \Ojs\Common\Model\JournalDomain */
        $journalDomain = $this->container->get('journal_domain');
        $data['entity'] = $journalDomain->getCurrentJournal();
        $data['page'] = 'index';

        if ($data['entity']) {
            return $this->journalIndexAction($data['entity']->getId());
        }
        
        $em = $this->getDoctrine()->getManager();
        $data["journals"] = $em->getRepository('OjsJournalBundle:Journal')->findBy(array(), array(), 6);
        $data['institutions'] = $em->getRepository('OjsJournalBundle:Institution')->findBy(array(), array(), 6);
        $subjects = $em->getRepository('OjsJournalBundle:Subject')->findAll();
        foreach ($subjects as $subject) {
            if ($subject->getTotalJournalCount() > 0) {
                $data["subjects"][] = $subject;
            }
        }
        $data['page'] = 'index';
        // anything else is anonym main page
        return $this->render('OjsSiteBundle::Site/home.html.twig', $data);
    }


    public function browseIndexAction() {
        $data['page'] = 'browse';
        return $this->render('OjsSiteBundle::Site/browse_index.html.twig', $data);
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
        return $this->render('OjsSiteBundle:Site:static/' . $page . '.html.twig', $data);
    }

    public function institutionsIndexAction() {
        $em = $this->getDoctrine()->getManager();
        $data['entities'] = $em->getRepository('OjsJournalBundle:Institution')->findAll();
        $data['page'] = 'institution';
        return $this->render('OjsSiteBundle::Institution/institutions_index.html.twig', $data);
    }

    public function institutionPageAction($institution_id) {
        $em = $this->getDoctrine()->getManager();
        $data['entity'] = $em->getRepository('OjsJournalBundle:Institution')->find($institution_id);
        $data['page'] = 'organizations';
        return $this->render('OjsSiteBundle::Institution/institution_index.html.twig', $data);
    }

    public function journalsIndexAction(Request $request, $start = 0, $offset = 12) {
        $journalDomain = $this->container->get('journal_domain');
        $em = $this->getDoctrine()->getManager();
        /** @var JournalRepository $journalRepo */
        $journalRepo = $em->getRepository('OjsJournalBundle:Journal');
        $journalRepo->setStart($start);
        $journalRepo->setOffset($offset);
        $journalRepo->setFilter($request);
        $data["journals"] = $journalRepo->all();
        $data['totalPageCount'] = $journalRepo->getTotalPageCount();

        $data["institution_types"] = $em->getRepository('OjsJournalBundle:InstitutionTypes')->findAll();

        // TODO find only has journal(s) and count them
        $subjects = $em->getRepository('OjsJournalBundle:Subject')->findAll();
        foreach ($subjects as $subject) {
            if ($subject->getTotalJournalCount() > 0) {
                $data["subjects"][] = $subject;
            }
        }
        $data['entity'] = $journalDomain->getCurrentJournal();
        $data['page'] = 'journals';

        $data['currentPage'] = 1;
        $data['offset'] = $offset;
        $data['start'] = $start;
        return $this->render('OjsSiteBundle::Journal/journals_index.html.twig', $data);
    }

    public function journalIndexAction($journal_id) {
        $em = $this->getDoctrine()->getManager();
        $data['journal'] = $em->getRepository('OjsJournalBundle:Journal')->find($journal_id);
        $data['users'] = $em->getRepository('OjsUserBundle:UserJournalRole')->getUsers($journal_id, true);
        $data['pages'] = $em->getRepository('OjsWikiBundle:Page')->findBy(['journal' => $data['journal']]);
        $this->throw404IfNotFound($data['journal']);
        $data['page'] = 'journal';
        $data['blocks'] = $em->getRepository('OjsSiteBundle:Block')->journalBlocks($data['journal']);
        return $this->render('OjsSiteBundle::Journal/journal_index.html.twig', $data);
    }

    public function journalArticlesAction($journal_id) {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var Journal $journal */
        $journal = $em->getRepository('OjsJournalBundle:Journal')->find($journal_id);
        $articles = $journal->getArticles();
        $data = [
            'journal' => $journal,
            'articles' => $articles,
            'page' => 'journal',
            'blocks' => $em->getRepository('OjsSiteBundle:Block')->journalBlocks($journal),
            'pages' => $em->getRepository('OjsWikiBundle:Page')->findBy(['journal' => $journal])
        ];
        return $this->render('OjsSiteBundle::Journal/journal_articles.html.twig', $data);
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
        $data['blocks'] = $em->getRepository('OjsSiteBundle:Block')->journalBlocks($data['journal']);

        return $this->render('OjsSiteBundle::Journal/last_articles_index.html.twig', $data);
    }

    public function articlePageAction($article_id) {
        $em = $this->getDoctrine()->getManager();
        /* @var $entity \Ojs\JournalBundle\Entity\Article */
        $data['article'] = $em->getRepository('OjsJournalBundle:Article')->find($article_id);
        if (!$data['article']) {
            throw $this->createNotFoundException($this->get('translator')->trans('Article Not Found'));
        }
        $data['journal'] = $data['article']->getJournal();
        $data['page'] = 'journals';
        $data['blocks'] = $em->getRepository('OjsSiteBundle:Block')->journalBlocks($data['journal']);

        return $this->render('OjsSiteBundle::Journal/article_page.html.twig', $data);
    }

    public function archiveIndexAction($journal_id) {
        $em = $this->getDoctrine()->getManager();
        $data['journal'] = $em->getRepository('OjsJournalBundle:Journal')->find($journal_id);
        $this->throw404IfNotFound($data['journal']);
        // get all issues
        $data['issues'] = $em->getRepository('OjsJournalBundle:Issue')->findBy(array('journalId' => $journal_id));
        $data['issues_grouped'] = [];
        foreach ($data['issues'] as $issue) {
            $data['issues_grouped'][$issue->getYear()][] = $issue;
        }
        $data['page'] = 'archive';
        $data['pages'] = $em->getRepository('OjsWikiBundle:Page')->findBy(['journal' => $data['journal']]);
        $data['blocks'] = $em->getRepository('OjsSiteBundle:Block')->journalBlocks($data['journal']);

        return $this->render('OjsSiteBundle::Journal/archive_index.html.twig', $data);
    }

}
