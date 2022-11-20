<?php

namespace App\Entity;

use App\Entity\Traits\EnabledTrait;
use App\Entity\Traits\MediaTrait;
use App\Entity\Traits\PositionTrait;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\CityRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Vich\UploaderBundle\Mapping\Annotation as Vich;


#[Vich\Uploadable]
#[UniqueEntity(fields: ['name'], message: 'Il existe déjà une ville avec cet nom.')]
#[ORM\Entity(repositoryClass: CityRepository::class)]
class City
{
    use EnabledTrait;
    use TimestampableTrait;
    use PositionTrait;
    use MediaTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 100)]
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $name = null;

    #[Gedmo\Slug(fields: ['name'], unique: true)]
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $slug = null;

    #[Assert\NotBlank]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $country = null;

    #[Assert\File(maxSize: '8M')]
    #[Vich\UploadableField(
        mapping: 'city',
        fileNameProperty: 'fileName',
        size: 'fileSize',
        mimeType: 'fileMimeType',
        originalName: 'fileOriginalName'
    )]
    private ?File $file = null;

    public function __construct()
    {
        $this->zones = new ArrayCollection();
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

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

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
}
