<?php

namespace App\Entity;

use App\Repository\BookRepository;
use App\Schema\BookSchema;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BookRepository::class)
 */
class Book
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $isbn_number;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $author;

    /**
     * @ORM\Column(type="datetime")
     */
    private $published_at;

    /**
     * @ORM\Column(type="integer")
     */
    private $genre;

    /**
     * @ORM\ManyToOne(targetEntity=Member::class, inversedBy="borrowed_books")
     */
    private $borrowed_by;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIsbnNumber(): ?string
    {
        return $this->isbn_number;
    }

    public function setIsbnNumber(string $isbn_number): self
    {
        $this->isbn_number = $isbn_number;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(string $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getPublishedAt(): ?\DateTimeInterface
    {
        return $this->published_at;
    }

    public function setPublishedAt(\DateTimeInterface $published_at): self
    {
        $this->published_at = $published_at;

        return $this;
    }

    public function getGenre(): ?int
    {
        return $this->genre;
    }

    public function setGenre(int $genre): self
    {
        $this->genre = $genre;

        return $this;
    }

    public function getBorrowedBy(): ?Member
    {
        return $this->borrowed_by;
    }

    public function setBorrowedBy(?Member $borrowed_by): self
    {
        $this->borrowed_by = $borrowed_by;

        return $this;
    }

    public function setFromSchema(BookSchema $schema, ?Member $borrower): self
    {
        $this->borrowed_by = $schema->borrowed_by;
        $this->genre = $schema->genre;
        $this->author = $schema->author;
        $this->description = $schema->description;
        $this->isbn_number = $schema->isbn_number;
        $this->title = $schema->title;
        $this->published_at = new \DateTime($schema->published_at);
        $this->setBorrowedBy($borrower);
        return $this;
    }
}
