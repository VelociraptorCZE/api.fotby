<?php
declare(strict_types=1);

namespace App\Transformer;

use App\Entity\Player;

class PlayerTransformer
{
    public function transform(Player $player): array
    {
        return [
            'createdAt' => $player->getCreatedAt()->format('j. n. Y'),
            'balance' => $player->getPlayerAccount()->getBalance(),
            'equippedSkin' => $player->getPlayerAccount()->getEquippedSkin(),
            'skins' => $player->getPlayerAccount()->getSkins()
        ];
    }
}