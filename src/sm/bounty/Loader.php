<?php

namespace sm\bounty;;

use sm\bounty\bounty\BountyManager;
use sm\bounty\provider\Provider;
use muqsit\invmenu\InvMenuHandler;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;

/**
 * Class Loader
 * @package sm\bounty
 */
class Loader extends PluginBase
{
    
    /** @var Loader */
    public static Loader $instance;
    /** @var Provider */
    public Provider $provider;
    public BountyManager $bountyManager;
    
    
    protected function onLoad(): void
    {
        self::$instance = $this;
    }
    
    protected function onEnable() : void
    {
        if (!InvMenuHandler::isRegistered())
	        InvMenuHandler::register($this);
        

        $this->provider = new Provider;
        $this->bountyManager = new BountyManager;

        
        # Register listener
        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
        
        $this->getScheduler()->scheduleRepeatingTask(new ClosureTask(function (): void {
            # Backup Automatic
            $this->getProvider()->save();

        }), 300 * 20);
    }
    
    protected function onDisable(): void
    {
        $this->getProvider()->save();
    }

    /**
     * @return Loader
     */
    public static function getInstance(): Loader
    {
        return self::$instance;
    }

    /**
     * @return Provider
     */
    public function getProvider(): Provider
    {
        return $this->provider;
    }

    public function getBountyManager(): BountyManager {
        return $this->bountyManager;
    }

}