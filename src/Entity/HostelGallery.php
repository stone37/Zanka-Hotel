<?php

namespace App\Entity;

use App\Entity\Traits\PositionTrait;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\HostelGalleryRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: HostelGalleryRepository::class)]
class HostelGallery
{
    use PositionTrait;
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['hostel:read'])]
    private $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['hostel:read'])]
    private ?string $extension = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['hostel:read'])]
    private ?string $name = null;

    #[Assert\File(
        maxSize: '8000k',
        maxSizeMessage: 'Le fichier excède 8000Ko',
        mimeTypes: ['image/png', 'image/jpeg', 'image/jpg', 'image/gif'],
        mimeTypesMessage: 'Format non autorisés. Formats autorisés: png, jpeg, jpg, gif'
    )]
    private ?File $file = null;

    private ?string $tempFilename = null;

    #[ORM\ManyToOne(inversedBy: 'galleries')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Hostel $hostel = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(File $file): self
    {
        $this->file = $file;

        // On vérifie si on avait déjà un fichier pour cette entité
        if (null !== $this->extension) {
            // On sauvegarde l'extension du fichier pour le supprimer plus tard
            $this->tempFilename = $this->extension;

            // On réinitialise les valeurs des attributs url et alt
            $this->extension = null;
            $this->name = null;
        }

        return $this;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function preUpload(): void
    {
        if (null === $this->file) {return;}

        $this->extension = $this->file->guessExtension();
        $this->name = $this->file->getFilename();
    }

    #[ORM\PostPersist]
    #[ORM\PostUpdate]
    public function upload(): void
    {
        if (null === $this->file) {return;}

        if (null !== $this->tempFilename) {
            $oldFile = $this->getUploadRootDir() . '/' . $this->id . '.' . $this->extension;
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
        }

        $this->file->move(
            $this->getUploadRootDir(),
            $this->id . '.' . $this->extension
        );
    }

    #[ORM\PreRemove]
    public function preRemoveUpload(): self
    {
        $this->tempFilename = $this->getUploadRootDir() . '/' . $this->id . '.' . $this->extension;

        return $this;
    }

    #[ORM\PostRemove]
    public function removeUpload(): self
    {
        if (file_exists($this->tempFilename)) {
            unlink($this->tempFilename);
        }

        return $this;
    }

    /**
     * Retourne le chemin relatif vers l'image pour un navigateur
     */
    public function getUploadDir(): string
    {
        return 'uploads/images/gallery/hostel';
    }

    /**
     * Retourne le chemin relatif vers l'image pour le code PHP
     */
    protected function getUploadRootDir(): string
    {
        return __DIR__ . '/../../public/' . $this->getUploadDir();
    }

    #[Groups(['hostel:read'])]
    public function getWebPath()
    {
        return $this->getUploadDir() . '/' . $this->getId() . '.' . $this->getExtension();
    }

    public function getExtension(): ?string
    {
        return $this->extension;
    }

    public function setExtension(string $extension): self
    {
        $this->extension = $extension;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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
