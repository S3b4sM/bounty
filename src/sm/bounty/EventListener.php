<?php

declare(strict_types=1);

namespace sm\bounty;

use hcf\player\Player;
use pocketmine\event\entity\EntityDamageByChildEntityEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerCreationEvent;
use pocketmine\utils\TextFormat;

class EventListener implements Listener
{
    /**
     * @param PlayerCreationEvent $event
     */
    public function handleCreation(PlayerCreationEvent $event): void
    {
        $event->setPlayerClass(Player::class);
    }

    /**
     * @param PlayerDeathEvent $event
     */
    public function handleDeath(PlayerDeathEvent $event): void
    {
        /** @var Player */
        $player = $event->getPlayer();

        if (!$player instanceof Player)
            return;
        $last = $player->getLastDamageCause();

        if ($last instanceof EntityDamageByEntityEvent || $last instanceof EntityDamageByChildEntityEvent) {
            $damager = $last->getDamager();

            if ($damager instanceof Player) {
                if (Loader::getInstance()->getBountyManager()->getBounty($player->getName()) !== null){
                    $price = Loader::getInstance()->getBountyManager()->getBounty($player->getName())->getPrice();
                    $damager->getSession()->setBalance($damager->getSession()->getBalance() + (int)$price);
                    $playerName = $damager->getName();
                    $deathName = $player->getName();
                    Loader::getInstance()->getBountyManager()->removeBounty($player->getName());
                    Loader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&4&l[BOUNTY] &r&a$playerName &eha reclamado la recompensa de &c$deathName &epor: &a$$price"));
                }
            }
        }
    }
}