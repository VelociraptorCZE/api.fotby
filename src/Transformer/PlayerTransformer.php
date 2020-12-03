<?php
declare(strict_types=1);

namespace App\Transformer;

use App\Entity\Player;
use InvalidArgumentException;

class PlayerTransformer
{
    public function transform(Player $player): array
    {
        $playerAccount = $player->getPlayerAccount();

        if ($playerAccount === null) {
            throw new InvalidArgumentException('Could not get player data properly');
        }

        return [
            'createdAt' => $player->getCreatedAt()->format('j. n. Y'),
            'balance' => $playerAccount->getBalance(),
            'equippedSkin' => $playerAccount->getEquippedSkin(),
            'skins' => $playerAccount->getSkins()
        ];
    }
}