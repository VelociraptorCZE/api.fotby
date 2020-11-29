<?php

namespace App\Entity;

use App\Repository\PlayerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PlayerRepository::class)
 */
class Player
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
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="string", length=512)
     */
    private $email;

    /**
     * @ORM\OneToMany(targetEntity=LadderEntry::class, mappedBy="player", orphanRemoval=true)
     */
    private $ladderEntries;

    /**
     * @ORM\OneToOne(targetEntity=PlayerAccount::class, mappedBy="player", cascade={"persist", "remove"})
     */
    private $playerAccount;

    public function __construct()
    {
        $this->ladderEntries = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
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
     * @return Collection|LadderEntry[]
     */
    public function getLadderEntries(): Collection
    {
        return $this->ladderEntries;
    }

    public function addLadderEntry(LadderEntry $ladderEntry): self
    {
        if (!$this->ladderEntries->contains($ladderEntry)) {
            $this->ladderEntries[] = $ladderEntry;
            $ladderEntry->setPlayer($this);
        }

        return $this;
    }

    public function removeLadderEntry(LadderEntry $ladderEntry): self
    {
        if ($this->ladderEntries->removeElement($ladderEntry)) {
            // set the owning side to null (unless already changed)
            if ($ladderEntry->getPlayer() === $this) {
                $ladderEntry->setPlayer(null);
            }
        }

        return $this;
    }

    public function getPlayerAccount(): ?PlayerAccount
    {
        return $this->playerAccount;
    }

    public function setPlayerAccount(PlayerAccount $playerAccount): self
    {
        $this->playerAccount = $playerAccount;

        // set the owning side of the relation if necessary
        if ($playerAccount->getPlayer() !== $this) {
            $playerAccount->setPlayer($this);
        }

        return $this;
    }
}
