<?php


namespace sm\bounty\bounty;

use sm\bounty\Loader;
use sm\bounty\bounty\command\BountyCommand;
use sm\bounty\bounty\listener\ItemTracker;

class BountyManager 
{
    private array $bountys = [];

    public function __construct()
    {
        Loader::getInstance()->getServer()->getPluginManager()->registerEvents(new ItemTracker(), Loader::getInstance());
        Loader::getInstance()->getServer()->getCommandMap()->register('bounty', new BountyCommand());
        foreach (Loader::getInstance()->getProvider()->getBountys() as $name => $data) {
            $this->createBounty((string) $name, $data);
        }
    }

    public function getBountys(): array
    {
        return $this->bountys;
    }
    
    public function getBounty(string $name): ?Bounty
    {
        return $this->bountys[$name] ?? null;
    }
    
    public function createBounty(string $name, array $data): void
    {
        $this->bountys[$name] = new Bounty($name, $data);
    }
    
    public function removeBounty(string $name): void
    {
        unset($this->bountys[$name]);
        
        if (file_exists(Loader::getInstance()->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'bountys' . DIRECTORY_SEPARATOR . $name . '.yml')) {
            $result = unlink(Loader::getInstance()->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'bountys' . DIRECTORY_SEPARATOR . $name . '.yml');

            if ($result) {
                Loader::getInstance()->getLogger()->debug('Bounty ' . $name . ' file deleted successfully');
            } else {
                Loader::getInstance()->getLogger()->debug('Error for deleted Bounty ' . $name . ' file');
            }
        }
    }
}