<?php

namespace App\Data;

use Symfony\Component\Validator\Constraints as Assert;

final class BookingSearchVerifyRequestData
{
    #[Assert\NotBlank]
    public string $code = '';

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): BookingSearchVerifyRequestData
    {
        $this->code = $code;

        return $this;
    }
}
