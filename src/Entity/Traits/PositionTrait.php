<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;

trait PositionTrait
{
    #[ORM\Column(nullable: true)]
    #[Gedmo\SortablePosition]
    #[Groups(['hostel:read', 'city:read', 'category:read', 'favorite:read'])]
    private ?int $position = null;

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): self
    {
        $this->position = $position;

        return $this;
    }
}


