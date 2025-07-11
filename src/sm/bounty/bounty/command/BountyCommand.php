<?php

declare(strict_types=1);

namespace sm\bounty\bounty\command;

use sm\bounty\bounty\command\subcommand\SetSubCommand;
use sm\bounty\bounty\command\subcommand\TrackerSubCommand;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class BountyCommand extends Command
{
    
    /** @var BountySubCommand[] */
    private array $subCommands = [];
    
    /**
     * BountyCommand construct.
     */
    public function __construct()
    {
        parent::__construct('bounty', 'bounty commands');
        $this->setPermission("bounty.command");
        
        $this->subCommands['set'] = new SetSubCommand;
        //$this->subCommands['tracker'] = new TrackerSubCommand;
    }
    
    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!isset($args[0])) {
            $sender->sendMessage(TextFormat::colorize('&cUse /bounty set [player] [price]'));
            return;
        }
        $subCommand = $this->subCommands[$args[0]] ?? null;
        
        if ($subCommand === null) {
            $sender->sendMessage(TextFormat::colorize('&cThis sub command does not exist'));
            return;
        }
        array_shift($args);
        $subCommand->execute($sender, $args);
    }
}