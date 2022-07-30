<?php

namespace App\Entity;

use App\Repository\RoomEquipmentGroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RoomEquipmentGroupRepository::class)]
class RoomEquipmentGroup
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 180)]
    #[ORM\Column(length: 180, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToOne]
    private ?User $owner = null;

    #[ORM\OneToMany(mappedBy: 'roomEquipmentGroup', targetEntity: RoomEquipment::class, orphanRemoval: true)]
    private Collection $equipments;

    #[ORM\ManyToMany(targetEntity: Room::class, mappedBy: 'equipments')]
    private Collection $rooms;

    public function __construct()
    {
        $this->equipments = new ArrayCollection();
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

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

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
            $equipment->setRoomEquipmentGroup($this);
        }

        return $this;
    }

    public function removeEquipment(RoomEquipment $equipment): self
    {
        if ($this->equipments->removeElement($equipment)) {
            // set the owning side to null (unless already changed)
            if ($equipment->getRoomEquipmentGroup() === $this) {
                $equipment->setRoomEquipmentGroup(null);
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
