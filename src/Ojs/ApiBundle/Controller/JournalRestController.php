<?php

namespace Ojs\ApiBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Get;

class JournalRestController extends FOSRestController
{
    /**
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Get Journal Issues"
     * )
     * @Get("/journal/{id}/issues")
     */
    public function getJournalIssues($id)
    {
        return $id;
    }
    /**
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Get Specific Journal"
     * )
     * @Get("/journal/{id}")
     */
    public function getJournalAction($id)
    {
        $journal = $this->getDoctrine()->getRepository('OjsJournalBundle:Journal')->find($id);
        if (!is_object($journal)) {
            throw new HttpException(404, 'Not found. The record is not found or route is not defined.');
        }

        return $journal;
    }

    /**
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Get Specific Journal Of Users Action",
     *  parameters={
     *      {
     *          "name"="page",
     *          "dataType"="integer",
     *          "required"="true",
     *          "description"="offset page"
     *      },
     *      {
     *          "name"="limit",
     *          "dataType"="integer",
     *          "required"="true",
     *          "description"="limit"
     *      }
     *  }
     * )
     * @Get("/journal/{id}/users")
     */
    public function getJournalUsersAction(Request $request,$id)
    {
        $limit = $request->get('limit');
        $page = $request->get('page');
        if (empty($limit)) {
            throw new HttpException(400, 'Missing parameter : limit');
        }
        if (empty($page)) {
            throw new HttpException(400, 'Missing parameter : page');
        }
        return true; //@todo fix it
        $users = $this
            ->getDoctrine()
            ->getRepository('OjsUserBundle:UserJournalRole')
                ->createQueryBuilder('j')
                ->where('j.journal_id > :id')
                ->setParameter('id', $id)
                //->orderBy('id', 'ASC')
                ->setMaxResults($limit)
                ->setFirstResult($page)
                ->getQuery()
                ->getResults();

        return $users;
    }

}
