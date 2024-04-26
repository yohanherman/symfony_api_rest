<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BookRepository::class)]
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]

    // avec "groups()" je limite la donnee a retourner
    #[Groups(['getBooks', 'getAuthors'])]

    private ?int $id = null;

    #[ORM\Column(length: 255)]
    // avec "groups()" je limite la donnee a retourner
    #[Groups(['getBooks', 'getAuthors'])]

    #[Assert\NotBlank(message:"le titre du livre est requis")]
    #[Assert\Length(min:4,max:40,minMessage:'votre titre doit avoir au moins {{ limit }} mots ',maxMessage:'votre titre ne peut pas avoir plus de {{limit}} mots')]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    // avec "groups()" je limite la donnee a retourner
    #[Groups(['getBooks', 'getAuthors'])]
    private ?string $coverText = null;

    #[ORM\ManyToOne(inversedBy: 'books')]

    // avec "groups()" je limite la donnee a retourner
    #[Groups('getBooks')]

    // il va me permettre de pas avoir de probleme lors de la suppression de mon auteur, a mettre quand jessaie d'effacer un element d'une autre entité qui est lié a cette entité
    #[ORM\JoinColumn(onDelete: "CASCADE")]
    private ?Author $author = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getCoverText(): ?string
    {
        return $this->coverText;
    }

    public function setCoverText(?string $coverText): static
    {
        $this->coverText = $coverText;

        return $this;
    }

    public function getAuthor(): ?Author
    {
        return $this->author;
    }

    public function setAuthor(?Author $author): static
    {
        $this->author = $author;

        return $this;
    }
}
