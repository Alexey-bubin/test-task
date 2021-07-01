<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class OrdersXlsExportService
{
    protected $orderRepository;

    protected $translator;

    public function __construct($orderRepository, $translator)
    {
        $this->orderRepository = $orderRepository;

        $this->translator = $translator;
    }

    public function saveToFile($path, $fileName):bool
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $orders = $this->orderRepository->findAll();
        $headers = [
            'Дата заказа', 'Код партнера', 'Имя партнера', '№ заказа', 'SKU', 'Производитель товара', 'Название товара',
            'Цена', 'Комиссия', 'Количество', 'Тип оплаты', 'ФИО', 'Номер телефона', 'Электронная почта (email)',
            'Статус заказа'
        ];
        $sheet->fromArray($headers);
        $rowNumber = 2;
        foreach ($orders as $order) {
            $row = [
                $order->getDate(),
                $order->getPartner()->getId(),
                $order->getPartner()->getName(),
                $order->getId(),
                $order->getSku(),
                $order->getVendor()->getName(),
                $order->getProductName(),
                $order->getPrice(),
                $order->getComision(),
                $order->getCount(),
                $order->getPaymentType()->getName(),
                $order->getUser()->getName(),
                $order->getUser()->getPhone(),
                $order->getUser()->getEmail(),
                $order->getStatusString($this->translator),
            ];
            $sheet->fromArray($row, NULL, 'A'. $rowNumber);
            $rowNumber++;
        }

        $sheet->setTitle("Orders");

        foreach(range('A','O') as $columnID) {
            $sheet->getColumnDimension($columnID)
                ->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $excelFilepath =  $path . '/orders.xlsx';
        $writer->save($excelFilepath);

        return true;
    }
}
