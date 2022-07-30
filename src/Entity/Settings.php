<?php

namespace App\Entity;

use App\Entity\Traits\MediaTrait;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\SettingsRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: SettingsRepository::class)]
class Settings
{
    use MediaTrait;
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[Assert\NotBlank]
    #[Assert\Email]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email = null;

    #[Assert\NotBlank]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fax = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $address = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $country = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $facebookAddress = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $twitterAddress = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $instagramAddress = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $youtubeAddress = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $linkedinAddress = null;

    #[ORM\Column(nullable: true)]
    private ?bool $activeCredit = null;

    #[ORM\Column(nullable: true)]
    private ?bool $activeCardPayment = null;

    #[ORM\Column(nullable: true)]
    private ?bool $activePub = null;

    #[ORM\Column(nullable: true)]
    private ?bool $activeParrainage = null;

    #[ORM\Column(nullable: true)]
    private ?int $numberHostelPerPage = null;

    #[ORM\Column(nullable: true)]
    private ?int $numberRoomPerPage = null;

    #[ORM\Column(nullable: true)]
    private ?int $numberUserRoomPerPage = null;

    #[ORM\Column(nullable: true)]
    private ?int $numberUserHostelFavoritePerPage = null;

    #[ORM\Column(nullable: true)]
    private ?int $parrainCreditOffer = null;

    #[ORM\Column]
    private ?int $fioleCreditOffer = null;

    #[ORM\Column(nullable: true)]
    private ?bool $activeRegisterDrift = null;

    #[ORM\Column(nullable: true)]
    private ?int $registerDriftCreditOffer = null;

    #[ORM\Column(nullable: true)]
    private ?int $parrainageNumberBookingRequired = null;

    #[ORM\Column(nullable: true)]
    private ?int $registerDriftNumberBookingRequired = null;

    #[Vich\UploadableField(
        mapping: 'settings',
        fileNameProperty: 'fileName',
        size: 'fileSize',
        mimeType: 'fileMimeType',
        originalName: 'fileOriginalName'
    )]
    private ?File $file;

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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getFax(): ?string
    {
        return $this->fax;
    }

    public function setFax(?string $fax): self
    {
        $this->fax = $fax;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getFacebookAddress(): ?string
    {
        return $this->facebookAddress;
    }

    public function setFacebookAddress(?string $facebookAddress): self
    {
        $this->facebookAddress = $facebookAddress;

        return $this;
    }

    public function getTwitterAddress(): ?string
    {
        return $this->twitterAddress;
    }

    public function setTwitterAddress(?string $twitterAddress): self
    {
        $this->twitterAddress = $twitterAddress;

        return $this;
    }

    public function getInstagramAddress(): ?string
    {
        return $this->instagramAddress;
    }

    public function setInstagramAddress(?string $instagramAddress): self
    {
        $this->instagramAddress = $instagramAddress;

        return $this;
    }

    public function getYoutubeAddress(): ?string
    {
        return $this->youtubeAddress;
    }

    public function setYoutubeAddress(?string $youtubeAddress): self
    {
        $this->youtubeAddress = $youtubeAddress;

        return $this;
    }

    public function getLinkedinAddress(): ?string
    {
        return $this->linkedinAddress;
    }

    public function setLinkedinAddress(?string $linkedinAddress): self
    {
        $this->linkedinAddress = $linkedinAddress;

        return $this;
    }

    public function setActiveAdFavorite(?bool $activeAdFavorite): self
    {
        $this->activeAdFavorite = $activeAdFavorite;

        return $this;
    }

    public function isActiveCredit(): ?bool
    {
        return $this->activeCredit;
    }

    public function setActiveCredit(?bool $activeCredit): self
    {
        $this->activeCredit = $activeCredit;

        return $this;
    }

    public function isActiveCardPayment(): ?bool
    {
        return $this->activeCardPayment;
    }

    public function setActiveCardPayment(?bool $activeCardPayment): self
    {
        $this->activeCardPayment = $activeCardPayment;

        return $this;
    }

    public function isActivePub(): ?bool
    {
        return $this->activePub;
    }

    public function setActivePub(?bool $activePub): self
    {
        $this->activePub = $activePub;

        return $this;
    }

    public function isActiveParrainage(): ?bool
    {
        return $this->activeParrainage;
    }

    public function setActiveParrainage(?bool $activeParrainage): self
    {
        $this->activeParrainage = $activeParrainage;

        return $this;
    }

    public function getNumberHostelPerPage(): ?int
    {
        return $this->numberHostelPerPage;
    }

    public function setNumberHostelPerPage(?int $numberHostelPerPage): self
    {
        $this->numberHostelPerPage = $numberHostelPerPage;

        return $this;
    }

    public function getNumberRoomPerPage(): ?int
    {
        return $this->numberRoomPerPage;
    }

    public function setNumberRoomPerPage(?int $numberRoomPerPage): self
    {
        $this->numberRoomPerPage = $numberRoomPerPage;

        return $this;
    }

    public function getNumberUserRoomPerPage(): ?int
    {
        return $this->numberUserRoomPerPage;
    }

    public function setNumberUserRoomPerPage(?int $numberUserRoomPerPage): self
    {
        $this->numberUserRoomPerPage = $numberUserRoomPerPage;

        return $this;
    }

    public function getNumberUserHostelFavoritePerPage(): ?int
    {
        return $this->numberUserHostelFavoritePerPage;
    }

    public function setNumberUserHostelFavoritePerPage(?int $numberUserHostelFavoritePerPage): self
    {
        $this->numberUserHostelFavoritePerPage = $numberUserHostelFavoritePerPage;

        return $this;
    }

    public function getParrainCreditOffer(): ?int
    {
        return $this->parrainCreditOffer;
    }

    public function setParrainCreditOffer(?int $parrainCreditOffer): self
    {
        $this->parrainCreditOffer = $parrainCreditOffer;

        return $this;
    }

    public function getFioleCreditOffer(): ?int
    {
        return $this->fioleCreditOffer;
    }

    public function setFioleCreditOffer(int $fioleCreditOffer): self
    {
        $this->fioleCreditOffer = $fioleCreditOffer;

        return $this;
    }

    public function isActiveRegisterDrift(): ?bool
    {
        return $this->activeRegisterDrift;
    }

    public function setActiveRegisterDrift(?bool $activeRegisterDrift): self
    {
        $this->activeRegisterDrift = $activeRegisterDrift;

        return $this;
    }

    public function getRegisterDriftCreditOffer(): ?int
    {
        return $this->registerDriftCreditOffer;
    }

    public function setRegisterDriftCreditOffer(?int $registerDriftCreditOffer): self
    {
        $this->registerDriftCreditOffer = $registerDriftCreditOffer;

        return $this;
    }

    public function getParrainageNumberBookingRequired(): ?int
    {
        return $this->parrainageNumberBookingRequired;
    }

    public function setParrainageNumberBookingRequired(?int $parrainageNumberBookingRequired): self
    {
        $this->parrainageNumbreBookingRequiere = $parrainageNumberBookingRequired;

        return $this;
    }

    public function getRegisterDriftNumberBookingRequired(): ?int
    {
        return $this->registerDriftNumberBookingRequired;
    }

    public function setRegisterDriftNumberBookingRequired(?int $registerDriftNumberBookingRequired): self
    {
        $this->registerDriftNumberBookingRequired = $registerDriftNumberBookingRequired;

        return $this;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(?File $file): self
    {
        $this->file = $file;

        if (null !== $file) {
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    public function __toString(): string
    {
        return (string) $this->name;
    }
}
