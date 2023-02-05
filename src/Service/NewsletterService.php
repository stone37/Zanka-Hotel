<?php

namespace App\Service;

use App\Entity\Emailing;
use App\Mailing\Mailer;
use App\Manager\SettingsManager;

class NewsletterService
{
    private Mailer $mailer;
    private SettingsManager $manager;

    public function __construct(Mailer $mailer, SettingsManager $manager)
    {
        $this->mailer = $mailer;
        $this->manager = $manager;
    }

    public function sendEmailing(Emailing $emailing)
    {
        $sender = $this->mailer->createEmail('mails/newsletter/particulier.twig', ['emailing' => $emailing])
            ->to($emailing->getDestinataire())
            ->subject($this->manager->get()->getName() . ' | '. $emailing->getSubject());

        $this->mailer->send($sender);
    }

    public function sendUserEmailing(Emailing $emailing, array $users)
    {
        foreach ($users as $user) {
            $sender = $this->mailer->createEmail('mails/newsletter/user.twig', [
                'emailing' => $emailing,
                'user' => $user
            ])
                ->to($user->getEmail())
                ->subject($this->manager->get()->getName() . ' | ' . $emailing->getSubject());

            $this->mailer->send($sender);
        }
    }

    public function sendPartnerEmailing(Emailing $emailing, array $partners)
    {
        foreach ($partners as $partner) {
            $sender = $this->mailer->createEmail('mails/newsletter/partner.twig', [
                'emailing' => $emailing,
                'partner' => $partner
            ])->to($partner->getEmail())
                ->subject($this->manager->get()->getName() . ' | ' . $emailing->getSubject());

            $this->mailer->send($sender);
        }
    }

    public function sendNewsletterEmailing(Emailing $emailing, array $newsletters)
    {
        foreach ($newsletters as $newsletter) {
            $sender = $this->mailer->createEmail('mails/newsletter/newsletter.twig', ['emailing' => $emailing])
                ->to($newsletter->getEmail())
                ->subject($this->manager->get()->getName() . ' | ' . $emailing->getSubject());

            $this->mailer->send($sender);
        }
    }
}