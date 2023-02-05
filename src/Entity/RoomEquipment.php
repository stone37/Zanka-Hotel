<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Entity\Traits\PositionTrait;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\RoomEquipmentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RoomEquipmentRepository::class)]
#[GetCollection(
    openapiContext: ['summary' => 'Récupère tous les équipements des chambres'],
    paginationItemsPerPage: 20,
    paginationMaximumItemsPerPage: 20,
    paginationClientItemsPerPage: true,
    normalizationContext: ['groups' => ['equipment:room:read']],
)]
#[Get(
    openapiContext: ['summary' => 'Récupère un équipement'],
    normalizationContext: ['groups' => ['equipment:room:read']]
)]
class RoomEquipment
{
    use TimestampableTrait;
    use PositionTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['hostel:read', 'equipment:room:read'])]
    private ?int $id = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 100)]
    #[ORM\Column(length: 180, nullable: true)]
    #[Groups(['hostel:read', 'equipment:room:read'])]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['hostel:read', 'equipment:room:read'])]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'equipments')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['hostel:read'])]
    private ?RoomEquipmentGroup $roomEquipmentGroup = null;

    #[ORM\ManyToMany(targetEntity: Room::class, mappedBy: 'equipments')]
    private Collection $rooms;

    public function __construct()
    {
        $this->rooms = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getRoomEquipmentGroup(): ?RoomEquipmentGroup
    {
        return $this->roomEquipmentGroup;
    }

    public function setRoomEquipmentGroup(?RoomEquipmentGroup $roomEquipmentGroup): self
    {
        $this->roomEquipmentGroup = $roomEquipmentGroup;

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
            $room->addEquipment($this);
        }

        return $this;
    }

    public function removeRoom(Room $room): self
    {
        if ($this->rooms->removeElement($room)) {
            $room->removeEquipment($this);
        }

        return $this;
    }
}
