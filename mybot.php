<?php


//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);


require_once 'Telegram.php';
require 'users.php';
require "Pages.php";

$telegram = new Telegram('5849239572:AAFFiVQoBNUNQVy-tSf9VnOIz0NDIstKHd8');
//if($t = $telegram->setWebhook('https://intelefon.uz/tiktok/mybot.php')){
//    var_dump($t);
//}
$data = $telegram->getData();

$chatID = $telegram->ChatID();
$text = $telegram->Text();
$message_id = $telegram->MessageID();
$userName = $telegram->FirstName();
$botName = "ProSaveAsbot";


$users = new Users($chatID);


if ($text == '/setting') {
    $telegram->sendMessage([
        'chat_id' => $chatID,
        'text' =>  /*$users->getKeyValue('error')*/ $users->getData1()['count(chatID)']
    ]);
}

if ($text == '/start') {

    $users->setPage(Pages::Start);

    $telegram->sendChatAction([
        'chat_id' => $chatID,
        'message_thread_id' => uniqid(),
        'action' => 'typing'
    ]);
    $telegram->sendMessage([
        'chat_id' => $chatID,
        'text' => "И снова здравствуйте, {$userName}!

Вы можете скинуть мне ссылку на пост в Instagram, Pinterest или TikTok откуда нужно выгрузить фото, видео и текст — через пару секунд эта фотка или видос будут у вас!

На данный момент, я поддерживаю только фото, видео, карусели, текст и IGTV-видео из Instagram, Pinterest и TikTok!"
    ]);
} else {
    $users->setPage(Pages::media);
    if ($strpos = strpos($text, "https://pin.it/") !== false) {
        $res = json_decode(PinterestDownload($text));

        if ($res->success == "true") {
            if ($res->type == 'image') {
                $telegram->sendChatAction([
                    'chat_id' => $chatID,
                    'message_thread_id' => uniqid(),
                    'action' => 'upload_photo'
                ]);

                $check = $telegram->sendPhoto([
                    'chat_id' => $chatID,
                    'photo' => $res->data->url,
                    'caption' => "Спасибо, что пользуетесь - @{$botName}'ом"
                ]);
                if ($check['ok']) {
                   // DeleteMessage($c['result']['message_id']);

                    $telegram->sendDocument([
                        'chat_id' => $chatID,
                        'document' => $res->data->url,
                        'caption' => "Для ценителей качества — изображение документом!"
                    ]);
                    if ($res->data->title != '' or $res->data->title) {
                        $telegram->sendChatAction([
                            'chat_id' => $chatID,
                            'message_thread_id' => uniqid(),
                            'action' => 'typing'
                        ]);

                        $telegram->sendMessage([
                            'chat_id' => $chatID,
                            'text' => $res->data->title
                        ]);
                    }
                } else {
                    $telegram->sendMessage([
                        'chat_id' => $chatID,
                        'text' => "Адрес неверный! Пожалуйста, проверьте и отправьте повторно!"
                    ]);
                }

            } elseif ($res->type == "video") {
                $telegram->sendChatAction([
                    'chat_id' => $chatID,
                    'message_thread_id' => uniqid(),
                    'action' => 'record_video'
                ]);

                $telegram->sendChatAction([
                    'chat_id' => $chatID,
                    'message_thread_id' => uniqid(),
                    'action' => 'upload_video'
                ]);

                $check = $telegram->sendVideo([
                    'chat_id' => $chatID,
                    'video' => $res->data->url,
                    'caption' => "Спасибо, что пользуетесь - @{$botName}'ом"
                ]);

                if ($check['ok']) {
                    if ($res->data->title != '' or $res->data->title) {
                        $telegram->sendChatAction([
                            'chat_id' => $chatID,
                            'message_thread_id' => uniqid(),
                            'action' => 'typing'
                        ]);

                        $telegram->sendMessage([
                            'chat_id' => $chatID,
                            'text' => $res->data->title
                        ]);
                    }
                } else {
                    $telegram->sendMessage([
                        'chat_id' => $chatID,
                        'text' => "Адрес неверный! Пожалуйста, проверьте и отправьте повторно!"
                    ]);
                }
            }
        } else {
            $telegram->sendMessage([
                'chat_id' => $chatID,
                'text' => "Адрес неверный! Пожалуйста, проверьте и отправьте повторно!"
            ]);
        }
    } else if (strpos($text, "https://vt.tiktok.com/") !== false or strpos($text, "https://www.tiktok.com/@") !== false) {
        $res = json_decode(TiktokVideo($text));
        $c = $telegram->sendMessage([
            'chat_id' => $chatID,
            'text' => 'Загрузка...'
        ]);
        if ($res) {
            if ($res->video[0] != '') {
                $telegram->sendChatAction([
                    'chat_id' => $chatID,
                    'message_thread_id' => uniqid(),
                    'action' => 'record_video'
                ]);

                $telegram->sendChatAction([
                    'chat_id' => $chatID,
                    'message_thread_id' => uniqid(),
                    'action' => 'upload_video'
                ]);

                $check = $telegram->sendVideo([
                    'chat_id' => $chatID,
                    'video' => $res->video[0],
                    'caption' => "Спасибо, что пользуетесь - @{$botName}'ом"
                ]);

            }
            if ($check['ok']) {
                DeleteMessage($c['result']['message_id']);

                if ($res->music[0] != '') {
                    $telegram->sendChatAction([
                        'chat_id' => $chatID,
                        'message_thread_id' => uniqid(),
                        'action' => 'record_voice'
                    ]);

                    $telegram->sendChatAction([
                        'chat_id' => $chatID,
                        'message_thread_id' => uniqid(),
                        'action' => 'upload_voice'
                    ]);

                    $telegram->sendAudio([
                        'chat_id' => $chatID,
                        'audio' => $res->music[0],
                        'caption' => "Спасибо, что пользуетесь - @{$botName}'ом"
                    ]);
                }
                if ($res->description[0] != '') {

                    $telegram->sendMessage([
                        'chat_id' => $chatID,
                        'text' => $res->description[0]
                    ]);
                }
            }

        } else {
            DeleteMessage($c['result']['message_id']);

            $telegram->sendMessage([
                'chat_id' => $chatID,
                'text' => "Адрес неверный! Пожалуйста, проверьте и отправьте повторно!"
            ]);
        }
    }
    elseif (strpos($text, "https://www.instagram.com/") !== false or strpos($text, "https://instagram.com/stories/") !== false) {
    //    $res = json_decode(InstagramVideo($text));
        $telegram->sendMessage([
            'chat_id' => $chatID,
            'text' => 'ведутся технические работы, скоро снова заработает'
        ]);
    }
}

function DeleteMessage($message_id)
{
    global $telegram, $chatID;
    $telegram->deleteMessage([
        'chat_id' => $chatID,
        'message_id' => $message_id
    ]);
}

function testError($id)
{
    while (true) {
        $count = $id + 1;
    }
}

function InstagramVideo($url)
{
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => "https://instagram-downloader-download-instagram-videos-stories.p.rapidapi.com/index?url=" . $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => [
            "X-RapidAPI-Host: instagram-downloader-download-instagram-videos-stories.p.rapidapi.com",
            "X-RapidAPI-Key: 45f035d36dmshe056590a062eae9p1685f8jsnbd810ec6796f"
        ],
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        return false;
    } else {
        return $response;
    }

}

function PinterestDownload($url)
{
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => "https://pinterest-video-and-image-downloader.p.rapidapi.com/pinterest?url=" . $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => [
            "X-RapidAPI-Host: pinterest-video-and-image-downloader.p.rapidapi.com",
            "X-RapidAPI-Key: 45f035d36dmshe056590a062eae9p1685f8jsnbd810ec6796f"
        ],
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        return false;
    } else {
        return $response;
    }
}

function TiktokVideo($url)
{

    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => "https://tiktok-full-info-without-watermark.p.rapidapi.com/vid/index?url=" . $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => [
            "X-RapidAPI-Host: tiktok-full-info-without-watermark.p.rapidapi.com",
            "X-RapidAPI-Key: 45f035d36dmshe056590a062eae9p1685f8jsnbd810ec6796f"
        ],
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        return false;
    } else {
        return $response;
    }
}

