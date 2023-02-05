<?php

namespace App\Data;

use Symfony\Component\Validator\Constraints as Assert;

class ReviewData
{
    #[Assert\NotBlank]
    public ?string $number = '';
}

