<?php
    require "../vendor/autoload.php";

    require "api/telegram.php";
    require "api/Commands.php";
    require "api/yandex.php";

    try {
    $tl = new Telegram("https://api.telegram.org/", "bot447665582:AAGEiHixHo5KBV8pp-aaYDhKxisfyu8X6DQ/");
    $location;
        while(1) {
                sleep(3);
                $command = new Commands($tl);
                $lastMessage = $tl->getLastMessage();
                if(!empty($lastMessage)) {
                    $tl->confirmMessage();
                    $command->handleCommand($lastMessage);
                }
                
            }
            
        } catch(Exception $e) {
            echo "Exception: " . $e;
        }
   
?>