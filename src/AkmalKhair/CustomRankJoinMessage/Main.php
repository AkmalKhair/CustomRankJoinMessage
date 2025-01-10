<?php

declare(strict_types=1);

namespace AkmalKhair\CustomRankJoinMessage;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\plugin\PluginBase;
use IvanCraft623\RankSystem\RankSystem;
use IvanCraft623\RankSystem\utils\Utils;
use pocketmine\Server;

class Main extends PluginBase implements Listener {

    private array $joinMessages;
    private array $leaveMessages;

    public function onEnable(): void {
        $this->saveDefaultConfig();
        $this->joinMessages = $this->getConfig()->get("join-messages", []);
        $this->leaveMessages = $this->getConfig()->get("leave-messages", []);
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onPlayerJoin(PlayerJoinEvent $event): void {
        $player = $event->getPlayer();
        $session = RankSystem::getInstance()->getSessionManager()->get($player->getName());
        $event->setJoinMessage("");
        $session->onInitialize(function() use ($player, $session) {
            $rank = Utils::ranks2string($session->getRanks()) ?? "";
            $customMessage = $this->joinMessages[$rank] ?? "Welcome to the kingdom, {player}!";
            $customMessage = str_replace("{player}", $player->getName(), $customMessage);
            Server::getInstance()->broadcastMessage($customMessage);
        });
    }
    public function onPlayerQuit(PlayerQuitEvent $event): void {
        $player = $event->getPlayer();
        $session = RankSystem::getInstance()->getSessionManager()->get($player->getName());
        $event->setQuitMessage("");
        $session->onInitialize(function() use ($player, $session) {
            $rank = Utils::ranks2string($session->getRanks()) ?? "";
            $customMessage = $this->leaveMessages[$rank] ?? "Farewell, {player}. May your journey be safe.";
            $customMessage = str_replace("{player}", $player->getName(), $customMessage);
            Server::getInstance()->broadcastMessage($customMessage);
        });
    }
}
