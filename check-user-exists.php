<?php

use danog\MadelineProto\Exception;

require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$MadelineProto = new \danog\MadelineProto\API('session/session.madeline');

$message = $MadelineProto->messages->sendMessage(['peer' => getenv('GROUP_ID'), 'message' => 'Comecei a monitorar']);

$loops = 0;
$MadelineProto->loop(function () use ($MadelineProto, $message, $loops) {
    $date = new DateTime();
    while (true) {
        yield $MadelineProto->start();

        try {
            $MadelineProto->getInfo(getenv('USER_TO_MONITORE'));
            $MadelineProto->messages->editMessage(['peer' => getenv('GROUP_ID'), 'id' => $message['id'],
                'message' => 'Ainda existe, monitorando faz ' . $loops . " minutos.\n".
                    "Comecei às " . $date->format('Y-m-d H:i:s')
            ]);
        } catch (Exception $th) {
            $MadelineProto->account->updateUsername(['username' => getenv('USER_TO_MONITORE')]);
            $MadelineProto->messages->sendMessage(['peer' => getenv('GROUP_ID'),
                'message' => getenv('SUCCESS_MESSAGE')
            ]);
        }
        sleep(60);
        $loops++;
    }
});
