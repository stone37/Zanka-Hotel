<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Api\Filter\InFilter;
use App\Entity\Traits\ClosedTrait;
use App\Entity\Traits\DeletableTrait;
use App\Entity\Traits\EnabledTrait;
use App\Entity\Traits\MediaTrait;
use App\Entity\Traits\Notifiable;
use App\Entity\Traits\PositionTrait;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\HostelRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

#[Vich\Uploadable]
#[ORM\Entity(repositoryClass: HostelRepository::class)]
#[GetCollection(
    openapiContext: ['summary' => 'Récupère tous les hotels'],
    paginationItemsPerPage: 25,
    paginationMaximumItemsPerPage: 25,
    paginationClientItemsPerPage: true,
    normalizationContext: ['groups' => ['hostel:read', 'skip_null_values' => false]],
)]
#[Get(
    openapiContext: ['summary' => 'Récupère un hotel'],
    normalizationContext: ['groups' => ['hostel:read', 'skip_null_values' => false]]
)]
#[ApiFilter(
    SearchFilter::class,
    properties: [
        'location.city' => 'exact',
        'category' => 'exact',
        'averageRating' => 'exact',
        'name' => 'partial',
    ]
)]
#[ApiFilter(RangeFilter::class, properties: ['rooms.occupant', 'rooms.price'])]
#[ApiFilter(InFilter::class, properties: ['starNumber', 'equipments.id', 'rooms.equipments.id'])]
class Hostel
{
    use MediaTrait;
    use EnabledTrait;
    use ClosedTrait;
    use PositionTrait;
    use TimestampableTrait;
    use DeletableTrait;
    use Notifiable;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['hostel:read', 'favorite:read', 'booking:read'])]
    private ?int $id = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 4, max: 180)]
    #[ORM\Column(length: 180, nullable: true)]
    #[Groups(['hostel:read', 'booking:read'])]
    private ?string $name = null;

    #[Gedmo\Slug(fields: ['name'], unique: true)]
    #[ORM\Column(length: 180, nullable: true)]
    #[Groups(['hostel:read'])]
    private ?string $slug = null;

    #[Assert\NotBlank(message: "Entrez une adresse e-mail s'il vous plait.")]
    #[Assert\Length(min: 2, max: 180, minMessage: "L'adresse e-mail est trop courte.", maxMessage: "L'adresse e-mail est trop longue.")]
    #[Assert\Email(message: "L'adresse e-mail est invalide.")]
    #[ORM\Column(length: 180,  unique: true, nullable: true)]
    #[Groups(['hostel:read'])]
    private ?string $email = null;

    #[Assert\NotBlank(message: "Entrez un numéro de téléphone s''il vous plait.")]
    #[Assert\Length(min: 10, max: 25, minMessage: "Le numéro de téléphone est trop court.", maxMessage: "Le numéro de téléphone est trop long.", groups: ['Registration', 'Profile'])]
    #[ORM\Column(length: 25, nullable: true)]
    #[Groups(['hostel:read'])]
    private ?string $phone = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['hostel:read'])]
    private ?string $description = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $reference = null;

    #[Assert\NotBlank]
    #[ORM\Column(length: 50, nullable: true)]
    #[Groups(['hostel:read'])]
    private ?int $starNumber = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['hostel:read'])]
    private ?string $address = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['hostel:read'])]
    private ?string $codePostal = null;

    #[Assert\NotNull(message: 'Cette valeur ne doit pas être vide.')]
    #[Assert\Type(type: 'bool', message: 'Cette valeur ne doit pas être vide.')]
    #[ORM\Column(nullable: true)]
    #[Groups(['hostel:read'])]
    private ?bool $parking = null;

    #[Assert\NotNull(message: 'Cette valeur ne doit pas être vide.')]
    #[Assert\Type(type: 'bool', message: 'Cette valeur ne doit pas être vide.')]
    #[ORM\Column(nullable: true)]
    #[Groups(['hostel:read'])]
    private ?bool $breakfast = null;

    #[Assert\NotNull(message: 'Cette valeur ne doit pas être vide.')]
    #[Assert\Type(type: 'bool', message: 'Cette valeur ne doit pas être vide.')]
    #[ORM\Column(nullable: true)]
    #[Groups(['hostel:read'])]
    private ?bool $animalsAllowed = null;

    #[Assert\NotNull(message: 'Cette valeur ne doit pas être vide.')]
    #[Assert\Type(type: 'bool', message: 'Cette valeur ne doit pas être vide.')]
    #[ORM\Column(nullable: true)]
    #[Groups(['hostel:read'])]
    private ?bool $children = null;

    #[Assert\NotNull(message: 'Cette valeur ne doit pas être vide.')]
    #[Assert\Type(type: 'bool', message: 'Cette valeur ne doit pas être vide.')]
    #[ORM\Column(nullable: true)]
    #[Groups(['hostel:read'])]
    private ?bool $wifi = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['hostel:read'])]
    private ?bool $mobilePaymentAllowed = false;

    #[ORM\Column(nullable: true)]
    #[Groups(['hostel:read'])]
    private ?bool $cardPaymentAllowed = false;

    #[Assert\NotBlank]
    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    #[Groups(['hostel:read'])]
    private ?array $spokenLanguages = [];

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['hostel:read'])]
    private ?DateTimeInterface $enabledAt = null;

    #[ORM\Column(type: Types::FLOAT, nullable: true)]
    #[Groups(['hostel:read'])]
    private ?float $averageRating = 0;

    #[Assert\File(maxSize: '8M')]
    #[Vich\UploadableField(
        mapping: 'hostel',
        fileNameProperty: 'fileName',
        size: 'fileSize',
        mimeType: 'fileMimeType',
        originalName: 'fileOriginalName'
    )]
    private ?File $file = null;

    #[ORM\OneToMany(mappedBy: 'hostel', targetEntity: HostelGallery::class, orphanRemoval: true, cascade: ['ALL'])]
    #[ORM\OrderBy(['position' => 'asc'])]
    #[Groups(['hostel:read'])]
    private Collection $galleries;

    #[Assert\NotBlank]
    #[Assert\Valid]
    #[ORM\ManyToOne(inversedBy: 'hostels')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['hostel:read'])]
    private ?Category $category = null;

    #[Assert\Valid]
    #[ORM\ManyToOne(inversedBy: 'hostels')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['hostel:read'])]
    private ?User $owner = null;

    #[Assert\NotBlank]
    #[Assert\Valid]
    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['hostel:read'])]
    private ?Location $location = null;

    #[ORM\ManyToMany(targetEntity: Equipment::class, inversedBy: 'hostels')]
    #[Groups(['hostel:read'])]
    private Collection $equipments;

    #[ORM\OneToMany(mappedBy: 'hostel', targetEntity: Favorite::class, orphanRemoval: true, cascade: ['remove'])]
    private Collection $favorites;

    #[ORM\OneToMany(mappedBy: 'hostel', targetEntity: Room::class, orphanRemoval: true)]
    #[ORM\OrderBy(['price' => 'ASC'])]
    #[Groups(['hostel:read'])]
    private Collection $rooms;

    #[ORM\OneToMany(mappedBy: 'hostel', targetEntity: Review::class, orphanRemoval: true)]
    #[ORM\OrderBy(['rating' => 'DESC'])]
    #[Groups(['hostel:read'])]
    private Collection $reviews;

    #[ORM\OneToMany(mappedBy: 'hostel', targetEntity: Booking::class, orphanRemoval: true)]
    private Collection $bookings;

    #[ORM\OneToMany(mappedBy: 'hostel', targetEntity: Commande::class, orphanRemoval: true)]
    private Collection $commandes;

    #[Assert\NotBlank]
    #[Assert\Valid]
    #[ORM\OneToOne(cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['hostel:read'])]
    private ?TimeInterval $checkinTime = null;

    #[Assert\NotBlank]
    #[Assert\Valid]
    #[ORM\OneToOne(cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['hostel:read'])]
    private ?TimeInterval $checkoutTime = null;

    #[Assert\NotBlank]
    #[Assert\Valid]
    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['hostel:read'])]
    private ?Cancelation $cancellationPolicy = null;

    #[Assert\NotBlank(groups: ['Admin'])]
    #[Assert\Valid(groups: ['Admin'])]
    #[ORM\ManyToOne]
    private ?Plan $plan = null;

    public function __construct()
    {
        $this->galleries = new ArrayCollection();
        $this->equipments = new ArrayCollection();
        $this->favorites = new ArrayCollection();
        $this->rooms = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->bookings = new ArrayCollection();
        $this->commandes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

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

    public function getStarNumber(): ?int
    {
        return $this->starNumber;
    }

    public function setStarNumber(?int $starNumber): self
    {
        $this->starNumber = $starNumber;

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

    public function getCodePostal(): ?string
    {
        return $this->codePostal;
    }

    public function setCodePostal(?string $codePostal): self
    {
        $this->codePostal = $codePostal;

        return $this;
    }

    public function isParking(): ?bool
    {
        return $this->parking;
    }

    public function setParking(?bool $parking): self
    {
        $this->parking = $parking;

        return $this;
    }

    public function isBreakfast(): ?bool
    {
        return $this->breakfast;
    }

    public function setBreakfast(?bool $breakfast): self
    {
        $this->breakfast = $breakfast;

        return $this;
    }

    public function isAnimalsAllowed(): ?bool
    {
        return $this->animalsAllowed;
    }

    public function setAnimalsAllowed(?bool $animalsAllowed): self
    {
        $this->animalsAllowed = $animalsAllowed;

        return $this;
    }

    public function isChildren(): ?bool
    {
        return $this->children;
    }

    public function setChildren(?bool $children): self
    {
        $this->children = $children;

        return $this;
    }

    public function isWifi(): ?bool
    {
        return $this->wifi;
    }

    public function setWifi(?bool $wifi): self
    {
        $this->wifi = $wifi;

        return $this;
    }

    public function isMobilePaymentAllowed(): ?bool
    {
        return $this->mobilePaymentAllowed;
    }

    public function setMobilePaymentAllowed(?bool $mobilePaymentAllowed): self
    {
        $this->mobilePaymentAllowed = $mobilePaymentAllowed;

        return $this;
    }

    public function isCardPaymentAllowed(): ?bool
    {
        return $this->cardPaymentAllowed;
    }

    public function setCardPaymentAllowed(?bool $cardPaymentAllowed): self
    {
        $this->cardPaymentAllowed = $cardPaymentAllowed;

        return $this;
    }

    public function getSpokenLanguages(): array
    {
        return $this->spokenLanguages;
    }

    public function setSpokenLanguages(?array $spokenLanguages): self
    {
        $this->spokenLanguages = $spokenLanguages;

        return $this;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(?File $file): self
    {
        $this->file = $file;

        if (null !== $file) {
            $this->updatedAt = new DateTimeImmutable();
        }

        return $this;
    }

    /**
     * @return Collection<int, HostelGallery>
     */
    public function getGalleries(): Collection
    {
        return $this->galleries;
    }

    public function addGallery(HostelGallery $gallery): self
    {
        if (!$this->galleries->contains($gallery)) {
            $this->galleries[] = $gallery;
            $gallery->setHostel($this);
        }

        return $this;
    }

    public function removeGallery(HostelGallery $gallery): self
    {
        if ($this->galleries->removeElement($gallery)) {
            // set the owning side to null (unless already changed)
            if ($gallery->getHostel() === $this) {
                $gallery->setHostel(null);
            }
        }

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

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

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(Location $location): self
    {
        $this->location = $location;

        return $this;
    }

    /**
     * @return Collection<int, Equipment>
     */
    public function getEquipments(): Collection
    {
        return $this->equipments;
    }

    public function addEquipment(Equipment $equipment): self
    {
        if (!$this->equipments->contains($equipment)) {
            $this->equipments[] = $equipment;
        }

        return $this;
    }

    public function removeEquipment(Equipment $equipment): self
    {
        $this->equipments->removeElement($equipment);

        return $this;
    }

    /**
     * @return Collection<int, Favorite>
     */
    public function getFavorites(): Collection
    {
        return $this->favorites;
    }

    public function addFavorite(Favorite $favorite): self
    {
        if (!$this->favorites->contains($favorite)) {
            $this->favorites[] = $favorite;
            $favorite->setHostel($this);
        }

        return $this;
    }

    public function removeFavorite(Favorite $favorite): self
    {
        if ($this->favorites->removeElement($favorite)) {
            // set the owning side to null (unless already changed)
            if ($favorite->getHostel() === $this) {
                $favorite->setHostel(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Room>
     */
    public function getRooms(): Collection
    {
        return $this->rooms;
    }

    public function addRoom(Room $room): self
    {
        if (!$this->rooms->contains($room)) {
            $this->rooms[] = $room;
            $room->setHostel($this);
        }

        return $this;
    }

    public function removeRoom(Room $room): self
    {
        if ($this->rooms->removeElement($room)) {
            // set the owning side to null (unless already changed)
            if ($room->getHostel() === $this) {
                $room->setHostel(null);
            }
        }

        return $this;
    }

    public function getRoom(): ?Room
    {
        return $this->rooms->first();
    }

    /**
     * @return Collection<int, Review>
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): self
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews[] = $review;
            $review->setHostel($this);
        }

        return $this;
    }

    public function removeReview(Review $review): self
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getHostel() === $this) {
                $review->setHostel(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Booking>
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addBooking(Booking $booking): self
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings[] = $booking;
            $booking->setHostel($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): self
    {
        if ($this->bookings->removeElement($booking)) {
            // set the owning side to null (unless already changed)
            if ($booking->getHostel() === $this) {
                $booking->setHostel(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Commande>
     */
    public function getCommandes(): Collection
    {
        return $this->commandes;
    }

    public function addCommande(Commande $commande): self
    {
        if (!$this->commandes->contains($commande)) {
            $this->commandes[] = $commande;
            $commande->setHostel($this);
        }

        return $this;
    }

    public function removeCommande(Commande $commande): self
    {
        if ($this->commandes->removeElement($commande)) {
            // set the owning side to null (unless already changed)
            if ($commande->getHostel() === $this) {
                $commande->setHostel(null);
            }
        }

        return $this;
    }

    public function getCheckinTime(): ?TimeInterval
    {
        return $this->checkinTime;
    }

    public function setCheckinTime(TimeInterval $checkinTime): self
    {
        $this->checkinTime = $checkinTime;

        return $this;
    }

    public function getCheckoutTime(): ?TimeInterval
    {
        return $this->checkoutTime;
    }

    public function setCheckoutTime(TimeInterval $checkoutTime): self
    {
        $this->checkoutTime = $checkoutTime;

        return $this;
    }

    public function getCancellationPolicy(): ?Cancelation
    {
        return $this->cancellationPolicy;
    }

    public function setCancellationPolicy(Cancelation $cancellationPolicy): self
    {
        $this->cancellationPolicy = $cancellationPolicy;

        return $this;
    }

    public function getEnabledAt(): ?DateTimeInterface
    {
        return $this->enabledAt;
    }

    public function setEnabledAt(?DateTimeInterface $enabledAt): self
    {
        $this->enabledAt = $enabledAt;

        return $this;
    }

    public function getAverageRating(): ?float
    {
        return $this->averageRating;
    }

    public function setAverageRating(?float $averageRating): self
    {
        $this->averageRating = $averageRating;

        return $this;
    }

    public function getPlan(): ?Plan
    {
        return $this->plan;
    }

    public function setPlan(?Plan $plan): self
    {
        $this->plan = $plan;

        return $this;
    }
}
