<?php

namespace App\Entity;

use App\Entity\Traits\TimestampableTrait;
use App\Repository\CommandeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?bool $validated = false;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $reference = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $address = null;

    #[ORM\Column(nullable: true)]
    private ?int $amount = 0;

    #[ORM\Column(nullable: true)]
    private ?int $amountTotal = 0;

    #[ORM\Column(nullable: true)]
    private ?int $taxeAmount = null;

    #[ORM\Column(nullable: true)]
    private ?int $discountAmount = null;

    #[ORM\OneToOne(mappedBy: 'commande', cascade: ['persist', 'remove'])]
    private ?Payment $payment = null;

    #[ORM\ManyToOne(inversedBy: 'commandes')]
    private ?Discount $discount = null;

    #[ORM\ManyToOne(inversedBy: 'commandes')]
    private ?User $owner = null;

    #[ORM\OneToOne(inversedBy: 'commande', cascade: ['persist', 'remove'])]
    private ?Booking $booking = null;

    #[ORM\ManyToOne(inversedBy: 'commandes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Hostel $hostel = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isValidated(): ?bool
    {
        return $this->validated;
    }

    public function setValidated(?bool $validated): self
    {
        $this->validated = $validated;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(?string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(?int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getAmountTotal(): ?int
    {
        return $this->amountTotal;
    }

    public function setAmountTotal(?int $amountTotal): self
    {
        $this->amountTotal = $amountTotal;

        return $this;
    }

    public function getTaxeAmount(): ?int
    {
        return $this->taxeAmount;
    }

    public function setTaxeAmount(?int $taxeAmount): self
    {
        $this->taxeAmount = $taxeAmount;

        return $this;
    }

    public function getDiscountAmount(): ?int
    {
        return $this->discountAmount;
    }

    public function setDiscountAmount(?int $discountAmount): self
    {
        $this->discountAmount = $discountAmount;

        return $this;
    }

    public function getPayment(): ?Payment
    {
        return $this->payment;
    }

    public function setPayment(Payment $payment): self
    {
        // set the owning side of the relation if necessary
        if ($payment->getCommande() !== $this) {
            $payment->setCommande($this);
        }

        $this->payment = $payment;

        return $this;
    }

    public function getDiscount(): ?Discount
    {
        return $this->discount;
    }

    public function setDiscount(?Discount $discount): self
    {
        $this->discount = $discount;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getBooking(): ?Booking
    {
        return $this->booking;
    }

    public function setBooking(?Booking $booking): self
    {
        $this->booking = $booking;

        return $this;
    }

    public function getHostel(): ?Hostel
    {
        return $this->hostel;
    }

    public function setHostel(?Hostel $hostel): self
    {
        $this->hostel = $hostel;

        return $this;
    }
}
