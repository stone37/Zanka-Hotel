<?php

namespace App\Entity;

use App\Entity\Traits\EnabledTrait;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\ReviewRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReviewRepository::class)]
class Review
{
    use TimestampableTrait;
    use EnabledTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 180)]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $firstname = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 180)]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lastname = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 180)]
    #[Assert\Email]
    #[ORM\Column(length: 180, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(length: 180, nullable: true)]
    private ?string $bookingNumber = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 180)]
    #[ORM\Column(length: 180, nullable: true)]
    private ?string $title = null;

    #[Assert\NotBlank]
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $comment = null;

    #[Assert\NotBlank]
    #[ORM\Column(nullable: true)]
    private ?int $personalRating = null;

    #[Assert\NotBlank]
    #[ORM\Column(nullable: true)]
    private ?int $equipmentRating = null;

    #[Assert\NotBlank]
    #[ORM\Column(nullable: true)]
    private ?int $propertyRating = null;

    #[Assert\NotBlank]
    #[ORM\Column(nullable: true)]
    private ?int $comfortRating = null;

    #[Assert\NotBlank]
    #[ORM\Column(nullable: true)]
    private ?int $priceRating = null;

    #[Assert\NotBlank]
    #[ORM\Column(nullable: true)]
    private ?int $locationRating = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ip = null;

    #[ORM\ManyToOne(inversedBy: 'reviews')]
    private ?User $owner = null;

    #[ORM\ManyToOne(inversedBy: 'reviews')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Hostel $hostel = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getBookingNumber(): ?string
    {
        return $this->bookingNumber;
    }

    public function setBookingNumber(?string $bookingNumber): self
    {
        $this->bookingNumber = $bookingNumber;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getPersonalRating(): ?int
    {
        return $this->personalRating;
    }

    public function setPersonalRating(?int $personalRating): self
    {
        $this->personalRating = $personalRating;

        return $this;
    }

    public function getEquipmentRating(): ?int
    {
        return $this->equipmentRating;
    }

    public function setEquipmentRating(?int $equipmentRating): self
    {
        $this->equipmentRating = $equipmentRating;

        return $this;
    }

    public function getPropertyRating(): ?int
    {
        return $this->propertyRating;
    }

    public function setPropertyRating(?int $propertyRating): self
    {
        $this->propertyRating = $propertyRating;

        return $this;
    }

    public function getComfortRating(): ?int
    {
        return $this->comfortRating;
    }

    public function setComfortRating(?int $comfortRating): self
    {
        $this->comfortRating = $comfortRating;

        return $this;
    }

    public function getPriceRating(): ?int
    {
        return $this->priceRating;
    }

    public function setPriceRating(?int $priceRating): self
    {
        $this->priceRating = $priceRating;

        return $this;
    }

    public function getLocationRating(): ?int
    {
        return $this->locationRating;
    }

    public function setLocationRating(?int $locationRating): self
    {
        $this->locationRating = $locationRating;

        return $this;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(?string $ip): self
    {
        $this->ip = $ip;

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
