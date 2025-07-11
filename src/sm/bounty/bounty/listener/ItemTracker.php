<?php 

namespace sm\bounty\bounty\listener;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\utils\TextFormat;
use pocketmine\block\VanillaBlocks;
use pocketmine\block\utils\MobHeadType;

use sm\bounty\Loader;
use sm\bounty\bounty\Bounty;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use muqsit\invmenu\type\InvMenuTypeIds;

class ItemTracker implements Listener
{
    private $targetPlayer;

    public function onPlayerMove(PlayerMoveEvent $event) {
        $player1 = $event->getPlayer();
        $hand = $player1->getInventory()->getItemInHand();
        if($hand->getNamedTag()->getTag("tracker")){
            $player2 = $hand->getNamedTag()->getString("tracker");
            $this->targetPlayer = Loader::getInstance()->getServer()->getPlayerExact($player2);
            $bounty = Loader::getInstance()->getBountyManager()->getBounty($player2);
            if ($bounty === null) {
                $player1->sendPopup(TextFormat::colorize("&cEl Jugador ".$player2." No tiene un Bounty Activo!"));
                return;
            }
            if ($this->targetPlayer === null) {
                $player1->sendPopup(TextFormat::RED."Player is not Online!");
                return;  
            }
            if ($this->targetPlayer->isOnline()) {
                $player1->sendPopup(TextFormat::colorize($this->targetPlayer->getName()." &bX:".$this->targetPlayer->getPosition()->getFloorX()." &bY:".$this->targetPlayer->getPosition()->getFloorY()." &bZ:".$this->targetPlayer->getPosition()->getFloorZ()));
            } else {
                $player1->sendPopup(TextFormat::colorize($this->targetPlayer->getName()." &cNot Online!"));
            }
        }
    }

    public function onPlayerInteract(PlayerInteractEvent $event) {
        $player = $event->getPlayer();
        $hand = $player->getInventory()->getItemInHand();
        if($hand->getNamedTag()->getTag("tracker_item")){
            $event->cancel();
            $menu = InvMenu::create(InvMenuTypeIds::TYPE_DOUBLE_CHEST);
            $menu->setName(TextFormat::colorize("&r&bBountys"));
            $bountyManager = Loader::getInstance()->getBountyManager();
            $bountyManager = Loader::getInstance()->getBountyManager();
            $todosLosBountys = $bountyManager->getBountys(); // array con todos los bountys

            usort($todosLosBountys, function(Bounty $a, Bounty $b){
                return $b->getPrice() <=> $a->getPrice();
            });

            $bountysAMostrar = [];

            if(count($todosLosBountys) > 54) {
                $bountysAMostrar = array_slice($todosLosBountys, 0, 54);
            } else {
                $bountysAMostrar = $todosLosBountys;
            }

            foreach($bountysAMostrar as $bounty){ 
                $skull = VanillaBlocks::MOB_HEAD()->setMobHeadType(MobHeadType::PLAYER())->asItem();
    
                $itemName = TextFormat::BOLD . $bounty->getPlayer();
                $lore = [
                    TextFormat::colorize("&7Sender: " . $bounty->getSender()),
                    TextFormat::colorize("&7Price: $" . $bounty->getPrice())
                ];
    
                $skull->setCustomName($itemName);
                $skull->setLore($lore);
    
                $menu->getInventory()->addItem($skull);
            }
            $menu->setListener(function(InvMenuTransaction $transaction): InvMenuTransactionResult{
                $player = $transaction->getPlayer();
                $itemClicked = $transaction->getItemClicked();


                $trackerItem = $player->getInventory()->getItemInHand();
                $namedtag = $trackerItem->getNamedTag();
                $bounty = $itemClicked->getCustomName();
                $namedtag->setString('tracker', TextFormat::clean($bounty));
                $trackerItem->setNamedTag($namedtag);
                $player->getInventory()->setItemInHand($trackerItem);
                $player->sendMessage(TextFormat::colorize("&5&l[BOUNTY] &r&eYou have set the position of player &a".TextFormat::clean($bounty)." &ein your Tracker"));
                $player->removeCurrentWindow();
                return $transaction->discard();
            });
            $menu->send($player);
        }
    }
}