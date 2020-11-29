<?php
declare(strict_types=1);

namespace App\Factory;

use App\Entity\Player;
use App\Entity\PlayerAccount;
use DateTime;

class PlayerFactory
{
    public function create(string $username, string $hash, string $email): Player
    {
        $player = new Player;
        $player->setUsername($username);
        $player->setPassword($hash);
        $player->setEmail($email);
        $player->setCreatedAt(new DateTime);

        $playerAccount = new PlayerAccount;
        $playerAccount->setBalance(1);
        $playerAccount->setEquippedSkin('SKIN_COVID_BALL');

        $player->setPlayerAccount($playerAccount);

        return $player;
    }
}