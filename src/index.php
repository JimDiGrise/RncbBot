<?php
    require "../vendor/autoload.php";

    require "api/telegram.php";
    require "api/Commands.php";

    $tl = new Telegram("https://api.telegram.org/", "bot310341855:AAGF60Bu1mHjDjjEn31ekxwJmKw-OMTBlqg/");
    $lastMessage = $tl->getLastMessage();
    print_r($lastMessage);
    $tl->confirmMessage();
    $command = new Commands($tl);
    $command->setLastChatId($tl->getChatId());
    $command->handleCommand($lastMessage);
?>