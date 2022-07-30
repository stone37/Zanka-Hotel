<?php

namespace App\Entity;

use App\Entity\Traits\MediaTrait;
use App\Entity\Traits\PositionTrait;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\CategoryRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Gedmo\Mapping\Annotation as Gedmo;

#[Vich\Uploadable]
#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    use PositionTrait;
    use TimestampableTrait;
    use MediaTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 180)]
    #[ORM\Column(length: 180, nullable: true)]
    private ?string $name = null;

    #[Gedmo\Slug(fields: ['name'], unique: true)]
    #[ORM\Column(length: 180, nullable: true)]
    private ?string $slug = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[Assert\File(maxSize: '8M')]
    #[Vich\UploadableField(
        mapping: 'category',
        fileNameProperty: 'fileName',
        size: 'fileSize',
        mimeType: 'fileMimeType',
        originalName: 'fileOriginalName'
    )]
    private ?File $file = null;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Hostel::class)]
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
            $hostel->setCategory($this);
        }

        return $this;
    }

    public function removeHostel(Hostel $hostel): self
    {
        if ($this->hostels->removeElement($hostel)) {
            // set the owning side to null (unless already changed)
            if ($hostel->getCategory() === $this) {
                $hostel->setCategory(null);
            }
        }

        return $this;
    }
}
