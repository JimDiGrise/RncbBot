<?php 


    class Commands {
        private $bot;
        private $lastChatId;
        private $startKeyboard = [
            'keyboard' => [
                ["Список", "Отправить местоположение"]
            ],
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ];
        private $geoKeyboard = [
            'keyboard' => [
                [
                   [
                       'text' => 'Отправить местоположение',
                       'request_location'=>true
                  ]
                ] 
                
            ],
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ];

        public function __construct($bot) {
            $this->bot = $bot;    
        }
        public function handleCommand($command) {
            if($command == "/start") {
                $this->handleStart();    
            } else if($command == "/geo" || $command == "Отправить местоположение") {
                $this->handleGeo();
            } else {
                $this->handleWrong();
            }
        }
        private function handleStart() {
            $this->bot->sendMessage($this->lastChatId, "БОТ РНКБ может найти отделения банка рнкб.", $this->startKeyboard);
        }
        private function handleWrong() {

        }
        private function handleGeo() {
            $this->bot->sendMessage($this->lastChatId, "С помощью вашей гео локации мы можем определить какие отделения банка находяться неподалеку.", $this->geoKeyboard);
        }
        public function setLastChatId($chatId) {
            $this->lastChatId = $chatId;
        }
    }
    ?>