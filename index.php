<?php
require 'vendor/autoload.php';

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

class EventHandler extends \danog\MadelineProto\EventHandler
{
    public function onLoop() {}
    public function onUpdateNewChannelMessage($update)
    {
        if (isset($update['message']['out']) && $update['message']['out']) {
            return;
        }
        if(isset($update['message']['to_id']['channel_id']) && $update['message']['to_id']['channel_id'] == getenv('GROUP_ID')) {
            print_r($update['message']);
            $this->messages->sendMessage([
                'peer' => $update,
                'message' => 'Este grupo foi inativado, acesse o novo grupo: @SegInfoBRasil',
                'reply_to_msg_id' => $update['message']['id']
            ]);
        }
    }
}

$MadelineProto = new \danog\MadelineProto\API('session/session.madeline');
$MadelineProto->start();
$MadelineProto->setEventHandler('\EventHandler');
$MadelineProto->loop();