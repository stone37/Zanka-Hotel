<?php

namespace App\Util;

use App\Entity\User;

class UserPrefixNameUtil
{
    public function prefix(User $user): string
    {
        $name = explode(' ', $user->getFirstName());
        $prefix = substr($name[count($name)-1],0,1);

        return strtoupper($prefix);
    }

    public function dataPrefix(string $data): string
    {
        $name = explode(' ', $data);
        $prefix = substr($name[count($name)-1],0,1);

        return strtoupper($prefix);
    }
}