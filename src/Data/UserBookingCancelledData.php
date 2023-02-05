<?php

namespace App\Data;

use Symfony\Component\Validator\Constraints as Assert;

class UserBookingCancelledData
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 8, max: 4096)]
    public ?string $password = null;

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }
}