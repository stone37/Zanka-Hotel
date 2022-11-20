<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait ClosedTrait
{
    #[ORM\Column(nullable: true)]
    private ?bool $closed = false;

    public function isClosed(): ?bool
    {
        return $this->closed;
    }

    public function setClosed(?bool $closed): self
    {
        $this->closed = $closed;

        return $this;
    }
}


