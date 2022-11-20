<?php

namespace App\Entity;

use App\Entity\Traits\TimestampableTrait;
use App\Repository\CancelationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CancelationRepository::class)]
class Cancelation
{
    public const CANCEL_STATE_DAY = 0;
    public const CANCEL_STATE_ONE_DAY = 1;
    public const CANCEL_STATE_TWO_DAY = 2;
    public const CANCEL_STATE_THREE_DAY = 3;
    public const CANCEL_STATE_SEVEN_DAY = 7;
    public const CANCEL_STATE_FOURTEEN_DAY = 14;

    public const CANCEL_RESULT_FIRST = 0;
    public const CANCEL_RESULT_SECOND = 1;

    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank]
    #[ORM\Column(nullable: true)]
    private ?int $state = null;

    #[Assert\NotBlank]
    #[ORM\Column(nullable: true)]
    private ?int $result = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getState(): ?int
    {
        return $this->state;
    }

    public function setState(?int $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getResult(): ?int
    {
        return $this->result;
    }

    public function setResult(?int $result): self
    {
        $this->result = $result;

        return $this;
    }
}
