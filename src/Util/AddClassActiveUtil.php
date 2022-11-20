<?php

namespace App\Util;

use Symfony\Component\HttpFoundation\RequestStack;

class AddClassActiveUtil
{
    private RequestStack $request;

    public function __construct(RequestStack $request)
    {
        $this->request = $request;
    }

    public function verify($routesToCheck): bool
    {
        $currentRoute = $this->request->getMainRequest()->get('_route');

        if ($routesToCheck == $currentRoute) {
            return true;
        }

        return false;
    }
}