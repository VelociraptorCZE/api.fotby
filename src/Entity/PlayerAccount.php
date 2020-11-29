<?php

namespace App\Entity;

use App\Repository\PlayerAccountRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PlayerAccountRepository::class)
 */
class PlayerAccount
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=Player::class, inversedBy="playerAccount", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $player;

    /**
     * @ORM\Column(type="integer")
     */
    private $balance;

    /**
     * @ORM\Column(type="json")
     */
    private $skins = ['SKIN_COVID_BALL', 'SKIN_REGULAR_BALL'];

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $equippedSkin;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlayer(): ?Player
    {
        return $this->player;
    }

    public function setPlayer(Player $player): self
    {
        $this->player = $player;

        return $this;
    }

    public function getBalance(): ?int
    {
        return $this->balance;
    }

    public function setBalance(int $balance): self
    {
        $this->balance = $balance;

        return $this;
    }

    public function getSkins(): ?array
    {
        return $this->skins;
    }

    public function setSkins(array $skins): self
    {
        $this->skins = $skins;

        return $this;
    }

    public function getEquippedSkin(): ?string
    {
        return $this->equippedSkin;
    }

    public function setEquippedSkin(string $equippedSkin): self
    {
        $this->equippedSkin = $equippedSkin;

        return $this;
    }
}
