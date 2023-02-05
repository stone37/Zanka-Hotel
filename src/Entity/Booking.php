<?php

namespace App\Entity;

use ApiPlatform\Action\NotFoundAction;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Api\Controller\UserCancelledBooking;
use App\Api\Controller\UserConfirmedBooking;
use App\Api\Controller\UserNewBooking;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\BookingRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: BookingRepository::class)]
#[GetCollection(
    uriTemplate: '/users/{id}/bookings/new',
    controller: UserNewBooking::class,
    openapiContext: [
        'summary' => 'Récupère tous les nouvelles réservations d\'un utilisateur',
        'security' => [['bearerAuth' => []]]
    ],
    paginationItemsPerPage: 10,
    paginationMaximumItemsPerPage: 20,
    paginationClientItemsPerPage: true,
    normalizationContext: ['groups' => ['booking:read', 'skip_null_values' => false]],
    security: 'is_granted("ROLE_USER")'
)]
#[GetCollection(
    uriTemplate: '/users/{id}/bookings/confirmed',
    controller: UserConfirmedBooking::class,
    openapiContext: [
        'summary' => 'Récupère tous les réservations confirmés d\'un utilisateur',
        'security' => [['bearerAuth' => []]]
    ],
    paginationItemsPerPage: 10,
    paginationMaximumItemsPerPage: 20,
    paginationClientItemsPerPage: true,
    normalizationContext: ['groups' => ['booking:read', 'skip_null_values' => false]],
    security: 'is_granted("ROLE_USER")'
)]
#[GetCollection(
    uriTemplate: '/users/{id}/bookings/cancelled',
    controller: UserCancelledBooking::class,
    openapiContext: [
        'summary' => 'Récupère tous les réservations refusés d\'un utilisateur',
        'security' => [['bearerAuth' => []]]
    ],
    paginationItemsPerPage: 10,
    paginationMaximumItemsPerPage: 20,
    paginationClientItemsPerPage: true,
    normalizationContext: ['groups' => ['booking:read', 'skip_null_values' => false]],
    security: 'is_granted("ROLE_USER")'
)]
#[Get(
    controller: NotFoundAction::class,
    output: false,
    read: false,
    openapiContext: ['summary' => 'hidden']
)]
class Booking
{
    const NEW = 'new';
    const CONFIRMED = 'confirm';
    const CANCELLED = 'cancel';
    const OLD = 'old';

    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['booking:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['booking:read'])]
    private ?string $firstname = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['booking:read'])]
    private ?string $lastname = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['booking:read'])]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['booking:read'])]
    private ?string $phone = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['booking:read'])]
    private ?DateTimeInterface $checkin = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['booking:read'])]
    private ?DateTimeInterface $checkout = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['booking:read'])]
    private ?int $days = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['booking:read'])]
    private ?int $roomNumber = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['booking:read'])]
    private ?string $ip = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['booking:read'])]
    private ?string $message = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['booking:read'])]
    private ?int $adult = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['booking:read'])]
    private ?int $children = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['booking:read'])]
    private ?string $reference = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['booking:read'])]
    private ?string $country = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['booking:read'])]
    private ?string $city = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['booking:read'])]
    private ?DateTimeInterface $confirmedAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['booking:read'])]
    private ?DateTimeInterface $cancelledAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['booking:read'])]
    private ?string $status = Booking::NEW;

    #[ORM\Column(nullable: true)]
    #[Groups(['booking:read'])]
    private ?int $amount = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['booking:read'])]
    private ?int $taxeAmount = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['booking:read'])]
    private ?int $discountAmount = null;

    #[ORM\ManyToOne(inversedBy: 'bookings')]
    private ?User $owner = null;

    #[ORM\ManyToOne(inversedBy: 'bookings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Hostel $hostel = null;

    #[ORM\ManyToOne(inversedBy: 'bookings')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['booking:read'])]
    private ?Room $room = null;

    #[ORM\OneToMany(mappedBy: 'booking', targetEntity: Occupant::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
    #[Groups(['booking:read'])]
    private Collection $occupants;

    #[ORM\OneToOne(mappedBy: 'booking', cascade: ['persist', 'remove'])]
    private ?Commande $commande = null;

    #[ORM\ManyToOne]
    #[Groups(['booking:read'])]
    private ?Cancelation $cancelation = null;

    public function __construct()
    {
        $this->occupants = new ArrayCollection();
    }

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

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getCheckin(): ?DateTimeInterface
    {
        return $this->checkin;
    }

    public function setCheckin(?DateTimeInterface $checkin): self
    {
        $this->checkin = $checkin;

        return $this;
    }

    public function getCheckout(): ?DateTimeInterface
    {
        return $this->checkout;
    }

    public function setCheckout(?DateTimeInterface $checkout): self
    {
        $this->checkout = $checkout;

        return $this;
    }

    public function getDays(): ?int
    {
        return $this->days;
    }

    public function setDays(?int $days): self
    {
        $this->days = $days;

        return $this;
    }

    public function getRoomNumber(): ?int
    {
        return $this->roomNumber;
    }

    public function setRoomNumber(?int $roomNumber): self
    {
        $this->roomNumber = $roomNumber;

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

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getAdult(): ?int
    {
        return $this->adult;
    }

    public function setAdult(?int $adult): self
    {
        $this->adult = $adult;

        return $this;
    }

    public function getChildren(): ?int
    {
        return $this->children;
    }

    public function setChildren(?int $children): self
    {
        $this->children = $children;

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

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getConfirmedAt(): ?DateTimeInterface
    {
        return $this->confirmedAt;
    }

    public function setConfirmedAt(?DateTimeInterface $confirmedAt): self
    {
        $this->confirmedAt = $confirmedAt;

        return $this;
    }

    public function getCancelledAt(): ?DateTimeInterface
    {
        return $this->cancelledAt;
    }

    public function setCancelledAt(?DateTimeInterface $cancelledAt): self
    {
        $this->cancelledAt = $cancelledAt;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

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

    public function getRoom(): ?Room
    {
        return $this->room;
    }

    public function setRoom(?Room $room): self
    {
        $this->room = $room;

        return $this;
    }

    /**
     * @return Collection<int, Occupant>
     */
    public function getOccupants(): Collection
    {
        return $this->occupants;
    }

    public function addOccupant(Occupant $occupant): self
    {
        if (!$this->occupants->contains($occupant)) {
            $this->occupants[] = $occupant;
            $occupant->setBooking($this);
        }

        return $this;
    }

    public function removeOccupant(Occupant $occupant): self
    {
        if ($this->occupants->removeElement($occupant)) {
            // set the owning side to null (unless already changed)
            if ($occupant->getBooking() === $this) {
                $occupant->setBooking(null);
            }
        }

        return $this;
    }

    public function getCommande(): ?Commande
    {
        return $this->commande;
    }

    public function setCommande(?Commande $commande): self
    {
        // unset the owning side of the relation if necessary
        if ($commande === null && $this->commande !== null) {
            $this->commande->setBooking(null);
        }

        // set the owning side of the relation if necessary
        if ($commande !== null && $commande->getBooking() !== $this) {
            $commande->setBooking($this);
        }

        $this->commande = $commande;

        return $this;
    }

    public function getCancelation(): ?Cancelation
    {
        return $this->cancelation;
    }

    public function setCancelation(?Cancelation $cancelation): self
    {
        $this->cancelation = $cancelation;

        return $this;
    }
}
