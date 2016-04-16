# Message Sender for Line TrialBot API

[![Build Status](https://travis-ci.org/oh-sky/line-trialbot.svg?branch=master)](https://travis-ci.org/oh-sky/line-trialbot)

# Install
composer require oh-sky/line-trial-bot-sender

# Usage

```
<?php
namespace Foo;

include 'vendor/autoload.php';

use OhSky\LineTrialBot\Sender;
use OhSky\LineTrialBot\RequestHandler;

$sender = new Sender(
    YOUR_CHANNEL_ID,
    YOUR_CHANNEL_SECRET,
    YOUR_CHANNEL_MID
);

$requestBodyJson = file_get_contents('php://input');
$eventList = RequestHandler::getEventList($requestBodyJson);

foreach ($eventList as $event) {
    // Sending TEXT
    $sender->sendText(
        [$event->content->from],
        "text to send"
    );

    // Sending Image
    $sender->sendImage(
        [$event->content->from],
        'IMAGE_URL',
        'THUMBNAIL_URL'
    );

    // Sending Video
    $sender->sendVideo(
        [$event->content->from],
        'VIDEO_URL',
        'THUMBNAIL_URL'
    );

    // Sending Audio
    $sender->sendAudio(
        [$event->content->from],
        'AUDIO_URL',
        'AUDIO_LENGTH_MILLI_SECOND'
    );

    // Sending Location
    $sender->sendLocation(
        [$event->content->from],
        'TEXT',
        'TITLE',
        latitude, //float
        longitude //fload
    );

    // Sending Sticker
    $sender->sendSticker(
        [$event->content->from],
        'STICKER_ID',
        'STICKER_PACKAGE_ID',
        'STICKER_VERSION'
    );
}
```
