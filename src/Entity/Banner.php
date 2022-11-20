<?php

namespace App\Entity;

use App\Entity\Traits\EnabledTrait;
use App\Entity\Traits\MediaMobileTrait;
use App\Entity\Traits\MediaTrait;
use App\Entity\Traits\PositionTrait;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\BannerRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[Vich\Uploadable]
#[ORM\Entity(repositoryClass: BannerRepository::class)]
class Banner
{
    public const TYPE_TEXT = 'text';
    public const TYPE_SILVER = 'silver';

    public const LOCATION_TOP = 'top';
    public const LOCATION_MIDDLE = 'middle';
    public const LOCATION_BOTTOM = 'bottom';

    public const DEVICE_ALL = 0;
    public const DEVICE_DESKTOP = 1;
    public const DEVICE_MOBILE = 2;

    use TimestampableTrait;
    use PositionTrait;
    use EnabledTrait;
    use MediaTrait;
    use MediaMobileTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $url = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $mainText = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $secondaryText = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $type = 'text';

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $location = 'middle';

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $bgColor = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?int $device = self::DEVICE_ALL;

    #[Assert\File(maxSize: '8M')]
    #[Vich\UploadableField(
        mapping: 'banner',
        fileNameProperty: 'fileName',
        size: 'fileSize',
        mimeType: 'fileMimeType',
        originalName: 'fileOriginalName'
    )]
    private ?File $file = null;

    #[Assert\File(maxSize: '8M')]
    #[Vich\UploadableField(
        mapping: 'banner_mobile',
        fileNameProperty: 'fileMobileName',
        size: 'fileMobileSize',
        mimeType: 'fileMobileMimeType',
        originalName: 'fileMobileOriginalName'
    )]
    private ?File $fileMobile = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getMainText(): ?string
    {
        return $this->mainText;
    }

    public function setMainText(?string $mainText): self
    {
        $this->mainText = $mainText;

        return $this;
    }

    public function getSecondaryText(): ?string
    {
        return $this->secondaryText;
    }

    public function setSecondaryText(?string $secondaryText): self
    {
        $this->secondaryText = $secondaryText;

        return $this;
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

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getBgColor(): ?string
    {
        return $this->bgColor;
    }

    public function setBgColor(?string $bgColor): self
    {
        $this->bgColor = $bgColor;

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

    public function getFileMobile(): ?File
    {
        return $this->fileMobile;
    }

    public function setFileMobile(?File $file): self
    {
        $this->fileMobile = $file;

        if (null !== $file) {
            $this->updatedAt = new DateTimeImmutable();
        }

        return $this;
    }

    public function getDevice(): ?int
    {
        return $this->device;
    }

    public function setDevice(?int $device): self
    {
        $this->device = $device;

        return $this;
    }
}
