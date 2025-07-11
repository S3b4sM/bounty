<?php

declare(strict_types=1);

namespace sm\bounty\bounty\command\subcommand;

use sm\bounty\bounty\command\BountySubCommand;
use sm\bounty\Loader;
use hcf\player\Player;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class SetSubCommand implements BountySubCommand
{

    /**
     * @param CommandSender $sender
     * @param array $args
     */
    public function execute(CommandSender $sender, array $args): void
    {
        if (!$sender instanceof Player)
            return;

        if (count($args) < 2) {
            $sender->sendMessage(TextFormat::colorize('&cUse /bounty set [target: name] [int: price]'));
            return;
        }
        $player = $args[0];
        $price = $args[1];
        $targetPlayer = Loader::getInstance()->getServer()->getPlayerByPrefix($player);
        if (!$targetPlayer instanceof Player) {
            $sender->sendMessage(TextFormat::RED."Jugador $player no encontrado!");
            return;  
        }
        if (Loader::getInstance()->getBountyManager()->getBounty($player) !== null) {
            $sender->sendMessage(TextFormat::colorize("&cEl jugador $player ya tiene un bounty"));
            return;
        }
        if ($sender->getSession()->getBalance() < (int)$price) {
            $sender->sendMessage(TextFormat::colorize('&cNo tienes dinero suficiente'));
            return;
        }     
        Loader::getInstance()->getBountyManager()->createBounty($player, [
            'price' => $price,
            'sender' => $sender->getName(),
        ]);
        $senderName = $sender->getName();
        $targetPlayerName = $targetPlayer->getName();
        $sender->getSession()->setBalance($sender->getSession()->getBalance() - $price);
        $sender->sendMessage(TextFormat::colorize("&5&l[BOUNTY] &r&eLe pusiste recompensa por &a$$price &eal jugador &a$targetPlayerName&e!"));
        Loader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&5&l[BOUNTY] &r&2$senderName &eha ofrecido &2$$price &epor matar a &c$targetPlayerName"));
    }
}