<?php

namespace App\Entity;

use ApiPlatform\Action\NotFoundAction;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Api\Controller\UserFavorite;
use App\Api\Controller\UserHostelFavorite;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\FavoriteRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: FavoriteRepository::class)]
#[GetCollection(
    uriTemplate: '/users/{id}/favorites',
    controller: UserFavorite::class,
    openapiContext: [
        'summary' => 'Récupère les favoris d\'un utilisateur',
        'security' => [['bearerAuth' => []]],
        'parameters' => [
            [
                'name' => 'id',
                'in' => 'path',
                'description' => 'Id de l\'utilisateur',
                'type' => 'integer',
                'required' => true
            ]
        ]
    ],
    normalizationContext: ['groups' => ['favorite:read'], 'skip_null_values' => false],
    security: 'is_granted("ROLE_USER")'
)]
#[Post(
    openapiContext: ['summary' => 'Crée une nouvelle favoris', 'security' => [['bearerAuth' => []]]],
    normalizationContext: ['groups' => ['favorite:read']],
    denormalizationContext: ['groups' => ['favorite:write']],
    security: 'is_granted("ROLE_USER")'
)]
#[Get(
    controller: NotFoundAction::class,
    output: false,
    read: false,
    openapiContext: ['summary' => 'hidden']
)]
#[GetCollection(
    uriTemplate: '/hostels/{id}/favorite',
    controller: UserHostelFavorite::class,
    openapiContext: [
        'summary' => 'Récupère une favoris',
        'security' => [['bearerAuth' => []]],
        'parameters' => [
            [
                'name' => 'id',
                'in' => 'path',
                'description' => 'Id de l\'hotel',
                'type' => 'integer',
                'required' => true
            ]
        ]
    ],
    normalizationContext: ['groups' => ['favorite:read']],
    security: 'is_granted("ROLE_USER")',
    name: 'get_favoris',
)]
#[Delete(
    openapiContext: ['summary' => 'Supprimer une favoris', 'security' => [['bearerAuth' => []]]],
    security: 'is_granted("ROLE_USER")'
)]
class Favorite
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['favorite:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'favorites')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['favorite:read'])]
    private ?User $owner = null;

    #[ORM\ManyToOne(inversedBy: 'favorites')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['favorite:read', 'favorite:write'])]
    private ?Hostel $hostel = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    /**
     * @param User|UserInterface|null $owner
     * @return $this
     */
    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getHostel(): ?Hostel
    {
        return $this->hostel;
    }

    public function setHostel(?Hostel $hostel): self
    {
        $this->hostel = $hostel;

        return $this;
    }
}
