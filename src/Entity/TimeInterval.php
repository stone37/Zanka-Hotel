<?php

namespace App\Entity;

use App\Entity\Traits\TimestampableTrait;
use App\Repository\TimeIntervalRepository;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TimeIntervalRepository::class)]
class TimeInterval
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['hostel:read'])]
    private ?int $id = null;

    #[Assert\NotBlank(message: 'Veuillez sélectionnez d\'heure de début')]
    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    #[Groups(['hostel:read'])]
    private ?DateTimeInterface $start = null;

    #[Assert\GreaterThan(propertyPath: 'start')]
    #[Assert\NotBlank(message: 'Veuillez sélectionnez d\'heure de fin')]
    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    #[Groups(['hostel:read'])]
    private ?DateTimeInterface $end = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStart(): ?DateTimeInterface
    {
        return $this->start;
    }

    public function setStart(?DateTimeInterface $start): self
    {
        $this->start = $start;

        return $this;
    }

    public function getEnd(): ?DateTimeInterface
    {
        return $this->end;
    }

    public function setEnd(?DateTimeInterface $end): self
    {
        $this->end = $end;

        return $this;
    }
}
