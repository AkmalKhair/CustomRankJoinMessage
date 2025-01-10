<?php

declare(strict_types=1);

namespace AkmalKhair\CustomRankJoinMessage;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;
use IvanCraft623\RankSystem\RankSystem; // Assuming RankSystem provides a RankManager class
use IvanCraft623\RankSystem\utils\Utils;
use pocketmine\Server;

class Main extends PluginBase implements Listener {

    private array $joinMessages;
    private array $leaveMessages;

    public function onEnable(): void {
        $this->saveDefaultConfig();
        $this->joinMessages = $this->getConfig()->get("join-messages", []);
        $this->leaveMessages = $this->getConfig()->get("leave-messages", []);

        $this->getLogger()->info(TextFormat::GREEN . "CustomRankJoinMessage plugin enabled!");
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onDisable(): void {
        $this->getLogger()->info(TextFormat::RED . "CustomRankJoinMessage plugin disabled!");
    }

    public function onPlayerJoin(PlayerJoinEvent $event): void {
        $player = $event->getPlayer();
        $session = RankSystem::getInstance()->getSessionManager()->get($player->getName());
    
        // Set pesan default sementara (hanya untuk mencegah kosong)
        $event->setJoinMessage("");
    
        // Tunggu hingga sesi diinisialisasi
        $session->onInitialize(function() use ($player, $session) {
            $rank = Utils::ranks2string($session->getRanks());
            if (empty($rank)) {
                $rank = ''; // Rank default jika rank kosong
            }
    
            $customMessage = $this->joinMessages[$rank] ?? "Welcome to the kingdom, {player}!";
            $customMessage = str_replace("{player}", $player->getName(), $customMessage);
    
            // Broadcast pesan ke seluruh pemain
            Server::getInstance()->broadcastMessage(TextFormat::AQUA . $customMessage);
        });
    }

    public function onPlayerQuit(PlayerQuitEvent $event): void {
        $player = $event->getPlayer();
        $session = RankSystem::getInstance()->getSessionManager()->get($player->getName());

        // Set pesan default sementara (hanya untuk mencegah kosong)
        $event->setQuitMessage("");

        // Tunggu hingga sesi diinisialisasi
        $session->onInitialize(function() use ($player, $session) {
            $rank = Utils::ranks2string($session->getRanks());
            if (empty($rank)) {
                $rank = ''; // Rank default jika rank kosong
            }

            $customMessage = $this->leaveMessages[$rank] ?? "Farewell, {player}. May your journey be safe.";
            $customMessage = str_replace("{player}", $player->getName(), $customMessage);
    
            // Broadcast pesan ke seluruh pemain
            Server::getInstance()->broadcastMessage(TextFormat::AQUA . $customMessage);
        });
    }
}
