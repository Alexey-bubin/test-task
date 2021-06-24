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
    protected $pageSize = 100;

    /**
     * @Route("/orders", name="orders")
     */
    public function list(Request $request): Response
    {
        $em     = $this->getDoctrine()->getManager();
        $page   = $request->query->get('page', 1);
        $orders = $em->getRepository(Order::class);

        $query = $orders->createQueryBuilder('o')
            ->getQuery();

        $paginator = new Paginator($query);
        $totalItems = count($paginator);
        $pagesCount = ceil($totalItems / $this->pageSize);
        $paginator
            ->getQuery()
            ->setFirstResult($this->pageSize * ($page-1))
            ->setMaxResults($this->pageSize);

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
              'limit' => $this->pageSize,
        ]));

        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}