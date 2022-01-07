<?php
require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$MadelineProto = new \danog\MadelineProto\API('session/session.madeline');
$MadelineProto->async(false);
$MadelineProto->start();
// die($MadelineProto->getFullInfo('groupname'));
$peer['_']='peerChannel';
$peer['channel_id'] = getenv('GROUP_ID');
$loops = 0;
$MadelineProto->loop(function () use ($MadelineProto, $peer, $loops) {
    while (true) {
        yield $MadelineProto->start();
        $pwr_chat = yield $MadelineProto->getPwrChat($peer);
        $total = 0;
        $inativos = 0;
        $users = [];
        foreach($pwr_chat['participants'] as $key => $partcipant) {
            if(!isset($partcipant['user']['first_name'])) {
                $inativos++;
                continue;
            }
            if(count($users) < 10 && $key+1 < count($pwr_chat['participants'])) {
                $name = $partcipant['user']['first_name'];
                $users[]='<a href="mention:'.$partcipant['user']['id'].'">'.$name.'</a>';
                continue;
            }
            yield $MadelineProto->messages->sendMessage([
                'peer' => $peer,
                'message' =>
                    'Olá '.implode(',', $users).','."\n".
                    getenv('MESSAGE'),
            'parse_mode' => 'Markdown'
            ]);
            $total=$total+count($users);
            echo "$total de ".count($pwr_chat['participants']).", inativos: $inativos\r";
            $users = [];
            sleep(5);
        }
        $loops++;
        yield $MadelineProto->echo("\ntotal pessoas: ".count($pwr_chat['participants']).", loop: $loops\n");
    }
});
