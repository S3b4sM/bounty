<?php

declare(strict_types=1);

namespace sm\bounty\bounty\command;

use pocketmine\command\CommandSender;

interface BountySubCommand
{
    
    /**
     * @param CommandSender $sender
     * @param array $args
     */
    public function execute(CommandSender $sender, array $args): void;
}
