<?php

namespace App\Data;

use Symfony\Component\Validator\Constraints as Assert;

class BookingCancelledData
{
    #[Assert\NotBlank]
    #[Assert\Email]
    public ?string $email = null;

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }
}
