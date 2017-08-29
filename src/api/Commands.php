<?php 
header('Content-type: text/plain; charset=utf-8');
    class Commands {
        private $bot;
        private $lastChatId;
        private $geoLocation;
        private $choiseKeyboard = [
            'keyboard' => [
                ["Отделения", "Терминалы", "Банкоматы"]
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
            if( $command == "/start"  || $command == "Отправить местоположение") {
                $this->handleStart();
            } else if( $command == "Банкоматы" || $command == "/atms" ){
                $this->handleListItems("Банкомат");
            } else if( $command == "Терминалы" || $command == "/terminals" ){
                $this->handleListItems("Терминал");
            } else if( $command == "Отделения" || $command == "/banks" ){
                $this->handleListItems("Отделение банка");
            } else if($command == "location was set") {
                $this->handleSetLocation();
            } else {
                $this->handleWrong();
            }
        }
        private function handleSetLocation() {
            $location = array(
                $this->bot->getChatId() => $this->bot->getLocation()    
            );
            file_put_contents("geolocation.cfg", json_encode($location));
            $this->bot->sendMessage( "Выберете что вы хотите найти:", $this->choiseKeyboard);
        }
        private function filterAtms($query, $result) {
            return array_filter($result, function ($i) {
                if($i->properties->CompanyMetaData->Categories[0]->class == $query) {
                    return true;
                }
                return false;
            });
        }
        private function handleListItems($query) {
            $ya = new Yandex($this->bot->getChatId());
            $result = $ya->getItemsByGeoLocation($query);
            if($query == "Терминалы") {
                $result = $this->filterAtms("atms", $result);                
            } else if($query == "Банкоматы") {
                $result = $this->filterAtms("atm", $result);
            } else if($query == "Отделения") {
                $result = $this->filterAtms("bank", $result);
            }
            if(empty($result)) {
                $this->bot->sendMessage("Ничего не найдено.", $this->choiseKeyboard);
            }
            $this->bot->sendMessage("Найдено " . count($result) . " объектов вблизи вас.", $this->choiseKeyboard);
            foreach($result as $item ) {
                sleep(2);
                $address =  $item->properties->CompanyMetaData->address;
                $hours = $item->properties->CompanyMetaData->Hours->text;
                $class = $item->properties->CompanyMetaData->Categories[0]->class;
                $geo = $item->geometry->coordinates;
                        $ya->getSaticMap($geo);
                        $this->bot->sendMessage("$query РНКБ.\nАдрес: $address\nВремя работы: $hours\n", $this->choiseKeyboard);
                        $this->bot->sendPhoto( "img.png");
            }   
            
        }
        private function handleStart() {
            $this->bot->sendMessage( "Бот позволяет найти отделения, банкоматы, терминалы крымского банка РНКБ.\n ", $this->geoKeyboard);
            
            $this->bot->sendMessage( "Отправьте боту вашу местоположение, что получить список объектов вблизи вас.", $this->geoKeyboard);
        }
        private function handleWrong() {
            $this->bot->sendMessage( "Команда не найдена", $this->geoKeyboard);
        }

        public function setLastChatId($chatId) {
            $this->lastChatId = $chatId;
        }
    }
    ?>