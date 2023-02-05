<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Entity\Traits\EnabledTrait;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\ReviewRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReviewRepository::class)]
#[GetCollection]
#[Post]
#[Get]
class Review
{
    use TimestampableTrait;
    use EnabledTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['hostel:read'])]
    private ?int $id = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 180)]
    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['hostel:read'])]
    private ?string $firstname = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 180)]
    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['hostel:read'])]
    private ?string $lastname = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 180)]
    #[Assert\Email]
    #[ORM\Column(length: 180, nullable: true)]
    #[Groups(['hostel:read'])]
    private ?string $email = null;

    #[ORM\Column(length: 180, nullable: true)]
    #[Groups(['hostel:read'])]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['hostel:read'])]
    private ?string $comment = null;

    #[ORM\Column(type: Types::FLOAT, nullable: true)]
    #[Groups(['hostel:read'])]
    private ?float $rating = 0;

    #[Assert\NotBlank]
    #[Assert\Range(min: 0, max: 10)]
    #[ORM\Column(type: Types::FLOAT, nullable: true)]
    #[Groups(['hostel:read'])]
    private ?float $personalRating = 0;

    #[Assert\NotBlank]
    #[Assert\Range(min: 0, max: 10)]
    #[ORM\Column(type: Types::FLOAT, nullable: true)]
    #[Groups(['hostel:read'])]
    private ?float $equipmentRating = 0;

    #[Assert\NotBlank]
    #[Assert\Range(min: 0, max: 10)]
    #[ORM\Column(type: Types::FLOAT, nullable: true)]
    #[Groups(['hostel:read'])]
    private ?float $propertyRating = 0;

    #[Assert\NotBlank]
    #[Assert\Range(min: 0, max: 10)]
    #[ORM\Column(type: Types::FLOAT, nullable: true)]
    #[Groups(['hostel:read'])]
    private ?float $comfortRating = 0;

    #[Assert\NotBlank]
    #[Assert\Range(min: 0, max: 10)]
    #[ORM\Column(type: Types::FLOAT, nullable: true)]
    #[Groups(['hostel:read'])]
    private ?float $priceRating = 0;

    #[Assert\NotBlank]
    #[Assert\Range(min: 0, max: 10)]
    #[ORM\Column(type: Types::FLOAT, nullable: true)]
    #[Groups(['hostel:read'])]
    private ?float $locationRating = 0;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['hostel:read'])]
    private ?string $ip = null;

    #[ORM\ManyToOne(inversedBy: 'reviews')]
    private ?User $owner = null;

    #[ORM\ManyToOne(inversedBy: 'reviews')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Hostel $hostel = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Booking $booking = null;

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

    public function getRating(): ?float
    {
        return $this->rating;
    }

    public function setRating(?float $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    public function getPersonalRating(): ?float
    {
        return $this->personalRating;
    }

    public function setPersonalRating(?float $personalRating): self
    {
        $this->personalRating = $personalRating;

        return $this;
    }

    public function getEquipmentRating(): ?float
    {
        return $this->equipmentRating;
    }

    public function setEquipmentRating(?float $equipmentRating): self
    {
        $this->equipmentRating = $equipmentRating;

        return $this;
    }

    public function getPropertyRating(): ?float
    {
        return $this->propertyRating;
    }

    public function setPropertyRating(?float $propertyRating): self
    {
        $this->propertyRating = $propertyRating;

        return $this;
    }

    public function getComfortRating(): ?float
    {
        return $this->comfortRating;
    }

    public function setComfortRating(?float $comfortRating): self
    {
        $this->comfortRating = $comfortRating;

        return $this;
    }

    public function getPriceRating(): ?float
    {
        return $this->priceRating;
    }

    public function setPriceRating(?float $priceRating): self
    {
        $this->priceRating = $priceRating;

        return $this;
    }

    public function getLocationRating(): ?float
    {
        return $this->locationRating;
    }

    public function setLocationRating(?float $locationRating): self
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

    /**
     * @param User|UserInterface|null $owner
     * @return $this
     */
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

    public function getBooking(): ?Booking
    {
        return $this->booking;
    }

    public function setBooking(?Booking $booking): self
    {
        $this->booking = $booking;

        return $this;
    }
}
