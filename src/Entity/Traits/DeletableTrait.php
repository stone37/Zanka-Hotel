<?php

namespace App\Entity\Traits;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

trait DeletableTrait
{
    #[ORM\Column(nullable: true)]
    #[Groups(['user:read', 'user:cancel', 'hostel:read'])]
    private ?DateTimeImmutable $deleteAt = null;

    public function getDeleteAt(): ?DateTimeImmutable
    {
        return $this->deleteAt;
    }

    public function setDeleteAt(?DateTimeImmutable $deleteAt): self
    {
        $this->deleteAt = $deleteAt;

        return $this;
    }
}

