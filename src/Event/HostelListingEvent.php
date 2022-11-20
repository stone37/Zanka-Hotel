<?php

namespace App\Event;

use Symfony\Component\HttpFoundation\Request;

class HostelListingEvent
{
    private Request $request;

    public function  __construct(Request $request)
    {
        $this->request = $request;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }
}