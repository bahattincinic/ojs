<?php

namespace Ojs\UserBundle\Controller;

use Doctrine\ORM\EntityManager;
use Ojs\UserBundle\Entity\UserJournalRole;
use Ojs\UserBundle\Form\UserJournalRoleType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * UserJournalRole controller.
 *
 */
class UserJournalRoleController extends Controller
{
    /**
     * Lists all UserJournalRole entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('OjsUserBundle:UserJournalRole')->findAll();

        return $this->render('OjsUserBundle:UserJournalRole:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Creates a new UserJournalRole entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new UserJournalRole();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $data = $form->getData();
            $journal = $em->getRepository('OjsJournalBundle:Journal')->findOneById($data->getJournalId());
            $user = $em->getRepository('OjsUserBundle:User')->findOneById($data->getUserId());
            $role = $em->getRepository('OjsUserBundle:Role')->findOneById($data->getRoleId());
            $entity->setUser($user);
            $entity->setJournal($journal);
            $entity->setRole($role);
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('ujr_show', array('id' => $entity->getId())));
        }

        return $this->render('OjsUserBundle:UserJournalRole:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a UserJournalRole entity.
     *
     * @param UserJournalRole $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(UserJournalRole $entity)
    {
        $form = $this->createForm(new UserJournalRoleType(), $entity, array(
            'action' => $this->generateUrl('ujr_create'),
            'method' => 'POST',
            'user' => $this->getUser()
        ));

        $form->add('submit', 'submit', array('label' => 'Create', 'attr' => array('class' => 'row btn btn-success')));

        return $form;
    }

    /**
     * Displays a form to create a new UserJournalRole entity.
     *
     */
    public function newAction()
    {
        $entity = new UserJournalRole();
        $form = $this->createCreateForm($entity);

        return $this->render('OjsUserBundle:UserJournalRole:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a UserJournalRole entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OjsUserBundle:UserJournalRole')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find UserJournalRole entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('OjsUserBundle:UserJournalRole:show.html.twig', array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),));
    }

    /**
     * Creates a form to delete a UserJournalRole entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('ujr_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm();
    }

    /**
     * Finds and displays a Users of a Journal with roles  (ungrouped).
     * @param int $journal_id
     */
    public function showUsersOfJournalAction($journal_id)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('ujr')
            ->from('OjsUserBundle:UserJournalRole', 'ujr')
            ->where(
                $qb->expr()->andX(
                    $qb->expr()->eq('ujr.journalId', ':jid')
                )
            )
            ->setParameter('jid', $journal_id);
        $entities = $qb->getQuery()->getResult();
        return $this->render('OjsUserBundle:UserJournalRole:show_users.html.twig', array(
            'entities' => $entities
        ));
    }

    public function myJournalsAction()
    {
        $user_id = $this->getUser()->getId();

        return $this->showJournalsOfUserAction($user_id, 'show_my_journals.html.twig');
    }

    /**
     * Finds and displays a Journals of a user with roles.
     * @param mixed $journal_id
     */
    public function showJournalsOfUserAction($user_id, $tpl = 'show_journals.html.twig')
    {
        $em = $this->getDoctrine()->getManager();
        $data = $em->createQuery(
            'SELECT  u  FROM OjsUserBundle:UserJournalRole u WHERE u.userId = :user_id '
        )->setParameter('user_id', $user_id)->getResult();
        $entities = array();
        if ($data) {
            foreach ($data as $item) {
                $entities[$item->getJournalId()]['journal'] = $item->getJournal();
                $entities[$item->getJournalId()]['roles'][] = $item->getRole();
            }
        }

        return $this->render('OjsUserBundle:UserJournalRole:' . $tpl, array(
            'entities' => $entities
        ));
    }

    /**
     * Displays a form to edit an existing UserJournalRole entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OjsUserBundle:UserJournalRole')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find UserJournalRole entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('OjsUserBundle:UserJournalRole:edit.html.twig', array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a UserJournalRole entity.
     *
     * @param UserJournalRole $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(UserJournalRole $entity)
    {
        $form = $this->createForm(new UserJournalRoleType(), $entity, array(
            'action' => $this->generateUrl('ujr_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'user' => $this->getUser()
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing UserJournalRole entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OjsUserBundle:UserJournalRole')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find UserJournalRole entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('ujr_edit', array('id' => $id)));
        }

        return $this->render('OjsUserBundle:UserJournalRole:edit.html.twig', array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a UserJournalRole entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('OjsUserBundle:UserJournalRole')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find UserJournalRole entity.');
        }
        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('ujr'));
    }

    public function sendAction(Request $request, UserJournalRole $user_journal_role)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $data = [];
        $session = $this->get('session');
        if ($request->isMethod('POST')) {
            $mailData = $request->get('mail');
            $mailer = $this->get('mailer');
            $message = $mailer->createMessage()
                ->setFrom($this->container->getParameter('system_email'))
                ->setTo($user_journal_role->getUser()->getEmail())
                ->setSubject($mailData['subject'])
                ->setBody($mailData['body'])
                ->setContentType('text/html');
            $mailer->send($message);
            $session->getFlashBag()->add('success', $this->get('translator')->trans('Email sending succefully.'));
            $session->save();
            return $this->redirect($this->get('router')->generate('ujr_show_users_ofjournal', ['journal_id' => $user_journal_role->getJournalId()]));

        }
        $qb = $em->createQueryBuilder();
        $qb->select('t')
            ->from('OjsJournalBundle:MailTemplate', 't')
            ->where(
                $qb->expr()->orX(
                    $qb->expr()->isNull('t.journalId'),
                    $qb->expr()->eq('t.journalId', ':journal')
                )
            )
            ->setParameter('journal', $user_journal_role->getJournalId());
        $templates = $qb->getQuery()->getResult();
        $data['templates'] = $templates;
        $data['user'] = $user_journal_role->getUser();
        return $this->render('OjsUserBundle:UserJournalRole:send_mail.html.twig', $data);
    }
}
