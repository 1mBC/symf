<?php

namespace App\Entity;

use App\Repository\ChoiceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChoiceRepository::class)]
class Choice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\ManyToMany(targetEntity: Account::class, mappedBy: 'choices')]
    private Collection $account;

    #[ORM\ManyToOne(targetEntity: Criterion::class, inversedBy: 'choices')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Criterion $criterion = null;

    #[ORM\Column]
    private ?int $position = null;

    #[ORM\Column]
    private ?bool $matching = null;

    public function __construct()
    {
        $this->account = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->title;
    }

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

    /**
     * @return Collection<int, Account>
     */
    public function getAccount(): Collection
    {
        return $this->account;
    }

    public function addAccount(Account $account): static
    {
        if (!$this->account->contains($account)) {
            $this->account->add($account);
            $account->addChoice($this);
        }

        return $this;
    }

    public function removeAccount(Account $account): static
    {
        if ($this->account->removeElement($account)) {
            $account->removeChoice($this);  // Make sure to update the Choice on the Account side
        }
    
        return $this;
    }

    public function getCriterion(): ?Criterion
    {
        return $this->criterion;
    }

    public function setCriterion(?Criterion $criterion): static
    {
        $this->criterion = $criterion;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): static
    {
        $this->position = $position;

        return $this;
    }

    public function isMatching(): ?bool
    {
        return $this->matching;
    }

    public function setMatching(bool $matching): static
    {
        $this->matching = $matching;

        return $this;
    }
}
