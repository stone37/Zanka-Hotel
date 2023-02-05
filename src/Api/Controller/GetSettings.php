<?php

namespace App\Api\Controller;

use App\Entity\Settings;
use App\Manager\SettingsManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class GetSettings extends AbstractController
{
    public function __construct(private SettingsManager $manager)
    {
    }

    public function __invoke(Request $request): Settings
    {
        return $this->manager->get();
    }
}
