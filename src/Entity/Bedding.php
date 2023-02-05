<?php

namespace App\Entity;

use App\Entity\Traits\TimestampableTrait;
use App\Repository\BeddingRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BeddingRepository::class)]
class Bedding
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['hostel:read', 'booking:read'])]
    private ?int $id = null;

    #[Assert\NotBlank]
    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['hostel:read', 'booking:read'])]
    private ?string $name = null;

    #[Assert\NotBlank(message: "Veuillez sélectionner le nombres de lit.")]
    #[ORM\Column(nullable: true)]
    #[Groups(['hostel:read', 'booking:read'])]
    private ?int $number = null;

    #[ORM\ManyToOne(inversedBy: 'beddings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Room $room = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(?int $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getRoom(): ?Room
    {
        return $this->room;
    }

    public function setRoom(?Room $room): self
    {
        $this->room = $room;

        return $this;
    }
}
