
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

    public function onChat(playerChatEvent $event)
    {
        $msg = $event->getMessage();

        if ($msg == $this->word)
        {
            $this->word = null;
        }
    }


    public function loadWords()
    {
        foreach($this->getConfig()->get("Words") as $word)
        {
            array_push($this->words, $word);
        }
    }

}
