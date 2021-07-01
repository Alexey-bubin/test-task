<?php

namespace App\Controller;

use App\Entity\Order;
use App\Services\OrdersXlsExportService;
use App\Services\OrdersXlsImport;
use App\Services\OrdersXlsImportService;
use Doctrine\ORM\Tools\Pagination\Paginator;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints\Required;

class OrdersController extends AbstractController
{
    protected $pageSize = 100;

    /**
     * @OA\Get(
     * path="/api/orders",
     * description="Get paginated orders",
     * operationId="orders.list",
     * @OA\Response(
     *     response=200,
     *     description="Return list of paginated orders",
     *     @OA\JsonContent(
     *          @OA\Property(property="data", type="array", @OA\Items(
     *              @OA\Property(property="id", type="integer"),
     *              @OA\Property(property="date", type="string"),
     *              @OA\Property(property="partner", type="string"),
     *              @OA\Property(property="comision", type="number", format="float"),
     *              @OA\Property(property="payment_type", type="string"),
     *              @OA\Property(property="user", type="string"),
     *              @OA\Property(property="status", type="integer", description="status code"),
     *              @OA\Property(property="status_name", type="integer", description="translated status name"),
     *              @OA\Property(property="count", type="integer"),
     *              @OA\Property(property="product", type="string"),
     *              @OA\Property(property="price", type="number", format="float"),
     *              @OA\Property(property="sku", type="string"),
     *          )),
     *          @OA\Property(property="total", type="integer"),
     *          @OA\Property(property="pages", type="integer"),
     *          @OA\Property(property="page", type="integer"),
     *          @OA\Property(property="limit", type="integer"),
     *     )
     *   )
     * )
     * @param Request             $request
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function list(Request $request, TranslatorInterface $translator): Response
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
                'status_name'  => $pageItem->getStatusString($translator),
                'count'        => (int) $pageItem->getCount(),
                'product'      => $pageItem->getProductName(),
                'price'        => (float) $pageItem->getPrice(),
                'sku'          => $pageItem->getSku(),
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

    /**
     * @OA\Get(
     * path="/api/orders/export",
     * description="export orders to server file",
     * operationId="orders.export",
     * @OA\Response(
     *     response=200,
     *     description="Return list of paginated orders",
     *     @OA\JsonContent(
     *          @OA\Property(property="result", type="string", example="success")
     *     )
     *   )
     * )
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function export(TranslatorInterface $translator): Response
    {
        $path             = $this->getParameter('kernel.project_dir') . '/public';
        $em               = $this->getDoctrine()->getManager();
        $ordersRepository = $em->getRepository(Order::class);

        $exportService = new OrdersXlsExportService($ordersRepository, $translator);
        $exportService->saveToFile($path, 'orders.xls');

        $response = new Response();
        $response->setContent(json_encode([
            'result' => 'success'
        ]));

        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    public function import(Request $request, TranslatorInterface $translator) {
        $validator = Validation::createValidator();
        $uploadedFile = $request->files->get('file');
        $allowedMimeTypes = [
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-excel'
        ];

        $violations = $validator->validate(
            $uploadedFile, [
                new File(['maxSize' => '10m', 'mimeTypes' => $allowedMimeTypes]),
                new NotNull(['message' => 'Please upload an file']),
            ]
        );

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        if (0 !== count($violations)) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[] = $violation->getMessage();
            }

            $response->setContent(json_encode(['success' => 'error', 'errors' => $errors]));
            return $response;
        }

        $importService = new OrdersXlsImportService($uploadedFile, $this->getDoctrine(), $translator);
        $importService->importFromUpload();

        $response->setContent(json_encode(['success' => 'success', 'errors' => []]));

        return $response;
    }
}