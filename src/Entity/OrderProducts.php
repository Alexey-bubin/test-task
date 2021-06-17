<?php

namespace App\Entity;

use App\Repository\OrderProductsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrderProductsRepository::class)
 */
class OrderProducts
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Order::class, inversedBy="product", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $order_number;

    /**
     * @ORM\ManyToOne(targetEntity=Product::class)
     */
    private $products;

    /**
     * @ORM\Column(type="integer")
     */
    private $count;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderNumber(): ?Order
    {
        return $this->order_number;
    }

    public function setOrderNumber(Order $order_number): self
    {
        $this->order_number = $order_number;

        return $this;
    }

    public function getProducts(): ?Product
    {
        return $this->products;
    }

    public function getCount(): ?int
    {
        return $this->count;
    }

    public function setCount(int $count): self
    {
        $this->count = $count;

        return $this;
    }
}
