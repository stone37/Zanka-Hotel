<?php

namespace App\Data;

use App\Entity\Occupant;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use App\Service\BookerService as Booker;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

class BookingData
{
    #[Assert\NotBlank]
    public string $location = '';

    public array $duration;

    public int $adult = Booker::INIT_ADULT;

    public int $children = Booker::INIT_CHILDREN;

    public int $roomNumber = Booker::INIT_ROOM;

    public ?string $message = '';

    #[Assert\NotBlank(groups: ['booking'])]
    public ?string $firstname = '';

    #[Assert\NotBlank(groups: ['booking'])]
    public ?string $lastname = '';

    #[Assert\Email(message: "L'adresse e-mail est invalide.", groups: ['booking'])]
    #[Assert\NotBlank(groups: ['booking'])]
    public ?string $email = '';

    #[Assert\NotBlank(message: "Entrez un numéro de téléphone s''il vous plait.", groups: ['booking'])]
    #[Assert\Length(min: 10, max: 20, minMessage: "Le numéro de téléphone est trop court.", maxMessage: "Le numéro de téléphone est trop long.", groups: ['booking'])]
    public ?string $phone = '';

    public ?string $country = '';

    public ?string $city = '';

    public int $night;

    public int $amount;

    public int $taxeAmount = 0;

    public int $discountAmount = 0;

    public ?int $roomId;

    public ?int $optionId;

    public ?int $userId;

    #[Assert\Valid(groups: ['booking'])]
    private Collection $occupants;

    public function __construct()
    {
        $this->duration = [
            'checkin' => (new DateTime())->format('Y-m-d'),
            'checkout' => ((new DateTime())->modify('+1 day'))->format('Y-m-d')
        ];

        $this->occupants = new ArrayCollection();
    }

    /**
     * @return Collection<int, Occupant>
     */
    public function getOccupants(): Collection
    {
        return $this->occupants;
    }

    public function addOccupant(Occupant $occupant): self
    {
        if (!$this->occupants->contains($occupant)) {
            $this->occupants->add($occupant);
        }

        return $this;
    }

    public function removeOccupant(Occupant $occupant): self
    {
        $this->occupants->removeElement($occupant);

        return $this;
    }
}

