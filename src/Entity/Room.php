<?php

namespace App\Entity;

use App\Entity\Traits\EnabledTrait;
use App\Entity\Traits\PositionTrait;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\RoomRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: RoomRepository::class)]
class Room
{
    use TimestampableTrait;
    use PositionTrait;
    use EnabledTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['hostel:read', 'booking:read'])]
    private ?int $id = null;

    #[Assert\NotBlank(message: "Veuillez sélectionner un type d'hébergement.")]
    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['hostel:read', 'booking:read'])]
    private ?string $type = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['hostel:read', 'booking:read'])]
    private ?string $name = null;

    #[Gedmo\Slug(fields: ['name'], unique: true)]
    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['hostel:read', 'booking:read'])]
    private ?string $slug = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['hostel:read', 'booking:read'])]
    private ?string $perfectName = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['hostel:read', 'booking:read'])]
    private ?string $specification = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['hostel:read', 'booking:read'])]
    private ?string $feature = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['hostel:read', 'booking:read'])]
    private ?string $amenities = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['hostel:read', 'booking:read'])]
    private ?string $description = null;

    #[Assert\NotBlank(message: "Veuillez indiquer le nombre d'hébergement.")]
    #[ORM\Column(nullable: true)]
    #[Groups(['hostel:read', 'booking:read'])]
    private ?int $roomNumber = null;

    #[Assert\NotBlank(message: "Veuillez indiquer le prix de cet type d'hébergement.")]
    #[ORM\Column(nullable: true)]
    #[Groups(['hostel:read', 'booking:read'])]
    private ?int $price = null;

    #[ORM\Column(nullable: true)]
    private ?int $originalPrice = null;

    #[Assert\NotBlank]
    #[ORM\Column(nullable: true)]
    #[Groups(['hostel:read', 'booking:read'])]
    private ?int $smoker = null;

    #[Assert\NotBlank(message: "Veuillez indiquer le nombre d'occupant max.")]
    #[ORM\Column(nullable: true)]
    #[Groups(['hostel:read', 'booking:read'])]
    private ?int $occupant = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['hostel:read', 'booking:read'])]
    private ?int $area = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['hostel:read', 'booking:read'])]
    private ?int $dataRoomNumber = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['hostel:read', 'booking:read'])]
    private ?int $dataLivingRoomNumber = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['hostel:read', 'booking:read'])]
    private ?int $dataBathroomNumber = null;

    #[ORM\ManyToMany(targetEntity: RoomEquipment::class, inversedBy: 'rooms')]
    #[Groups(['hostel:read'])]
    private Collection $equipments;

    #[Assert\NotBlank(message: "Veuillez sélectionner un établissement.")]
    #[ORM\ManyToOne(inversedBy: 'rooms')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Hostel $hostel = null;

    #[ORM\OneToMany(mappedBy: 'room', targetEntity: RoomGallery::class, orphanRemoval: true, cascade: ['ALL'])]
    #[ORM\OrderBy(['position' => 'ASC'])]
    #[Groups(['hostel:read', 'booking:read'])]
    private Collection $galleries;

    #[ORM\ManyToMany(targetEntity: Supplement::class, inversedBy: 'rooms')]
    #[Groups(['hostel:read', 'booking:read'])]
    private Collection $supplements;

    #[ORM\OneToMany(mappedBy: 'room', targetEntity: Booking::class)]
    private Collection $bookings;

    #[ORM\ManyToMany(targetEntity: Promotion::class, mappedBy: 'rooms')]
    #[ORM\OrderBy(['createdAt' => 'asc'])]
    #[Groups(['hostel:read', 'booking:read'])]
    private Collection $promotions;

    #[Assert\Valid]
    #[ORM\OneToMany(mappedBy: 'room', targetEntity: Bedding::class, orphanRemoval: true, cascade: ['ALL'])]
    #[Groups(['hostel:read', 'booking:read'])]
    private Collection $beddings;

    #[ORM\ManyToMany(targetEntity: Taxe::class, inversedBy: 'rooms')]
    #[Groups(['hostel:read', 'booking:read'])]
    private Collection $taxes;

    public function __construct()
    {
        $this->equipments = new ArrayCollection();
        $this->galleries = new ArrayCollection();
        $this->promotions = new ArrayCollection();
        $this->supplements = new ArrayCollection();
        $this->bookings = new ArrayCollection();
        $this->beddings = new ArrayCollection();
        $this->taxes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
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

    public function getPerfectName(): ?string
    {
        return $this->perfectName;
    }

    public function setPerfectName(?string $perfectName): self
    {
        $this->perfectName = $perfectName;

        return $this;
    }

    public function getSpecification(): ?string
    {
        return $this->specification;
    }

    public function setSpecification(?string $specification): self
    {
        $this->specification = $specification;

        return $this;
    }

    public function getFeature(): ?string
    {
        return $this->feature;
    }

    public function setFeature(?string $feature): self
    {
        $this->feature = $feature;

        return $this;
    }

    public function getAmenities(): ?string
    {
        return $this->amenities;
    }

    public function setAmenities(?string $amenities): self
    {
        $this->amenities = $amenities;

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

    public function getRoomNumber(): ?int
    {
        return $this->roomNumber;
    }

    public function setRoomNumber(?int $roomNumber): self
    {
        $this->roomNumber = $roomNumber;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(?int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getOriginalPrice(): ?int
    {
        return $this->originalPrice;
    }

    public function setOriginalPrice(?int $originalPrice): self
    {
        $this->originalPrice = $originalPrice;

        return $this;
    }

    public function getSmoker(): ?int
    {
        return $this->smoker;
    }

    public function setSmoker(?int $smoker): self
    {
        $this->smoker = $smoker;

        return $this;
    }

    public function getOccupant(): ?int
    {
        return $this->occupant;
    }

    public function setOccupant(?int $occupant): self
    {
        $this->occupant = $occupant;

        return $this;
    }

    public function getArea(): ?int
    {
        return $this->area;
    }

    public function setArea(?int $area): self
    {
        $this->area = $area;

        return $this;
    }

    public function getDataRoomNumber(): ?int
    {
        return $this->dataRoomNumber;
    }

    public function setDataRoomNumber(?int $dataRoomNumber): self
    {
        $this->dataRoomNumber = $dataRoomNumber;

        return $this;
    }

    public function getDataLivingRoomNumber(): ?int
    {
        return $this->dataLivingRoomNumber;
    }

    public function setDataLivingRoomNumber(?int $dataLivingRoomNumber): self
    {
        $this->dataLivingRoomNumber = $dataLivingRoomNumber;

        return $this;
    }

    public function getDataBathroomNumber(): ?int
    {
        return $this->dataBathroomNumber;
    }

    public function setDataBathroomNumber(?int $dataBathroomNumber): self
    {
        $this->dataBathroomNumber = $dataBathroomNumber;

        return $this;
    }

    /**
     * @return Collection<int, RoomEquipment>
     */
    public function getEquipments(): Collection
    {
        return $this->equipments;
    }

    public function addEquipment(RoomEquipment $equipment): self
    {
        if (!$this->equipments->contains($equipment)) {
            $this->equipments[] = $equipment;
        }

        return $this;
    }

    public function removeEquipment(RoomEquipment $equipment): self
    {
        $this->equipments->removeElement($equipment);

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

    /**
     * @return Collection<int, RoomGallery>
     */
    public function getGalleries(): Collection
    {
        return $this->galleries;
    }

    public function addGallery(RoomGallery $gallery): self
    {
        if (!$this->galleries->contains($gallery)) {
            $this->galleries[] = $gallery;
            $gallery->setRoom($this);
        }

        return $this;
    }

    public function removeGallery(RoomGallery $gallery): self
    {
        if ($this->galleries->removeElement($gallery)) {
            // set the owning side to null (unless already changed)
            if ($gallery->getRoom() === $this) {
                $gallery->setRoom(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Supplement>
     */
    public function getSupplements(): Collection
    {
        return $this->supplements;
    }

    public function addSupplement(Supplement $supplement): self
    {
        if (!$this->supplements->contains($supplement)) {
            $this->supplements[] = $supplement;
        }

        return $this;
    }

    public function removeSupplement(Supplement $supplement): self
    {
        $this->supplements->removeElement($supplement);

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
            $booking->setRoom($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): self
    {
        if ($this->bookings->removeElement($booking)) {
            // set the owning side to null (unless already changed)
            if ($booking->getRoom() === $this) {
                $booking->setRoom(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Promotion>
     */
    public function getPromotions(): Collection
    {
        return $this->promotions;
    }

    public function addPromotion(Promotion $promotion): self
    {
        if (!$this->promotions->contains($promotion)) {
            $this->promotions[] = $promotion;
            $promotion->addRoom($this);
        }

        return $this;
    }

    public function removePromotion(Promotion $promotion): self
    {
        if ($this->promotions->removeElement($promotion)) {
            $promotion->removeRoom($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Bedding>
     */
    public function getBeddings(): Collection
    {
        return $this->beddings;
    }

    public function addBedding(Bedding $bedding): self
    {
        if (!$this->beddings->contains($bedding)) {
            $this->beddings->add($bedding);
            $bedding->setRoom($this);
        }

        return $this;
    }

    public function removeBedding(Bedding $bedding): self
    {
        if ($this->beddings->removeElement($bedding)) {
            // set the owning side to null (unless already changed)
            if ($bedding->getRoom() === $this) {
                $bedding->setRoom(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Taxe>
     */
    public function getTaxes(): Collection
    {
        return $this->taxes;
    }

    public function addTax(Taxe $tax): self
    {
        if (!$this->taxes->contains($tax)) {
            $this->taxes[] = $tax;
        }

        return $this;
    }

    public function removeTax(Taxe $tax): self
    {
        $this->taxes->removeElement($tax);

        return $this;
    }

    public function isPriceReduced(): bool
    {
        return $this->originalPrice > $this->price;
    }
}
