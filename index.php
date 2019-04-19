<?php
require 'vendor/autoload.php';

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

class EventHandler extends \danog\MadelineProto\EventHandler
{
    public function onLoop() {
        $updates = $this->updates;
    }
    public function onUpdateNewChannelMessage($update)
    {
        if (isset($update['message']['out']) && $update['message']['out']) {
            return;
        }
        if(isset($update['message']['to_id']['channel_id']) && $update['message']['to_id']['channel_id'] == getenv('GROUP_ID')) {
            print_r($update['message']);
            $this->messages->sendMessage([
                'peer' => $update,
                'message' => "Este grupo foi inativado, acesse o novo grupo: @SegInfoBRasil.\n".
                    "Recomendamos que saia deste grupo e entre no outro.",
                'reply_to_msg_id' => $update['message']['id']
            ]);
        }
    }
}

$MadelineProto = new \danog\MadelineProto\API('session/session.madeline');
$MadelineProto->start();
$peer['_']='peerChannel';
$peer['channel_id'] = getenv('GROUP_ID');
$loops = 0;
while(true){
    $pwr_chat = $MadelineProto->get_pwr_chat($peer);
    $total = 0;
    $inativos = 0;
    $users = [];
    foreach($pwr_chat['participants'] as $key => $partcipant) {
        if(!isset($partcipant['user']['first_name'])) {
            $inativos++;
            continue;
        }
        if(count($users) <=10 && $key+1 < count($pwr_chat['participants'])) {
            $name = $partcipant['user']['first_name'];
            $users[]='<a href="mention:'.$partcipant['user']['id'].'">'.$name.'</a>';
            continue;
        }
        $MadelineProto->messages->sendMessage([
            'peer' => $peer,
            'message' =>
                'Olá '.implode(',', $users).','."\n".
                "Este grupo foi inativado, acesse o novo grupo: @SegInfoBRasil.\n".
                "Recomendamos que saia daqui e entre no outro.",
           'parse_mode' => 'Markdown'
        ]);
        $total=$total+count($users);
        echo "$total de ".count($pwr_chat['participants']).", inativos: $inativos\r";
        $users = [];
        sleep(5);
    }
    $loops++;
    echo "\ntotal pessoas: ".count($pwr_chat['participants']).", loop: $loops\n";
}
