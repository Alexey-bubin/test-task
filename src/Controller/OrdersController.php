<?php

namespace App\Controller;

use App\Entity\Order;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrdersController extends AbstractController
{
    /**
     * @Route("/orders", name="orders")
     */
    public function list(Request $request)
    {
        $em     = $this->getDoctrine()->getManager();
        $page   = $request->query->get('page', 1);
        $orders = $em->getRepository(Order::class);

        $query = $orders->createQueryBuilder('o')
            ->getQuery();

        $pageSize = 100;

        $paginator = new Paginator($query);
        $totalItems = count($paginator);
        $pagesCount = ceil($totalItems / $pageSize);
        $paginator
            ->getQuery()
            ->setFirstResult($pageSize * ($page-1)) // set the offset
            ->setMaxResults($pageSize);

        $items = [];
        foreach ($paginator->getQuery()->getResult() as $pageItem) {
            $items[] = [
                'id'           => (int) $pageItem->getId(),
                'date'         => $pageItem->getDate()->format('Y-m-d H:i:s'),
                'partner'      => $pageItem->getPartner()->getName(),
                'comision'     => (float) $pageItem->getComision(),
                'payment_type' => $pageItem->getPaymentType()->getName(),
                'user'         => $pageItem->getUser()->getName(),
                'status'       => $pageItem->getStatus(),
            ];
        }

        $response = new Response();
        $response->setContent(json_encode([
              'data'  => $items,
              'total' => $totalItems,
              'pages' => $pagesCount,
              'page'  => $page,
              'limit' => $pageSize,
        ]));

        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}