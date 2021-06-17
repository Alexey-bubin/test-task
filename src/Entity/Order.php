<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 * @ORM\Table(name="`order`")
 */
class Order
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity=Partner::class, cascade={"persist", "remove"})
     */
    private $partner;

    /**
     * @ORM\Column(type="float", scale=2, precision=10)
     */
    private $comision;

    /**
     * @ORM\ManyToOne(targetEntity=PaymentTypes::class, cascade={"persist", "remove"})
     */
    private $payment_type;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, cascade={"persist", "remove"})
     */
    private $user;

    /**
     * @ORM\Column(type="integer")
     */
    private $status;

    /**
     * @ORM\ManyToMany (targetEntity=OrderProducts::class, cascade={"persist", "remove"})
     */
    private $product;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getPartner(): ?Partner
    {
        return $this->partner;
    }

    public function setPartner(?Partner $partner): self
    {
        $this->partner = $partner;

        return $this;
    }

    public function getComision(): ?float
    {
        return $this->comision;
    }

    public function setComision(float $comision): self
    {
        $this->comision = $comision;

        return $this;
    }

    public function getPaymentType(): ?PaymentTypes
    {
        return $this->payment_type;
    }

    public function setPaymentType(?PaymentTypes $payment_type): self
    {
        $this->payment_type = $payment_type;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getProduct(): ?OrderProducts
    {
        return $this->product;
    }

    public function setProduct(OrderProducts $product): self
    {
        // set the owning side of the relation if necessary
        if ($product->getOrderNumber() !== $this) {
            $product->setOrderNumber($this);
        }

        $this->product = $product;

        return $this;
    }
}
