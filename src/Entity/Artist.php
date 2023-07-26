<?php

namespace App\Entity;

use App\Repository\ArtistRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ArtistRepository::class)]
class Artist
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('artists:readAll')]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Groups('artists:readAll')]
    private ?string $name = null;

    #[ORM\Column(length: 100)]
    #[Groups('artists:readAll')]
    private ?string $style = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups('artists:readAll')]
    private ?string $resume = null;

    #[ORM\Column(length: 100)]
    #[Groups('artists:readAll')]
    private ?string $profilePicture = null;

    #[ORM\OneToMany(mappedBy: 'artist', targetEntity: Tattoo::class)]
    #[Groups('artists:readAll')]
    private Collection $gallery;

    #[ORM\Column(length: 255)]
    #[Groups('artists:readAll')]
    private ?string $link = null;

    public function __construct()
    {
        $this->gallery = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getStyle(): ?string
    {
        return $this->style;
    }

    public function setStyle(string $style): static
    {
        $this->style = $style;

        return $this;
    }

    public function getResume(): ?string
    {
        return $this->resume;
    }

    public function setResume(string $resume): static
    {
        $this->resume = $resume;

        return $this;
    }

    public function getProfilePicture(): ?string
    {
        return $this->profilePicture;
    }

    public function setProfilePicture(string $profilePicture): static
    {
        $this->profilePicture = $profilePicture;

        return $this;
    }

    /**
     * @return Collection<int, Tattoo>
     */
    public function getGallery(): Collection
    {
        return $this->gallery;
    }

    public function addGallery(Tattoo $gallery): static
    {
        if (!$this->gallery->contains($gallery)) {
            $this->gallery->add($gallery);
            $gallery->setArtist($this);
        }

        return $this;
    }

    public function removeGallery(Tattoo $gallery): static
    {
        if ($this->gallery->removeElement($gallery)) {
            // set the owning side to null (unless already changed)
            if ($gallery->getArtist() === $this) {
                $gallery->setArtist(null);
            }
        }

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(string $link): static
    {
        $this->link = $link;

        return $this;
    }
}
