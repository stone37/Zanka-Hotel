<?php

namespace App\Entity;

use ApiPlatform\Action\NotFoundAction;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Api\Controller\GetSettings;
use App\Entity\Traits\MediaTrait;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\SettingsRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[Vich\Uploadable]
#[ORM\Entity(repositoryClass: SettingsRepository::class)]
#[GetCollection(
    uriTemplate: '/settings/get',
    controller: GetSettings::class,
    openapiContext: ['summary' => 'Récupère la configuration du site'],
    paginationEnabled: false,
    normalizationContext: ['groups' => ['settings:read']],
    name: 'get_settings'
)]
#[Get(
    controller: NotFoundAction::class,
    output: false,
    read: false,
    openapiContext: ['summary' => 'hidden']
)]
class Settings
{
    use MediaTrait;
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('settings:read')]
    private ?int $id = null;

    #[Assert\NotBlank]
    #[ORM\Column(length: 255, nullable: true)]
    #[Groups('settings:read')]
    private ?string $name = null;

    #[Assert\NotBlank]
    #[Assert\Email]
    #[ORM\Column(length: 255, nullable: true)]
    #[Groups('settings:read')]
    private ?string $email = null;

    #[Assert\NotBlank]
    #[ORM\Column(length: 255, nullable: true)]
    #[Groups('settings:read')]
    private ?string $phone = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups('settings:read')]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups('settings:read')]
    private ?string $fax = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups('settings:read')]
    private ?string $address = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups('settings:read')]
    private ?string $country = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups('settings:read')]
    private ?string $city = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups('settings:read')]
    private ?string $facebookAddress = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups('settings:read')]
    private ?string $twitterAddress = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups('settings:read')]
    private ?string $instagramAddress = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups('settings:read')]
    private ?string $youtubeAddress = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups('settings:read')]
    private ?string $linkedinAddress = null;

    #[ORM\Column(nullable: true)]
    private ?bool $activePub = null;

    #[ORM\Column(nullable: true)]
    private ?int $numberHostelPerPage = null;

    #[ORM\Column(nullable: true)]
    private ?int $numberRoomPerPage = null;

    #[ORM\Column(nullable: true)]
    private ?int $numberUserHostelFavoritePerPage = null;

    #[Vich\UploadableField(
        mapping: 'settings',
        fileNameProperty: 'fileName',
        size: 'fileSize',
        mimeType: 'fileMimeType',
        originalName: 'fileOriginalName'
    )]
    private ?File $file = null;

    #[ApiProperty(types: ['https://schema.org/fileUrl'])]
    #[Groups(['settings:read'])]
    private ?string $fileUrl;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups('settings:read')]
    private ?Currency $baseCurrency = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups('settings:read')]
    private ?Locale $defaultLocale = null;

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

    public function getNumberUserHostelFavoritePerPage(): ?int
    {
        return $this->numberUserHostelFavoritePerPage;
    }

    public function setNumberUserHostelFavoritePerPage(?int $numberUserHostelFavoritePerPage): self
    {
        $this->numberUserHostelFavoritePerPage = $numberUserHostelFavoritePerPage;

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

    public function getFileUrl(): ?string
    {
        return $this->fileUrl;
    }

    public function setFileUrl(?string $fileUrl): self
    {
        $this->fileUrl = $fileUrl;

        return $this;
    }

    public function __toString(): string
    {
        return (string) $this->name;
    }

    public function getBaseCurrency(): ?Currency
    {
        return $this->baseCurrency;
    }

    public function setBaseCurrency(Currency $baseCurrency): self
    {
        $this->baseCurrency = $baseCurrency;

        return $this;
    }

    public function getDefaultLocale(): ?Locale
    {
        return $this->defaultLocale;
    }

    public function setDefaultLocale(Locale $defaultLocale): self
    {
        $this->defaultLocale = $defaultLocale;

        return $this;
    }
}
