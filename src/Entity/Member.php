<?php

namespace App\Entity;

use App\Repository\MemberRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MemberRepository::class)
 */
class Member
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
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=Book::class, mappedBy="borrowed_by")
     */
    private $borrowed_books;

    public function __construct()
    {
        $this->borrowed_books = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return Collection|Book[]
     */
    public function getBorrowedBooks(): Collection
    {
        return $this->borrowed_books;
    }

    public function addBorrowedBook(Book $borrowedBook): self
    {
        if (!$this->borrowed_books->contains($borrowedBook)) {
            $this->borrowed_books[] = $borrowedBook;
            $borrowedBook->setBorrowedBy($this);
        }

        return $this;
    }

    public function removeBorrowedBook(Book $borrowedBook): self
    {
        if ($this->borrowed_books->removeElement($borrowedBook)) {
            // set the owning side to null (unless already changed)
            if ($borrowedBook->getBorrowedBy() === $this) {
                $borrowedBook->setBorrowedBy(null);
            }
        }

        return $this;
    }
}
