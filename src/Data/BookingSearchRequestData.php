<?php

namespace App\Data;

use Symfony\Component\Validator\Constraints as Assert;

final class BookingSearchRequestData
{
    #[Assert\NotBlank]
    #[Assert\Email]
    public string $email = '';

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): BookingSearchRequestData
    {
        $this->email = $email;

        return $this;
    }
}
