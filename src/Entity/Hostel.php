<?php

namespace App\Entity;

use App\Entity\Traits\DeletableTrait;
use App\Entity\Traits\EnabledTrait;
use App\Entity\Traits\MediaTrait;
use App\Entity\Traits\Notifiable;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\HostelRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

#[Vich\Uploadable]
#[ORM\Entity(repositoryClass: HostelRepository::class)]
class Hostel
{
    use MediaTrait;
    use EnabledTrait;
    use TimestampableTrait;
    use DeletableTrait;
    use Notifiable;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 4, max: 180)]
    #[ORM\Column(length: 180, nullable: true)]
    private ?string $name = null;

    #[Gedmo\Slug(fields: ['name'], unique: true)]
    #[ORM\Column(length: 180, nullable: true)]
    private ?string $slug = null;

    #[Assert\NotBlank(message: "Entrez une adresse e-mail s'il vous plait.")]
    #[Assert\Length(min: 2, max: 180, minMessage: "L'adresse e-mail est trop courte.", maxMessage: "L'adresse e-mail est trop longue.")]
    #[Assert\Email(message: "L'adresse e-mail est invalide.")]
    #[ORM\Column(length: 180,  unique: true, nullable: true)]
    private ?string $email = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 5)]
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $reference = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $starNumber = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $address = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $codePostal = null;

    #[ORM\Column(nullable: true)]
    private ?int $averageRating = null;

    #[ORM\Column(nullable: true)]
    private ?int $propertyAverageRating = null;

    #[ORM\Column(nullable: true)]
    private ?int $equipmentAverageRating = null;

    #[ORM\Column(nullable: true)]
    private ?int $personalAverageRating = null;

    #[ORM\Column(nullable: true)]
    private ?int $comfortAverageRating = null;

    #[ORM\Column(nullable: true)]
    private ?int $priceAverageRating = null;

    #[ORM\Column(nullable: true)]
    private ?int $locationAverageRating = null;

    #[ORM\Column(nullable: true)]
    private ?bool $parking = null;

    #[ORM\Column(nullable: true)]
    private ?int $parkingPrice = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $animalsAllowed = null;

    #[ORM\Column(nullable: true)]
    private ?bool $mobilePaymentAllowed = false;

    #[ORM\Column(nullable: true)]
    private ?bool $cardPaymentAllowed = false;

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private ?array $spokenLanguages = [];

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $breakfast = null;

    #[ORM\Column(nullable: true)]
    private ?int $breakfastPrice = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $cancelFreeOfCharge = null;

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
    private Collection $galleries;

    #[ORM\ManyToOne(inversedBy: 'hostels')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    #[ORM\ManyToOne(inversedBy: 'hostels')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Location $location = null;

    #[ORM\ManyToMany(targetEntity: EquipmentGroup::class, inversedBy: 'hostels')]
    private Collection $equipments;

    #[ORM\OneToMany(mappedBy: 'hostel', targetEntity: Favorite::class)]
    private Collection $favorites;

    #[ORM\OneToMany(mappedBy: 'hostel', targetEntity: Room::class, orphanRemoval: true)]
    private Collection $rooms;

    #[ORM\OneToMany(mappedBy: 'hostel', targetEntity: Review::class, orphanRemoval: true)]
    private Collection $reviews;

    #[ORM\OneToMany(mappedBy: 'hostel', targetEntity: Booking::class, orphanRemoval: true)]
    private Collection $bookings;

    #[ORM\OneToMany(mappedBy: 'hostel', targetEntity: Commande::class, orphanRemoval: true)]
    private Collection $commandes;

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

    public function getStarNumber(): ?string
    {
        return $this->starNumber;
    }

    public function setStarNumber(?string $starNumber): self
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

    public function getAverageRating(): ?int
    {
        return $this->averageRating;
    }

    public function setAverageRating(?int $averageRating): self
    {
        $this->averageRating = $averageRating;

        return $this;
    }

    public function getPropertyAverageRating(): ?int
    {
        return $this->propertyAverageRating;
    }

    public function setPropertyAverageRating(?int $propertyAverageRating): self
    {
        $this->propertyAverageRating = $propertyAverageRating;

        return $this;
    }

    public function getEquipmentAverageRating(): ?int
    {
        return $this->equipmentAverageRating;
    }

    public function setEquipmentAverageRating(?int $equipmentAverageRating): self
    {
        $this->equipmentAverageRating = $equipmentAverageRating;

        return $this;
    }

    public function getPersonalAverageRating(): ?int
    {
        return $this->personalAverageRating;
    }

    public function setPersonalAverageRating(?int $personalAverageRating): self
    {
        $this->personalAverageRating = $personalAverageRating;

        return $this;
    }

    public function getComfortAverageRating(): ?int
    {
        return $this->comfortAverageRating;
    }

    public function setComfortAverageRating(?int $comfortAverageRating): self
    {
        $this->comfortAverageRating = $comfortAverageRating;

        return $this;
    }

    public function getPriceAverageRating(): ?int
    {
        return $this->priceAverageRating;
    }

    public function setPriceAverageRating(?int $priceAverageRating): self
    {
        $this->priceAverageRating = $priceAverageRating;

        return $this;
    }

    public function getLocationAverageRating(): ?int
    {
        return $this->locationAverageRating;
    }

    public function setLocationAverageRating(?int $locationAverageRating): self
    {
        $this->locationAverageRating = $locationAverageRating;

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

    public function getParkingPrice(): ?int
    {
        return $this->parkingPrice;
    }

    public function setParkingPrice(?int $parkingPrice): self
    {
        $this->parkingPrice = $parkingPrice;

        return $this;
    }

    public function getAnimalsAllowed(): ?string
    {
        return $this->animalsAllowed;
    }

    public function setAnimalsAllowed(?string $animalsAllowed): self
    {
        $this->animalsAllowed = $animalsAllowed;

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

    public function getBreakfast(): ?string
    {
        return $this->breakfast;
    }

    public function setBreakfast(?string $breakfast): self
    {
        $this->breakfast = $breakfast;

        return $this;
    }

    public function getBreakfastPrice(): ?int
    {
        return $this->breakfastPrice;
    }

    public function setBreakfastPrice(?int $breakfastPrice): self
    {
        $this->breakfastPrice = $breakfastPrice;

        return $this;
    }

    public function getCancelFreeOfCharge(): ?string
    {
        return $this->cancelFreeOfCharge;
    }

    public function setCancelFreeOfCharge(?string $cancelFreeOfCharge): self
    {
        $this->cancelFreeOfCharge = $cancelFreeOfCharge;

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
     * @return Collection<int, EquipmentGroup>
     */
    public function getEquipments(): Collection
    {
        return $this->equipments;
    }

    public function addEquipment(EquipmentGroup $equipment): self
    {
        if (!$this->equipments->contains($equipment)) {
            $this->equipments[] = $equipment;
        }

        return $this;
    }

    public function removeEquipment(EquipmentGroup $equipment): self
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
}
