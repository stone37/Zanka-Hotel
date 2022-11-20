<?php

namespace App\Entity;

use App\Entity\Traits\EnabledTrait;
use App\Entity\Traits\PositionTrait;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\PromotionRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: PromotionRepository::class)]
class Promotion
{
    public const STATE_INACTIVE = 'inactive';
    public const STATE_PROCESSING = 'processing';
    public const STATE_ACTIVE = 'active';
    public const STATE_FAILED = 'failed';

    use TimestampableTrait;
    use EnabledTrait;
    use PositionTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank(message: 'Veuillez entrer le nom de la promotion.')]
    #[Assert\Length(min: 2, max: 180, minMessage: 'Le nom de la promotion catalogue doit contenir au moins {{ limit }} caractères.', maxMessage: 'Le nom de la promotion catalogue ne peut pas dépasser les {{ limit }} caractères.')]
    #[ORM\Column(length: 180, nullable: true)]
    private ?string $name = null;

    #[Gedmo\Slug(fields: ['name'], unique: true)]
    #[ORM\Column(length: 180, nullable: true)]
    private ?string $slug = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $startDate = null;

    #[Assert\GreaterThan(propertyPath: 'startDate')]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $endDate = null;

    #[ORM\Column(nullable: true, options: ['default' => 0])]
    private bool $exclusive = false;

    private string $state = self::STATE_INACTIVE;

    #[ORM\ManyToOne]
    private ?User $owner = null;

    #[Assert\Valid]
    #[ORM\OneToOne(mappedBy: 'promotion', cascade: ['persist', 'remove'])]
    private ?PromotionAction $action = null;

    #[ORM\ManyToMany(targetEntity: Room::class, inversedBy: 'promotions')]
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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

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

    public function getStartDate(): ?DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(?DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(?DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function isExclusive(): ?bool
    {
        return $this->exclusive;
    }

    public function setExclusive(bool $exclusive): self
    {
        $this->exclusive = $exclusive;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): self
    {
        $this->state = $state;

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

    public function getAction(): ?PromotionAction
    {
        return $this->action;
    }

    public function setAction(PromotionAction $action): self
    {
        // set the owning side of the relation if necessary
        if ($action->getPromotion() !== $this) {
            $action->setPromotion($this);
        }

        $this->action = $action;

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
        }

        return $this;
    }

    public function removeRoom(Room $room): self
    {
        $this->rooms->removeElement($room);

        return $this;
    }
}
