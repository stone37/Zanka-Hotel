<?php

namespace App\Entity;

use App\Entity\Traits\EnabledTrait;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\TaxeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TaxeRepository::class)]
class Taxe
{
    use EnabledTrait;
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['hostel:read', 'booking:read'])]
    private ?int $id = null;

    #[Assert\NotBlank]
    #[ORM\Column(length: 100, nullable: true)]
    #[Groups(['hostel:read', 'booking:read'])]
    private ?string $name = null;

    #[Assert\NotBlank]
    #[ORM\Column(nullable: true)]
    #[Groups(['hostel:read', 'booking:read'])]
    private float $value = 0.0;

    #[ORM\Column(nullable: true)]
    private ?bool $includedInPrice = false;

    #[ORM\ManyToOne(inversedBy: 'taxes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    #[ORM\ManyToMany(targetEntity: Room::class, mappedBy: 'taxes')]
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

    public function getValue(): float
    {
        return $this->value;
    }

    public function setValue(float $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function isIncludedInPrice(): ?bool
    {
        return $this->includedInPrice;
    }

    public function setIncludedInPrice(?bool $includedInPrice): self
    {
        $this->includedInPrice = $includedInPrice;

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
            $room->addTax($this);
        }

        return $this;
    }

    public function removeRoom(Room $room): self
    {
        if ($this->rooms->removeElement($room)) {
            $room->removeTax($this);
        }

        return $this;
    }
}