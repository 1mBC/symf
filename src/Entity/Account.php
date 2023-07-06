<?php

namespace App\Entity;

use App\Repository\AccountRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AccountRepository::class)]
class Account
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToMany(targetEntity: Choice::class, inversedBy: 'account')]
    private Collection $choices;

    #[ORM\OneToOne(mappedBy: 'account', cascade: ['persist', 'remove'])]
    private ?User $user = null;

    #[ORM\Column]
    private ?int $credit = 0;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $subs = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $picurl = null;

    #[ORM\Column(length: 255)]
    private ?string $season = "summer";

    //TO ADD A CREATED AT CHECK : https://symfony.com/doc/current/doctrine.html#doctrine-extensions-timestampable-translatable-etc

    public function __construct()
    {
        $this->choices = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    /**
     * @return Collection<int, Choice>
     */
    public function getChoices(): Collection
    {
        return $this->choices;
    }

    public function addChoice(Choice $choice): static
    {
        if (!$this->choices->contains($choice)) {
            $this->choices->add($choice);
            $choice->addAccount($this);
        }

        return $this;
    }

    public function removeChoice(Choice $choice): static
    {
        if ($this->choices->removeElement($choice)) {
            $choice->removeAccount($this);
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        // set the owning side of the relation if necessary
        if ($user->getAccount() !== $this) {
            $user->setAccount($this);
        }

        $this->user = $user;

        return $this;
    }

    public function getCredit(): ?int
    {
        return $this->credit;
    }

    public function setCredit(int $credit): static
    {
        $this->credit = $credit;

        return $this;
    }

    public function getSubs(): ?\DateTimeInterface
    {
        return $this->subs;
    }

    public function setSubs(?\DateTimeInterface $subs): static
    {
        $this->subs = $subs;

        return $this;
    }

    public function getPicurl(): ?string
    {
        return $this->picurl;
    }

    public function setPicurl(?string $picurl): static
    {
        $this->picurl = $picurl;

        return $this;
    }

    public function getSeason(): ?string
    {
        return $this->season;
    }

    public function setSeason(string $season): static
    {
        $this->season = $season;

        return $this;
    }
}
