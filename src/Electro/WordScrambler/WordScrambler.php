<?php

namespace Electro\WordScrambler;

use pocketmine\plugin\PluginBase;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\Listener;
use onebone\economyapi\EconomyAPI;

class WordScrambler extends PluginBase implements Listener{

    public ?string $word = null;
    public float $reward;
    public array $words = [];
    public function onEnable() : void
    {
        $this->loadWords();
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getScheduler()->scheduleDelayedTask(new ScrambleTask($this), (20 * 60 * $this->getConfig()->get("Scramble-Time")));
    }

    public function onChat(playerChatEvent $event)
    {
        $player = $event->getPlayer();
        $msg = $event->getMessage();

        if (strtolower($msg) == strtolower($this->word))
        {
            $event->cancel();
            $this->word = null;
            $this->playerWon($player);
        }
    }


    public function loadWords()
    {
        foreach($this->getConfig()->get("Words") as $word)
        {
            $this->words[] = $word;
        }
    }
    public function playerWon($player)
    {
        $this->getServer()->broadcastMessage("§6" . $player->getName() . " Guessed The Word Correctly.\n§6The Word Was §e" . $this->word);
        EconomyAPI::getInstance()->addMoney($player, $this->reward);
    }

    public function scrambleWord()
    {
        $this->word = $this->words[array_rand($this->words)];
        $this->reward = mt_rand($this->getConfig()->get("Min-Reward"), $this->getConfig()->get("Max-Reward"));
        foreach($this->getServer()->getOnlinePlayers() as $player)
        {
            $player->sendMessage("§bFirst Player To Unscramble The Word §e". str_shuffle($this->word) ." §bWill Receive $". $this->reward ."!");
        }
        $this->getScheduler()->scheduleDelayedTask(new ScrambleTask($this), (20 * 60 * $this->getConfig()->get("Scramble-Time")));
    }

}
