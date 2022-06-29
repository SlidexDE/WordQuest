<?php

namespace Electro\WordScrambler;

use pocketmine\plugin\PluginBase;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\Listener;
use cooldogedev\BedrockEconomy\api\version\LegacyBEAPI;
use cooldogedev\BedrockEconomy\api\BedrockEconomyAPI;
use pocketmine\player\Player;

class WordScrambler extends PluginBase implements Listener{
    
    public ?string $word = null;
    public int $reward;
    public bool $rewardEnabled = false;
    public array $words = [];
    
    /** @var LegacyBEAPI*/
    public $moneyAPI;
    
    public function onEnable() : void
    {
        if ($this->getConfig()->get("Reward-Enabled"))
        {
            $this->rewardEnabled = true;
        }
        if (!$this->getServer()->getPluginManager()->getPlugin("BedrockEconomy") && $this->rewardEnabled == true)
        {
            $this->getLogger()->warning("Reward has been disabled since you do not have BedrockEconomy installed on your server.");
            $this->rewardEnabled = false;
        }
        $this->loadWords();
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getScheduler()->scheduleDelayedTask(new ScrambleTask($this), (20 * 60 * $this->getConfig()->get("Scramble-Time")));
        $this->moneyAPI = BedrockEconomyAPI::legacy();
    }
    
    public function onChat(playerChatEvent $event)
    {
        $player = $event->getPlayer();
        $msg = $event->getMessage();
        
        if (strtolower($msg) == strtolower($this->word))
        {
           $event->cancel();
           $this->playerWon($player);
           $this->word = null;
        }
    }
    
    
    public function loadWords()
    {
        foreach($this->getConfig()->get("Words") as $word)
        {
            $this->words[] = $word;
        }
    }
    public function playerWon(Player $player)
    {
        $this->getServer()->broadcastMessage("§6" . $player->getName() . " Guessed The Word Correctly.\n§6The Word Was §e" . $this->word);
        if ($this->rewardEnabled)
        {
            $this->moneyAPI->addToPlayerBalance($player->getName(), $this->reward);
        }
    }
    
    public function scrambleWord()
    {
        $this->word = $this->words[array_rand($this->words)];
        if ($this->rewardEnabled)
        {
            $this->reward = mt_rand($this->getConfig()->get("Min-Reward"), $this->getConfig()->get("Max-Reward"));
        }
        foreach($this->getServer()->getOnlinePlayers() as $player)
        {
            if ($this->rewardEnabled)
            {
                $player->sendMessage("§bFirst Player To Unscramble The Word §e". str_shuffle($this->word) ." §bWill Receive $". $this->reward ."!");
            }
            else
            {
                $player->sendMessage("§bTry to be the first player to unscramble §e". str_shuffle($this->word) . "!");
            }
        }
        $this->getScheduler()->scheduleDelayedTask(new ScrambleTask($this), (20 * 60 * $this->getConfig()->get("Scramble-Time")));
    }
}
