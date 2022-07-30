<?php

namespace App\Entity;

use App\Entity\Traits\PositionTrait;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\RoomEquipmentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RoomEquipmentRepository::class)]
class RoomEquipment
{
    use TimestampableTrait;
    use PositionTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 100)]
    #[ORM\Column(length: 180, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToOne]
    private ?User $owner = null;

    #[ORM\ManyToOne(inversedBy: 'equipments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?RoomEquipmentGroup $roomEquipmentGroup = null;

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

    public function getRoomEquipmentGroup(): ?RoomEquipmentGroup
    {
        return $this->roomEquipmentGroup;
    }

    public function setRoomEquipmentGroup(?RoomEquipmentGroup $roomEquipmentGroup): self
    {
        $this->roomEquipmentGroup = $roomEquipmentGroup;

        return $this;
    }
}
