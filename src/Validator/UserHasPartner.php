<?php

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute] class UserHasPartner extends Constraint
{
    public string $message = 'Cet utilisateur n\'est pas un partenaire.';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}