<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Put;
use App\Api\Controller\UserAccount;
use App\Api\Controller\UserAccountCancel;
use App\Api\Controller\UserAccountDelete;
use App\Api\Controller\UserAccountProfilPhoto;
use App\Api\State\UserStateProcessor;
use App\Entity\Traits\DeletableTrait;
use App\Entity\Traits\MediaTrait;
use App\Entity\Traits\Notifiable;
use App\Entity\Traits\SocialLoggableTrait;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\UserRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Serializable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[Vich\Uploadable]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['phone'], message: 'Il existe déjà un compte avec cet numéro de téléphone.')]
#[UniqueEntity(fields: ['email'], repositoryMethod: 'findByCaseInsensitive', message: 'Il existe déjà un compte avec cet e-mail.')]
#[UniqueEntity(fields: ['username'], repositoryMethod: 'findByCaseInsensitive')]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[GetCollection(
    openapiContext: ['summary' => 'Récupère tous les utilisateurs', 'security' => [['bearerAuth' => []]]],
    paginationClientItemsPerPage: true,
    normalizationContext: ['groups' => ['user:read']],
    security: 'is_granted("ROLE_ADMIN")'
)]
#[Get(
    openapiContext: ['summary' => 'Récupère un utilisateur', 'security' => [['bearerAuth' => []]]],
    normalizationContext: ['groups' => ['user:read']],
    security: 'is_granted("ROLE_USER")'
)]
#[Post(
    openapiContext: ['summary' => 'Créer un nouveau utilisateur'],
    normalizationContext: ['groups' => ['user:read']],
    denormalizationContext: ['groups' => ['user:write'], 'swagger_definition_name' => 'UserCreated'],
    validationContext: ['groups' => ['Default', 'Registration', 'Api']],
    processor: UserStateProcessor::class
)]
#[Put(
    openapiContext: ['summary' => 'Met à jour un utilisateur', 'security' => [['bearerAuth' => []]]],
    normalizationContext: ['groups' => ['user:read']],
    denormalizationContext: ['groups' => ['user:edit']],
    security: 'is_granted("ROLE_USER")',
    validationContext: ['groups' => ['Registration', 'Profile']],
    processor: UserStateProcessor::class
)]
#[Post(
    uriTemplate: '/users/social/register',
    openapiContext: ['summary' => 'Créer un nouveau utilisateur à partie des comptes Google et Facebook'],
    normalizationContext: ['groups' => ['user:read']],
    denormalizationContext: ['groups' => ['user:social:write'], 'swagger_definition_name' => 'UserCreated'],
    validationContext: ['groups' => ['Registration']],
    processor: UserStateProcessor::class
)]
#[GetCollection(
    uriTemplate: '/users/enabled',
    controller: UserAccount::class,
    openapiContext: ['summary' => 'Récupère d\'utilisateur connecter', 'security' => [['bearerAuth' => []]]],
    paginationEnabled: false,
    normalizationContext: ['groups' => ['user:read'], 'skip_null_values' => false],
    security: 'is_granted("ROLE_USER")',
    name: 'get_owner'
)]
#[Patch(
    uriTemplate: '/users/{id}/password-change',
    requirements: ['id' => '\d+'],
    openapiContext: ['summary' => 'Modifie le mot de passe d\'un utilisateur', 'security' => [['bearerAuth' => []]]],
    normalizationContext: ['groups' => ['user:read']],
    denormalizationContext: ['groups' => ['user:delete']],
    security: 'is_granted("ROLE_USER")',
    name: 'change_password',
    processor: UserStateProcessor::class
)]
#[Put(
    uriTemplate: '/users/{id}/delete/cancel',
    requirements: ['id' => '\d+'],
    controller: UserAccountCancel::class,
    openapiContext: ['summary' => 'Annule la suppression d\'un utilisateur', 'security' => [['bearerAuth' => []]]],
    normalizationContext: ['groups' => ['user:read']],
    denormalizationContext: ['groups' => ['user:cancel'], 'skip_null_values' => false],
    security: 'is_granted("ROLE_USER")',
    name: 'soft_delete_cancel',
)]
#[Patch(
    uriTemplate: '/users/{id}/delete',
    requirements: ['id' => '\d+'],
    controller: UserAccountDelete::class,
    openapiContext: ['summary' => 'Supprime un utilisateur', 'security' => [['bearerAuth' => []]]],
    normalizationContext: ['groups' => ['user:read']],
    denormalizationContext: ['groups' => ['user:delete']],
    security: 'is_granted("ROLE_USER")',
    name: 'soft_delete',
)]
#[Post(
    uriTemplate: '/users/{id}/profil-photo',
    requirements: ['id' => '\d+'],
    controller: UserAccountProfilPhoto::class,
    openapiContext: [
        'summary' => 'Supprime un utilisateur',
        'security' => [['bearerAuth' => []]],
        'requestBody' => [
            'content' => [
                'multipart/form-data' => [
                    'schema' => [
                        'type' => 'object',
                        'properties' => [
                            'file' => [
                                'type' => 'string',
                                'format' => 'binary'
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ],
    normalizationContext: ['groups' => ['user:read']],
    security: 'is_granted("ROLE_USER")',
    deserialize: false,
    name: 'profil_photo'
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface, Serializable
{
    use MediaTrait;
    use Notifiable;
    use SocialLoggableTrait;
    use DeletableTrait;
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:read', 'hostel:read'])]
    private ?int $id = null;

    #[Assert\NotBlank(message: "Entrez une adresse e-mail s'il vous plait.", groups: ['Registration', 'Profile'])]
    #[Assert\Length(min: 2, max: 180, minMessage: "L'adresse e-mail est trop courte.", maxMessage: "L'adresse e-mail est trop longue.", groups: ['Registration', 'Profile'])]
    #[Assert\Email(message: "L'adresse e-mail est invalide.", groups: ['Registration', 'Profile'])]
    #[ORM\Column(length: 180, unique: true)]
    #[Groups(['user:write', 'user:social:write', 'user:read', 'user:edit', 'hostel:read'])]
    private ?string $email = null;

    #[ORM\Column]
    #[Groups(['user:read', 'hostel:read'])]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[Assert\NotBlank(groups: ['Api'])]
    #[Assert\Length(min: 8, max: 4096, groups: ['Api'])]
    #[SerializedName('password')]
    #[Groups(['user:write', 'user:edit', 'user:delete'])]
    private $plainPassword;

    #[ORM\Column(length: 180, nullable: true)]
    #[Groups(['user:read', 'user:edit', 'hostel:read'])]
    private ?string $username = null;

    #[Assert\NotBlank(message: "Entrez un prénom s'il vous plait.", groups: ['Registration', 'Profile'])]
    #[Assert\Length(min: 2, max: 180, minMessage: "Le prénom est trop court", maxMessage: "Le prénom est trop long.", groups: ['Registration', 'Profile'])]
    #[ORM\Column(length: 180, nullable: true)]
    #[Groups(['user:write', 'user:social:write', 'user:read', 'user:edit', 'hostel:read'])]
    private ?string $firstname = null;

    #[Assert\NotBlank(message: "Entrez un nom s'il vous plait.", groups: ['Registration', 'Profile'])]
    #[Assert\Length(min: 2, max: 180, minMessage: "Le nom est trop court", maxMessage: "Le nom est trop long.", groups: ['Registration', 'Profile'])]
    #[ORM\Column(length: 180, nullable: true)]
    #[Groups(['user:write', 'user:social:write', 'user:read', 'user:edit', 'hostel:read'])]
    private ?string $lastname = null;

    #[Assert\NotBlank(message: "Entrez un numéro de téléphone s''il vous plait.", groups: ['Registration', 'Profile'])]
    #[Assert\Length(min: 10, max: 13, minMessage: "Le numéro de téléphone est trop court.", maxMessage: "Le numéro de téléphone est trop long.", groups: ['Registration', 'Profile'])]
    #[ORM\Column(length: 25, nullable: true)]
    #[Groups(['user:write', 'user:social:write', 'user:read', 'user:edit', 'hostel:read'])]
    private ?string $phone = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?DateTimeInterface $birthDay = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['user:read', 'user:edit', 'hostel:read'])]
    private ?string $country = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['user:read', 'user:edit', 'hostel:read'])]
    private ?string $city = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['user:read', 'user:edit', 'hostel:read'])]
    private ?string $address = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $bannedAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['user:read', 'hostel:read'])]
    private ?string $lastLoginIp = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['user:read', 'hostel:read'])]
    private ?DateTimeInterface $lastLoginAt = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isVerified = false;

    #[ORM\Column(nullable: true)]
    #[Groups(['user:read', 'hostel:read'])]
    private ?bool $subscribedToNewsletter = false;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['user:read', 'hostel:read'])]
    private ?string $confirmationToken;

    #[ORM\Column(nullable: true)]
    #[Groups(['user:read', 'hostel:read'])]
    private ?int $hostelNumber = 3;

    #[Assert\File(maxSize: '8M')]
    #[Vich\UploadableField(
        mapping: 'user',
        fileNameProperty: 'fileName',
        size: 'fileSize',
        mimeType: 'fileMimeType',
        originalName: 'fileOriginalName'
    )]
    private ?File $file = null;

    #[ApiProperty(types: ['https://schema.org/fileUrl'])]
    #[Groups(['user:read', 'hostel:read'])]
    private ?string $fileUrl;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Hostel::class, orphanRemoval: true)]
    private Collection $hostels;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Favorite::class, orphanRemoval: true)]
    private Collection $favorites;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Review::class, orphanRemoval: true)]
    private Collection $reviews;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Booking::class)]
    private Collection $bookings;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Commande::class)]
    private Collection $commandes;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Payout::class)]
    private Collection $payouts;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Supplement::class, cascade: ['remove'])]
    private Collection $supplements;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Taxe::class, cascade: ['remove'])]
    private Collection $taxes;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Promotion::class, cascade: ['remove'])]
    private Collection $promotions;

    public function __construct()
    {
        $this->hostels = new ArrayCollection();
        $this->favorites = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->bookings = new ArrayCollection();
        $this->commandes = new ArrayCollection();
        $this->payouts = new ArrayCollection();
        $this->supplements = new ArrayCollection();
        $this->taxes = new ArrayCollection();
        $this->promotions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';
        //$roles[] = 'ROLE_PARTNER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        $this->plainPassword = null;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): self
    {
        $this->username = trim($username ?: '');

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;

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

    public function getBirthDay(): ?DateTimeInterface
    {
        return $this->birthDay;
    }

    public function setBirthDay(?DateTimeInterface $birthDay): self
    {
        $this->birthDay = $birthDay;

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

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getBannedAt(): ?DateTimeInterface
    {
        return $this->bannedAt;
    }

    public function setBannedAt(?DateTimeInterface $bannedAt): self
    {
        $this->bannedAt = $bannedAt;

        return $this;
    }

    public function isBanned(): bool
    {
        return null !== $this->bannedAt;
    }

    public function getLastLoginIp(): ?string
    {
        return $this->lastLoginIp;
    }

    public function setLastLoginIp(?string $lastLoginIp): self
    {
        $this->lastLoginIp = $lastLoginIp;

        return $this;
    }

    public function getLastLoginAt(): ?DateTimeInterface
    {
        return $this->lastLoginAt;
    }

    public function setLastLoginAt(?DateTimeInterface $lastLoginAt): self
    {
        $this->lastLoginAt = $lastLoginAt;

        return $this;
    }

    public function isIsVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(?bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function isSubscribedToNewsletter(): ?bool
    {
        return $this->subscribedToNewsletter;
    }

    public function setSubscribedToNewsletter(?bool $subscribedToNewsletter): self
    {
        $this->subscribedToNewsletter = $subscribedToNewsletter;

        return $this;
    }

    public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    public function setConfirmationToken(?string $confirmationToken): self
    {
        $this->confirmationToken = $confirmationToken;

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
            $this->updatedAt = new DateTimeImmutable();
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

    public function getHostelNumber(): ?int
    {
        return $this->hostelNumber;
    }

    public function setHostelNumber(?int $hostelNumber): self
    {
        $this->hostelNumber = $hostelNumber;

        return $this;
    }

    /**
     * @return Collection<int, Hostel>
     */
    public function getHostels(): Collection
    {
        return $this->hostels;
    }

    public function addHostel(Hostel $hostel): self
    {
        if (!$this->hostels->contains($hostel)) {
            $this->hostels[] = $hostel;
            $hostel->setOwner($this);
        }

        return $this;
    }

    public function removeHostel(Hostel $hostel): self
    {
        if ($this->hostels->removeElement($hostel)) {
            // set the owning side to null (unless already changed)
            if ($hostel->getOwner() === $this) {
                $hostel->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Favorite>
     */
    public function getFavorites(): Collection
    {
        return $this->favorites;
    }

    public function addFavorite(Favorite $favorite): self
    {
        if (!$this->favorites->contains($favorite)) {
            $this->favorites[] = $favorite;
            $favorite->setOwner($this);
        }

        return $this;
    }

    public function removeFavorite(Favorite $favorite): self
    {
        if ($this->favorites->removeElement($favorite)) {
            // set the owning side to null (unless already changed)
            if ($favorite->getOwner() === $this) {
                $favorite->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Review>
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): self
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews[] = $review;
            $review->setOwner($this);
        }

        return $this;
    }

    public function removeReview(Review $review): self
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getOwner() === $this) {
                $review->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Booking>
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addBooking(Booking $booking): self
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings[] = $booking;
            $booking->setOwner($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): self
    {
        if ($this->bookings->removeElement($booking)) {
            // set the owning side to null (unless already changed)
            if ($booking->getOwner() === $this) {
                $booking->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Commande>
     */
    public function getCommandes(): Collection
    {
        return $this->commandes;
    }

    public function addCommande(Commande $commande): self
    {
        if (!$this->commandes->contains($commande)) {
            $this->commandes[] = $commande;
            $commande->setOwner($this);
        }

        return $this;
    }

    public function removeCommande(Commande $commande): self
    {
        if ($this->commandes->removeElement($commande)) {
            // set the owning side to null (unless already changed)
            if ($commande->getOwner() === $this) {
                $commande->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Payout>
     */
    public function getPayouts(): Collection
    {
        return $this->payouts;
    }

    public function addPayout(Payout $payout): self
    {
        if (!$this->payouts->contains($payout)) {
            $this->payouts[] = $payout;
            $payout->setOwner($this);
        }

        return $this;
    }

    public function removePayout(Payout $payout): self
    {
        if ($this->payouts->removeElement($payout)) {
            // set the owning side to null (unless already changed)
            if ($payout->getOwner() === $this) {
                $payout->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Supplement>
     */
    public function getSupplements(): Collection
    {
        return $this->supplements;
    }

    public function addSupplement(Supplement $supplement): self
    {
        if (!$this->supplements->contains($supplement)) {
            $this->supplements[] = $supplement;
            $supplement->setOwner($this);
        }

        return $this;
    }

    public function removeSupplement(Supplement $supplement): self
    {
        if ($this->supplements->removeElement($supplement)) {
            // set the owning side to null (unless already changed)
            if ($supplement->getOwner() === $this) {
                $supplement->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Taxe>
     */
    public function getTaxes(): Collection
    {
        return $this->taxes;
    }

    public function addTaxe(Taxe $taxe): self
    {
        if (!$this->taxes->contains($taxe)) {
            $this->taxes[] = $taxe;
            $taxe->setOwner($this);
        }

        return $this;
    }

    public function removeTaxe(Taxe $taxe): self
    {
        if ($this->taxes->removeElement($taxe)) {
            // set the owning side to null (unless already changed)
            if ($taxe->getOwner() === $this) {
                $taxe->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Promotion>
     */
    public function getPromotions(): Collection
    {
        return $this->promotions;
    }

    public function addPromotion(Promotion $promotion): self
    {
        if (!$this->promotions->contains($promotion)) {
            $this->promotions[] = $promotion;
            $promotion->setOwner($this);
        }

        return $this;
    }

    public function removePromotion(Promotion $promotion): self
    {
        if ($this->promotions->removeElement($promotion)) {
            // set the owning side to null (unless already changed)
            if ($promotion->getOwner() === $this) {
                $promotion->setOwner(null);
            }
        }

        return $this;
    }

    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->email,
            $this->password
        ));
    }

    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->email,
            $this->password
        ) = unserialize($serialized);
    }
}
