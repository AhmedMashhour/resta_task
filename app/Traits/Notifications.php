<?php
/**
 * Created by PhpStorm.
 * User: foda
 * Date: 10/22/2019
 * Time: 12:32 PM
 */

namespace App\Traits;
use LaravelFCM\Message\Topics;
use \Pusher\PushNotifications\PushNotifications;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use LaravelFCM\Facades\FCM;
use Pusher\Pusher;

trait Notifications
{
    public function notify($title , $body,$sendingData,$eventListenKey)
    {
        $beamsClient = new PushNotifications(array(
            "instanceId" => env('PUSHER_APP_INSTANCE_ID'),
            "secretKey" => env('PUSHER_APP_SECRET_KEY'),
        ));
        $publishResponse = $beamsClient->publishToInterests(
            $eventListenKey,
            array(
                "fcm" => array(
                    "notification" => array(
                        "title" => $title,
                        "body" => $body
                    ),
                    "data" => $sendingData
                ),
                "apns" => array(
                    "aps" => array(
                        "alert" => array(
                            "title" => $title,
                            "body" => $body
                        )
                    ),
                    "data" => $sendingData
                )
            ));

    }

    public function notifyFireBase($title,$body,$sendingData,$listenKey)
    {
        $notificationBuilder = new PayloadNotificationBuilder($title);
        $notificationBuilder->setBody($body)
            ->setSound('default');

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData($sendingData);

        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

//        $token = "fPgTwxHOyms:APA91bFsPnotbbV5nut7lYCMlWMqsXuNuohupg-DrHbjQWtkH96jPTf2UkZ6gJ-nNTfNoocpGLZUcn9u7UaygwKLF2qHJflMg81GYxQM0B_sR95raiPSeZlyd50eE4DqqI6OOwQ1xAja";

        $topic = new Topics();
        $topic = $topic->topic($listenKey[0]);

        for ($i = 1 ; $i < sizeof($listenKey) ; $i++)
        {
            $topic = $topic->orTopic($listenKey[$i]);
        }


        FCM::sendToTopic($topic, null, $notification, $data);
    }

    public function pushChanel($channel , $event , $data)
    {
        $restId = auth()->user()->restaurant_id;

        $options = array(
            'cluster' => env('PUSHER_CHANNEL_APP_CLUSTER'),
            'useTLS' => true
        );
        $pusher = new Pusher(
            env('PUSHER_CHANNEL_APP_KEY'),
            env('PUSHER_CHANNEL_APP_SECRET'),
            env('PUSHER_CHANNEL_APP_ID'),
            $options
        );

        $pusher->trigger($restId.'_'.$channel, $event, $data);
    }
}
