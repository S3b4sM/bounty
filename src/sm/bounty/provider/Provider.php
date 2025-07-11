<?php

declare(strict_types=1);

namespace sm\bounty\provider;

use sm\bounty\Loader;
use pocketmine\utils\Config;

/**
 * Class Provider
 * @package sm\bounty\provider
 */
class Provider
{

    /** @var Config */
    public Config $treasureConfig, $kothConfig, $claimConfig, $kitConfig, $vkitConfig, $reclaimConfig, $crateConfig, $packageConfig, $shopConfig;

    /**
     * YamlProvider construct
     */
    public function __construct()
    {
        $plugin = Loader::getInstance();

        # Creation of folders that do not exist
        if (!is_dir($plugin->getDataFolder() . 'database'))
            @mkdir($plugin->getDataFolder() . 'database');

        if (!is_dir($plugin->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'bountys'))
            @mkdir($plugin->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'bountys');

        # Save default config
        $plugin->saveDefaultConfig();
    }

    public function save(): void {
        $this->saveBountys();
    }

    public function getBountys(): array
    {
        $bountys = [];

        foreach (glob(Loader::getInstance()->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'bountys' . DIRECTORY_SEPARATOR . '*.yml') as $file)
            $bountys[basename($file, '.yml')] = (new Config(Loader::getInstance()->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'bountys' . DIRECTORY_SEPARATOR . basename($file), Config::YAML))->getAll();
        return $bountys;
    }

    public function saveBountys(): void
    {
        foreach (Loader::getInstance()->getBountyManager()->getBountys() as $name => $bounty) {
            $config = new Config(Loader::getInstance()->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'bountys' . DIRECTORY_SEPARATOR . $name . '.yml', Config::YAML);
            $config->setAll($bounty->getData());
            $config->save();
        }
    }
}