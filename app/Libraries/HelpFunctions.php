<?php

namespace App\Libraries;

use DB;
use Pusher;
class HelpFunctions{

    public function getRandom($randomLength, $whichCaracters = 0){
        $letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $digits = '0123456789';

        switch ($whichCaracters){
            case 1:
                $characters = $letters;
                break;
            case 2:
                $characters = $digits;
                break;
            default:
                $characters = $letters.$digits;
                break;
        }

        $randomText = '';
        $max = strlen($characters) - 1;
        for ($i = 0; $i < $randomLength; $i++) {
            $randomText .= $characters[mt_rand(0, $max)];
        }

        return $randomText;
    }

    public function sendData($socketKey, $data){

        $options = array(
            'cluster' => env("PUSHER_APP_CLUSTER"),
            'encrypted' => true
        );
        $pusher = new Pusher(
            env("PUSHER_APP_KEY"),
            env("PUSHER_APP_SECRET"),
            env("PUSHER_APP_ID"),
            $options
        );

        //$data['data'] = $data;
        $pusher->trigger($socketKey, 'my-event', $data);


//        $localsocket = 'tcp://127.0.0.1:1234';
//
//        // соединяемся с локальным tcp-сервером
//        $instance = stream_socket_client($localsocket);
//        // отправляем сообщение
//        fwrite($instance, json_encode(['user' => $user, 'data' => $data])  . "\n");
    }

    public function createAndSendNotification($userId, $notifType, $notifParameters, $messageData){
        $notificationData = [
            "user_client_id" => $userId,
            "type" => $notifType,
            "parameters" => json_encode($notifParameters),
            "created_at" => date("Y-m-d H:i:s")
        ];

        DB::table("notifications")->insert($notificationData);

        $this->sendData(DB::table("users_clients")->where("id", $userId)->first()->socket_key, $messageData);
    }
}