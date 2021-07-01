<?php

namespace App\Services;

use App\Entity\Order;
use App\Entity\Partner;
use App\Entity\PaymentTypes;
use App\Entity\User;
use App\Entity\Vendor;
use App\Enum\OrderStatuses;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class OrdersXlsImportService
{
    protected $file;

    protected $doctrine;

    protected $translator;

    /**
     * OrdersXlsImportService constructor.
     * @param $file
     * @param $doctrine
     * @param $translator
     */
    public function __construct($file, $doctrine, $translator)
    {
        $this->file = $file;
        $this->doctrine = $doctrine;
        $this->translator = $translator;
    }

    public function importFromUpload():bool
    {
        $reader = new Xlsx();
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($this->file->getPathName());
        $worksheet = $spreadsheet->getActiveSheet();

        $index = 1;
        foreach ($worksheet->getRowIterator() as $row) {
            if ($index === 1) {
                $index++;
                continue;
            }

            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(FALSE);
            $row = [];
            foreach ($cellIterator as $cell) {
                $row[] = $cell->getValue();
            }

            $partner     = $this->importPartner($row);
            $vendor      = $this->importVendor($row);
            $paymentType = $this->importPaymentType($row);
            $user        = $this->importUser($row);
            $this->importOrder($row, $partner, $vendor, $paymentType, $user);
        }

        return true;
    }

    /**
     * @param $row
     * @param $partner
     * @param $vendor
     * @param $paymentType
     * @param $user
     * @return Order
     */
    private function importOrder($row, $partner, $vendor, $paymentType, $user):Order
    {
        $entityManager = $this->doctrine->getManager();
        $order = $this->doctrine
            ->getRepository(Order::class)
            ->findOneBy(['id' => (int) $row[3]]);

        if (is_null($order)) {
            $order = new Order();
            $order->setId($row[3]);
        } else {
            return $order;
        }

        $date      = \DateTime::createFromFormat('Y-m-d H:i:s', $row[0]);
        $count     = $row[9] ?? 1;
        $status    = $this->getStatusFromStatusName($row[14]);
        $price     = $row[7] ?? 0.00;
        $commision = $row[8] ?? 0.00;

        $order->setProductName($row[6])
            ->setDate($date)
            ->setPartner($partner)
            ->setVendor($vendor)
            ->setPaymentType($paymentType)
            ->setUser($user)
            ->setSku($row[4])
            ->setPrice($price)
            ->setComision($commision)
            ->setCount($count)
            ->setStatus($status);

        $entityManager->persist($order);
        $entityManager->flush();

        return $order;
    }

    private function getStatusFromStatusName($statusName):int
    {
        foreach (OrderStatuses::getAllowedStatuses() as $status) {
            if ($this->translator->trans('order.status.' . $status) === $statusName) {
                return $status;
            }
        }

        return 0;
    }

    /**
     * @param $row
     * @return Partner
     */
    private function importPartner($row): Partner
    {
        $entityManager = $this->doctrine->getManager();
        $partner = $this->doctrine
            ->getRepository(Partner::class)
            ->findOneBy(['id' => (int) $row[1]]);

        if (is_null($partner)) {
            $partner = new Partner();
        } else {
            return $partner;
        }

        $partner->setName($row[2]);

        $entityManager->persist($partner);
        $entityManager->flush();

        return $partner;
    }

    /**
     * @param $row
     * @return Vendor
     */
    private function importVendor($row):Vendor
    {
        $entityManager = $this->doctrine->getManager();

        $vendor = $this->doctrine
            ->getRepository(Vendor::class)
            ->findOneBy(['name' => $row[5]]);

        if (is_null($vendor)) {
            $vendor = new Vendor();
            $vendor->setName($row[5]);

            $entityManager->persist($vendor);
            $entityManager->flush();
        }

        return $vendor;
    }

    /**
     * @param $row
     * @return PaymentTypes
     */
    private function importPaymentType($row):PaymentTypes
    {
        $entityManager = $this->doctrine->getManager();

        $paymentType = $this->doctrine
            ->getRepository(PaymentTypes::class)
            ->findOneBy(['name' => $row[10]]);

        if (is_null($paymentType)) {
            $paymentType = new PaymentTypes();
            $paymentType->setName($row[10]);

            $entityManager->persist($paymentType);
            $entityManager->flush();
        }

        return $paymentType;
    }

    /**
     * @param $row
     * @return User
     */
    private function importUser($row):User
    {
        $entityManager = $this->doctrine->getManager();

        $user = $this->doctrine
            ->getRepository(User::class)
            ->findOneBy(['name' => $row[11]]);

        if (is_null($user)) {
            $user = new User();
            $user->setName($row[11]);
        } else {
            return $user;
        }

        $user->setPhone($row[12]);
        $user->setEmail($row[13]);

        $entityManager->persist($user);
        $entityManager->flush();

        return $user;
    }
}