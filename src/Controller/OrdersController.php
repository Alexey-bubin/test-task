<?php

namespace App\Controller;

use App\Entity\Order;
use App\Repository\OrderRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class OrdersController extends AbstractController
{
    /**
     * @Route("/orders", name="orders")
     */
    public function list(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $orderRepository = $em->getRepository(Order::class);
        $page = $request->query->get('page', 1);
        $pageSize = '10';

        $query = $orderRepository->createQueryBuilder('u')
            ->orderBy('d.id', 'DESC')
            ->getQuery();

        $paginator = new Paginator($query);
        $totalItems = count($paginator);
        $pagesCount = ceil($totalItems / $pageSize);

        $paginator
            ->getQuery()
            ->setFirstResult($pageSize * ($page-1))
            ->setMaxResults($pageSize);


        //$orders = $orderRepository->findAll();
        $response = new Response();
        $response->setContent(json_encode([
            'data' => $paginator,
            'total' => $totalItems,
            'pages' => $pagesCount,
            'page' => $page,
            'limit' => $pageSize,
        ]));

        $response->headers->set('Content-Type', 'application/json');

        /*
        $page = $request->query->get('page', 1);
        $qb = $this->getDoctrine()
            ->getRepository('AppBundle:Programmer')
            ->findAllQueryBuilder();

        $adapter = new DoctrineORMAdapter($qb);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(10);
        $pagerfanta->setCurrentPage($page);
        $programmers = [];
        foreach ($pagerfanta->getCurrentPageResults() as $result) {
            $programmers[] = $result;
        }
        $response = $this->createApiResponse([
                                                 'total' => $pagerfanta->getNbResults(),
                                                 'count' => count($programmers),
                                                 'programmers' => $programmers,
                                             ], 200);
        */
        return ;
    }
}