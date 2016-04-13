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
    $sender->sendText(
        [$event->content->from],
        "text to send"
    );
}
```
