<?php

namespace App\Entity;

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
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[Vich\Uploadable]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['phone'], message: 'Il existe déjà un compte avec cet numéro de téléphone.')]
#[UniqueEntity(fields: ['email'], repositoryMethod: 'findByCaseInsensitive', message: 'Il existe déjà un compte avec cet e-mail.')]
#[UniqueEntity(fields: ['username'], repositoryMethod: 'findByCaseInsensitive')]
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use MediaTrait;
    use Notifiable;
    use SocialLoggableTrait;
    use DeletableTrait;
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank(message: "Entrez une adresse e-mail s'il vous plait.", groups: ['Registration', 'Profile'])]
    #[Assert\Length(min: 2, max: 180, minMessage: "L'adresse e-mail est trop courte.", maxMessage: "L'adresse e-mail est trop longue.", groups: ['Registration', 'Profile'])]
    #[Assert\Email(message: "L'adresse e-mail est invalide.", groups: ['Registration', 'Profile'])]
    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 8, max: 4096, minMessage: 'Votre mot de passe doit comporter au moins {{ limit }} caractères')]
    #[SerializedName('password')]
    private $plainPassword;

    #[Assert\NotBlank(message: "Entrez un nom d'utilisateur s'il vous plait.", groups: ['Registration', 'Profile'])]
    #[Assert\Length(min: 2, max: 180, minMessage: "Le nom d'utilisateur est trop courte.", maxMessage: "Le nom d'utilisateur est trop longue.", groups: ['Registration', 'Profile'])]
    #[ORM\Column(length: 180, nullable: true)]
    private ?string $username = null;

    #[Assert\NotBlank(message: "Entrez un prénom s'il vous plait.", groups: ['Registration', 'Profile'])]
    #[Assert\Length(min: 2, max: 180, minMessage: "Le prénom est trop court", maxMessage: "Le prénom est trop long.", groups: ['Registration', 'Profile'])]
    #[ORM\Column(length: 180, nullable: true)]
    private ?string $firstname = null;

    #[Assert\NotBlank(message: "Entrez un nom s'il vous plait.", groups: ['Registration', 'Profile'])]
    #[Assert\Length(min: 2, max: 180, minMessage: "Le nom est trop court", maxMessage: "Le nom est trop long.", groups: ['Registration', 'Profile'])]
    #[ORM\Column(length: 180, nullable: true)]
    private ?string $lastname = null;

    #[Assert\NotBlank(message: "Entrez un numéro de téléphone s''il vous plait.", groups: ['Registration', 'Profile'])]
    #[Assert\Length(min: 10, max: 25, minMessage: "Le numéro de téléphone est trop court.", maxMessage: "Le numéro de téléphone est trop long.", groups: ['Registration', 'Profile'])]
    #[ORM\Column(length: 25, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?DateTimeInterface $birthDay = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $country = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $address = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $bannedAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lastLoginIp = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $lastLoginAt = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isVerified = null;

    #[ORM\Column(nullable: true)]
    private ?bool $subscribedToNewsletter = false;

    #[Assert\File(maxSize: '8M')]
    #[Vich\UploadableField(
        mapping: 'user',
        fileNameProperty: 'fileName',
        size: 'fileSize',
        mimeType: 'fileMimeType',
        originalName: 'fileOriginalName'
    )]
    private ?File $file = null;

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

    public function __construct()
    {
        $this->hostels = new ArrayCollection();
        $this->favorites = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->bookings = new ArrayCollection();
        $this->commandes = new ArrayCollection();
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
}
