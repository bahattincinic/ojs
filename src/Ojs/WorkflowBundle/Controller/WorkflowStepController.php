<?php

namespace Ojs\WorkflowBundle\Controller;

use \Symfony\Component\HttpFoundation\Request;

class WorkflowStepController extends \Ojs\Common\Controller\OjsController {

    public function indexAction() {
        $selectedJournal = $this->get("ojs.journal_service")->getSelectedJournal();

        $steps = $this->get('doctrine_mongodb')
                ->getRepository('OjsWorkflowBundle:JournalWorkflowStep')
                ->findBy(array('journalid' => $selectedJournal->getId()));

        return $this->render('OjsWorkflowBundle:WorkflowStep:index.html.twig', array('steps' => $steps));
    }

    /**
     * render "new workflow" flow
     */
    public function newAction() {
        $selectedJournal = $this->get("ojs.journal_service")->getSelectedJournal();
        $dm = $this->get('doctrine_mongodb')->getManager();

        $em = $this->getDoctrine();
        $roles = $em->getRepository('OjsUserBundle:Role')->findAll();
        $nextSteps = $dm->getRepository('OjsWorkflowBundle:JournalWorkflowStep')
                ->findByJournalid($selectedJournal->getId());
        $journalReviewForms = $dm->getRepository('OjsWorkflowBundle:ReviewForm')->getJournalForms($selectedJournal->getId());

        return $this->render('OjsWorkflowBundle:WorkflowStep:new.html.twig', array(
                    'roles' => $roles,
                    'nextSteps' => $nextSteps,
                    'journal' => $selectedJournal,
                    'forms' => $journalReviewForms
        ));
    }

    /**
     * insert new step with data from "new workflow" form data
     */
    public function createAction(Request $request) {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $step = new \Ojs\WorkflowBundle\Document\JournalWorkflowStep();
        $step->setMaxdays($request->get('maxdays'));
        $step->setFirststep($request->get('firststep') ? true : false);
        $step->setLaststep($request->get('laststep') ? true : false);
        $step->setJournalid($request->get('journalId'));
        $step->setRoles($this->prepareRoles($request->get('roles')));
        $step->setNextsteps($this->prepareNextsteps($request->get('nextsteps')));
        $step->setOnlyreply($request->get('onlyreply') ? true : false);
        $step->setStatus($request->get('status'));
        $step->setTitle($request->get('title'));
        $step->setIsVisible($request->get('isVisible') ? true : false);
        $step->setMustBeAssigned($request->get('mustBeAssigned') ? true : false);
        $step->setCanEdit($request->get('canEdit') ? true : false);
        $step->setCanSeeAuthor($request->get('canSeeAuthor') ? true : false);
        $reviewFormIds = $request->get('reviewforms');
        foreach ($reviewFormIds as $formId) {
            $form = $dm->getRepository('OjsWorkflowBundle:ReviewForm')->find($formId);
            $form && $step->addReviewForm($form);
        }
        $dm->persist($step);
        $dm->flush();

        return $this->redirect($this->generateUrl('workflowsteps_show', array('id' => $step->getId())));
    }

    /**
     * prepare an array given form values for JournalWorkflow $roles atrribute
     * @param  array $roleIds 
     * @return array
     */
    public function prepareRoles($roleIds) {
        $serializer = $this->get('serializer');
        $em = $this->get('doctrine')->getManager();
        $roles = array();
        $rolesArray = array();
        if ($roleIds) {
            foreach ($roleIds as $roleId) {
                $roles[] = $em->getRepository("OjsUserBundle:Role")->findOneById($roleId);
            }
        }
        if ($roles) {
            foreach ($roles as $role) {
                $rolesArray[] = json_decode(
                        $serializer->serialize($role, 'json'));
            }
        }
        return $rolesArray;
    }

    /**
     * prepare an array from given form values for JournalWorkflow nextSteps atrribute
     * @param  array $nextStepIds
     * @return array
     */
    public function prepareNextsteps($nextStepIds) {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $nextSteps = array();
        $nextStepsArray = array();
        if ($nextStepIds) {
            foreach ($nextStepIds as $nextStepId) {
                $nextSteps[] = $dm->getRepository("OjsWorkflowBundle:JournalWorkflowStep")->findOneById($nextStepId);
            }
        }
        if ($nextSteps) {
            foreach ($nextSteps as $step) {
                $nextStepsArray[] = array(
                    'id' => $step->getId(),
                    'title' => $step->getTitle());
            }
        }
        return $nextStepsArray;
    }

    public function editAction($id) {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $em = $this->getDoctrine()->getManager();
        $selectedJournal = $this->get("ojs.journal_service")->getSelectedJournal();
        $journalReviewForms = $dm->getRepository('OjsWorkflowBundle:ReviewForm')->getJournalForms($selectedJournal->getId());

        $step = $dm->getRepository('OjsWorkflowBundle:JournalWorkflowStep')->find($id);
        $journal = $em->getRepository('OjsJournalBundle:Journal')->findOneById($step->getJournalId());
        $roles = $em->getRepository('OjsUserBundle:Role')->findAll();
        $nextSteps = $dm->getRepository('OjsWorkflowBundle:JournalWorkflowStep')
                ->findByJournalid($selectedJournal->getId());

        return $this->render('OjsWorkflowBundle:WorkflowStep:edit.html.twig', array(
                    'roles' => $roles,
                    'nextSteps' => $nextSteps,
                    'journal' => $journal,
                    'step' => $step,
                    'forms' => $journalReviewForms
                        )
        );
    }

    public function deleteAction($id) {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $step = $dm->getRepository('OjsWorkflowBundle:JournalWorkflowStep')->find($id);
        $dm->remove($step);
        $dm->flush();

        return $this->redirect($this->generateUrl('manage_workflowsteps'));
    }

    public function showAction($id) {
        $step = $this->get('doctrine_mongodb')->getManager()
                        ->getRepository('OjsWorkflowBundle:JournalWorkflowStep')->find($id);

        return $this->render('OjsWorkflowBundle:WorkflowStep:show.html.twig', array('step' => $step));
    }

    public function updateAction(Request $request, $id) {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $em = $this->getDoctrine()->getManager();
        $repo = $dm->getRepository('OjsWorkflowBundle:JournalWorkflowStep');
        /* @var $step \Ojs\WorkflowBundle\Document\JournalWorkflowStep  */
        $step = $repo->find($id);
        $step->setTitle($request->get('title'));
        $step->setFirststep($request->get('firststep') ? true : false);
        $step->setLaststep($request->get('laststep') ? true : false);
        $step->setMaxdays($request->get('maxdays'));
        $step->setJournalid($request->get('journalId'));
        $step->setStatus($request->get('status'));
        $step->removeAllReviewForms();
        $reviewFormIds = $request->get('reviewforms');
        foreach ($reviewFormIds as $formId) {
            $form = $dm->getRepository('OjsWorkflowBundle:ReviewForm')->find($formId);
            $form && $step->addReviewForm($form);
        }
        $step->setRoles($this->prepareRoles($request->get('roles')));
        $step->setNextsteps($this->prepareNextsteps($request->get('nextsteps')));
        $step->setOnlyreply($request->get('onlyreply') ? true : false);
        $step->setIsVisible($request->get('isVisible') ? true : false);
        $step->setMustBeAssigned($request->get('mustBeAssigned') ? true : false);
        $step->setCanEdit($request->get('canEdit') ? true : false);
        $step->setCanSeeAuthor($request->get('canSeeAuthor'));
        $dm->persist($step);
        $dm->flush();

        return $this->redirect($this->generateUrl('workflowsteps_show', array('id' => $id)));
    }

}
