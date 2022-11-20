<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait MediaMobileTrait
{
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $fileMobileName = null;

    #[ORM\Column(nullable: true)]
    private ?int $fileMobileSize = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $fileMobileMimeType = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $fileMobileOriginalName = null;

    public function getFileMobileName(): ?string
    {
        return $this->fileMobileName;
    }

    public function setFileMobileName(?string $fileName): self
    {
        $this->fileMobileName = $fileName;

        return $this;
    }

    public function getFileMobileSize(): ?int
    {
        return $this->fileMobileSize;
    }

    public function setFileMobileSize(?int $fileSize): self
    {
        $this->fileMobileSize = $fileSize;

        return $this;
    }

    public function getFileMobileMimeType(): ?string
    {
        return $this->fileMobileMimeType;
    }

    public function setFileMobileMimeType(?string $fileMimeType): self
    {
        $this->fileMobileMimeType = $fileMimeType;

        return $this;
    }

    public function getFileMobileOriginalName(): ?string
    {
        return $this->fileMobileOriginalName;
    }

    public function setFileMobileOriginalName(?string $fileOriginalName): self
    {
        $this->fileMobileOriginalName = $fileOriginalName;

        return $this;
    }
}

