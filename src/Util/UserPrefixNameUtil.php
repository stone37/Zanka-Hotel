<?php

namespace App\Util;

use App\Entity\User;

class UserPrefixNameUtil
{
    public function prefix(User $user)
    {
        $name = explode(' ', $user->getFirstName());
        $prefix = substr($name[count($name)-1],0,1);

        return strtoupper($prefix);
    }
}