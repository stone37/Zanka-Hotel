<?php

namespace App\Entity;

use App\Entity\Traits\PositionTrait;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\EquipmentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EquipmentRepository::class)]
class Equipment
{
    use PositionTrait;
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 180)]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[Assert\NotBlank]
    #[ORM\ManyToOne(inversedBy: 'equipments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?EquipmentGroup $equipmentGroup = null;

    #[ORM\ManyToMany(targetEntity: Hostel::class, mappedBy: 'equipments')]
    private Collection $hostels;

    public function __construct()
    {
        $this->hostels = new ArrayCollection();
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

    public function getEquipmentGroup(): ?EquipmentGroup
    {
        return $this->equipmentGroup;
    }

    public function setEquipmentGroup(?EquipmentGroup $equipmentGroup): self
    {
        $this->equipmentGroup = $equipmentGroup;

        return $this;
    }

    /**
     * @return Collection<int, Hostel>
     */
    public function getHostels(): Collection
    {
        return $this->hostels;
    }

    public function addHostel(Hostel $hostel): self
    {
        if (!$this->hostels->contains($hostel)) {
            $this->hostels[] = $hostel;
            $hostel->addEquipment($this);
        }

        return $this;
    }

    public function removeHostel(Hostel $hostel): self
    {
        if ($this->hostels->removeElement($hostel)) {
            $hostel->removeEquipment($this);
        }

        return $this;
    }
}
