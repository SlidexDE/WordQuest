
<?php

namespace Electro\WordScrambler;

use pocketmine\Player;

use pocketmine\plugin\PluginBase;
// use Config
// use PlayerChatEvent
use pocketmine\event\Listener;


class ChatScrambler extends PluginBase implements Listener{

    public $word = null;
    public $words = [];

    public function onEnable()
    {
        $this->loadWords();
        // Enable Listener
    }

    public function onChat(playerChatEvent $event)
    {
        $player = $event->getPlayer();
        $msg = $event->getMessage();

        if ($msg == $this->word)
        {
            $this->word = null;
            $this->playerWon($player);
        }
    }


    public function loadWords()
    {
        foreach($this->getConfig()->get("Words") as $word)
        {
            array_push($this->words, $word);
        }
    }
    public function playerWon($player)
    {
        $this->getServer()->broadcastMessage("§6" . $player->getName() . " Guessed The Word Correctly.\nThe Word Was " . $this->word);
        // Give money
    }

}
