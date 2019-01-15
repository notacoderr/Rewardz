<?php

declare(strict_types = 1);

namespace sakura;

use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\plugin\PluginException;

use onebone\economyapi\EconomyAPI;

use skywars\event\PlayerArenaWinEvent as SWEvent;
use vixikhd\onevsone\event\PlayerArenaWinEvent as OneEvent;


class Rewardz extends PluginBase implements Listener {

    public $config;

    public $economyAPI;

    public function onEnable() {
        if(!is_dir($this->getDataFolder())) {
            mkdir($this->getDataFolder());
        }
        if(!is_file($this->getDataFolder() . "/config.yml")) {
            $this->saveResource("/config.yml");
        }

        $this->config = (new Config($this->getDataFolder() . "/config.yml", Config::YAML))->getAll(false);

        if(!class_exists(EconomyAPI::class)) {
            throw new PluginException("Could not load EconomyAPI provider");
        }
        if(!class_exists(SkyWars::class)) {
            throw new PluginException("SkyWars (GamakCZ) plugin was not found!");
        }
        if(!class_exists(OneVsOne::class)) {
            throw new PluginException("OneVsOne (GamakCZ) plugin was not found!");
        }
        if(!class_exists(core::class)) {
            throw new PluginException("CoreX2 (NotACoderr) plugin was not found!");
        }

        $this->economyAPI = EconomyAPI::getInstance();
        $this->coreX = Server::getInstance()->getPluginManager()->getPlugin("CoreX2");
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    /**
     * @param PlayerArenaWinEvent [SW] $event
     */
    function onWin(SWEvent $event) : void {
        $player = $event->getPlayer();
        $player->sendMessage((string) $this->config["SW_message"]);
        $this->economyAPI->addMoney($player, (int)$this->config["SW_reward"]);
        $this->core->calculate->doMagic($player, (int)$this->config["SW_Exp"]); //Exp
        $this->core->elo->increasePoints($player, (int) $this->config["SW_Elo"]); //Elo points
    }
    
    /**
     * @param PlayerArenaWinEvent [OneVsOne] $event
     */
    function onWin(OneEvent $event) : void {
        $player = $event->getPlayer();
        $player->sendMessage((string) $this->config["1_message"]);
        $this->economyAPI->addMoney($player, (int)$this->config["1_reward"]); //Cash
        $this->core->calculate->doMagic($player, (int)$this->config["1_Exp"]); //Exp
        $this->core->elo->increasePoints($player, (int) $this->config["1_Elo"]); //Elo points
    }
}
