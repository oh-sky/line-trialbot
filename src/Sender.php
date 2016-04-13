<?php
/**
 * LineTrialBot Message Sender
 *
 * @copyright    Copyright © 2016 oh-sky
 * @link         https://github.com/oh-sky/line-trial-bot
 * @license      MIT License
 * @author oh-sky <yoshihiro.ohsuka@gmail.com>
 * @see https://developers.line.me/bot-api/api-reference
 */

namespace OhSky\LineTrialBot;

use GuzzleHttp\Client as GuzzleClient;
use Psr\Http\Message\ResponseInterface;

class Sender
{
    const BASE_URI = 'https://trialbot-api.line.me/';
    const ENDPOINT_EVENTS = '/v1/events';
    const TO_CHANNEL = 1383378250;
    const EVENT_TYPE = '138311608800106203';

    /** @var \GuzzleHttp\Client $guzzleClient */
    private $guzzleClient = null;
    /** @var  \Psr\Http\Message\ResponseInterface $response */
    private $response = null;
    /** @var string $channelId */
    private $channelId = null;
    /** @var string $channelSecret */
    private $channelSecret = null;
    /** @var string $mid */
    private $mid = null;

    /**
     * LineTrialBot constructor.
     * @param string $channelId
     * @param string $channelSecret
     * @param string $mid
     * @param array|null $options The argument for \GuzzleHttp\Client()
     */
    public function __construct($channelId, $channelSecret, $mid, $options = null)
    {
        $guzzleClientOptions = [
            'base_uri' => self::BASE_URI,
        ];
        if (is_array($options)) {
            $guzzleClientOptions = array_merge($guzzleClientOptions, $options);
        }
        $this->guzzleClient = new GuzzleClient($guzzleClientOptions);
        $this->channelId = (string)$channelId;
        $this->channelSecret = (string)$channelSecret;
        $this->mid = (string)$mid;
    }

    /**
     * @param array|string $to
     * @param string $text
     * @return object
     */
    public function sendText($to, $text)
    {
        return $this->sendMessage($to, [
            'contentType' => 1,
            'toType' => 1,
            'text' => $text,
        ]);
    }

    /**
     * @param array|string $to
     * @param string $imageUrl
     * @param string $thumbnailImageUrl
     * @return object
     */
    public function sendImage($to, $imageUrl, $thumbnailImageUrl)
    {
        return $this->sendMessage($to, [
            'contentType' => 2,
            'toType' => 1,
            'originalContentUrl' => $imageUrl,
            'previewImageUrl' => $thumbnailImageUrl,
        ]);
    }

    /**
     * @param array|string $to
     * @param string $videoUrl URL of the movie. The "mp4" format is recommended.
     * @param string $thumbnailImageUrl
     * @return object
     */
    public function sendVideo($to, $videoUrl, $thumbnailImageUrl)
    {
        return $this->sendMessage($to, [
            'contentType' => 3,
            'toType' => 1,
            'originalContentUrl' => $videoUrl,
            'previewImageUrl' => $thumbnailImageUrl,
        ]);
    }

    /**
     * @param array|string $to
     * @param string $audioUrl
     * @param string $lengthMilliSec
     * @return object
     */
    public function sendAudio($to, $audioUrl, $lengthMilliSec)
    {
        return $this->sendMessage($to, [
            'contentType' => 4,
            'toType' => 1,
            'originalContentUrl' => $audioUrl,
            'contentMetadata' => [
                'AUDLEN'  => $lengthMilliSec,
            ],
        ]);
    }

    /**
     * @param array|string $to
     * @param string $text
     * @param string $title
     * @param double $latitude
     * @param double $longitude
     * @return object
     */
    public function sendLocation($to, $text, $title, $latitude, $longitude)
    {
        return $this->sendMessage($to, [
            'contentType' => 7,
            'toType' => 1,
            'text' => $text,
            'location' => [
                'title'  => $title,
                'latitude' => $latitude,
                'longitude' => $longitude,
            ],
        ]);
    }

    /**
     * @param array|string $to
     * @param string $stickerId
     * @param string $stickerPackageId
     * @param string $stickerVersion
     * @return object
     */
    public function sendSticker($to, $stickerId, $stickerPackageId, $stickerVersion)
    {
        return $this->sendMessage($to, [
            'contentType' => 8,
            'toType' => 1,
            'contentMetadata' => [
                'STKID' => $stickerId,
                'STKPKGID' => $stickerPackageId,
                'STKVER' => $stickerVersion,
            ],
        ]);
    }

    /**
     * @param array|string $to
     * @param array $content
     * @return $this|mixed
     */
    public function sendMessage($to, $content)
    {
        $this->response = $this->guzzleClient->request('POST', self::ENDPOINT_EVENTS, [
            'headers' => $this->createRequestHeaderArray(),
            'body' => $this->createRequestBodyJson($to, $content),
        ]);
        return $this;
    }

    /**
     * @return array
     */
    private function createRequestHeaderArray()
    {
        return [
            'Content-Type' => 'application/json; charset=UTF-8',
            'X-Line-ChannelID' => $this->channelId,
            'X-Line-ChannelSecret' => $this->channelSecret,
            'X-Line-Trusted-User-With-ACL' => $this->mid,
        ];
    }

    /**
     * @param array|string $to
     * @param array $content
     * @return string json
     */
    private function createRequestBodyJson($to, $content)
    {
        if (!is_array($to)) {
            $to = [$to];
        }
        return json_encode([
                'to' => $to,
                'toChannel' => self::TO_CHANNEL,
                'eventType' => self::EVENT_TYPE,
                'content' => $content,
        ]);
    }

    /**
     * @return object|array|null
     */
    public function getResponse()
    {
        if ($this->response instanceof ResponseInterface) {
            return json_decode($this->response->getBody());
        } else {
            return null;
        }
    }
}
