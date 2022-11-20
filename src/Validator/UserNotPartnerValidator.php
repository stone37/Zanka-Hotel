<?php

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

#[Attribute] class UserNotPartnerValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint)
    {
        /** @var UserNotPartner $constraint */
        Assert::isInstanceOf($constraint, UserHasPartner::class);

        $value->getOwner();

        if (in_array('ROLE_PARTNER', $value->getOwner()->getRoles())) {
            $this->context->addViolation($constraint->message);
        }
    }
}