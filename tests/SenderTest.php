<?php
/**
 * @see http://docs.guzzlephp.org/en/latest/testing.html
 */
namespace OhSky\LineTrialBot\Tests;

use PHPUnit_Framework_TestCase;
use OhSky\LineTrialBot\Sender;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Middleware;
use GuzzleHttp\HandlerStack;
use stdClass;

/**
 * Class SenderTest
 * @package OhSky\LineTrialBot\Tests
 */
class SenderTest extends PHPUnit_Framework_TestCase
{
    const CHANNEL_ID_FOR_TEST = '123';
    const CHANNEL_SECRET_FOR_TEST = '456';
    const MID_FOR_TEST = '789';
    const TO_USER_ID_FOR_TEST = 'u123';
    const RESPONSE_BODY_FOR_MOCK = '{"foo":"bar"}';

    public function testSendText()
    {
        $container = [];
        $sender = $this->generateSender(
            $this->generateHandlerStack(
                $container,
                $this->generateMockHandler()
            )
        );

        $res = $sender
            ->sendText([self::TO_USER_ID_FOR_TEST], 'hello')
            ->getResponse();

        $this->assertSame(self::RESPONSE_BODY_FOR_MOCK, json_encode($res));

        $request = $container[0]['request'];
        $this->assertSame('POST', $request->getMethod());

        $this->assertCorrectRequestHeaders($request);

        $expectedBody = $this->generateCommonPartOfExpectedBody();
        $expectedBody->content = new stdClass();
        $expectedBody->content->contentType = 1;
        $expectedBody->content->toType = 1;
        $expectedBody->content->text = 'hello';
        $this->assertCorrectRequestBody($request, json_encode($expectedBody));
    }

    public function testSendImage()
    {
        $container = [];
        $sender = $this->generateSender(
            $this->generateHandlerStack(
                $container,
                $this->generateMockHandler()
            )
        );

        $res = $sender
            ->sendImage(
                [self::TO_USER_ID_FOR_TEST],
                'https://example.com/image.jpg',
                'https://example.com/thumbnail.jpg'
            )
            ->getResponse();

        $this->assertSame(self::RESPONSE_BODY_FOR_MOCK, json_encode($res));

        $request = $container[0]['request'];
        $this->assertSame('POST', $request->getMethod());

        $this->assertCorrectRequestHeaders($request);

        $expectedBody = $this->generateCommonPartOfExpectedBody();
        $expectedBody->content = new stdClass();
        $expectedBody->content->contentType = 2;
        $expectedBody->content->toType = 1;
        $expectedBody->content->originalContentUrl = 'https://example.com/image.jpg';
        $expectedBody->content->previewImageUrl = 'https://example.com/thumbnail.jpg';
        $this->assertCorrectRequestBody($request, json_encode($expectedBody));
    }

    public function testSendVideo()
    {
        $container = [];
        $sender = $this->generateSender(
            $this->generateHandlerStack(
                $container,
                $this->generateMockHandler()
            )
        );

        $res = $sender
            ->sendVideo(
                [self::TO_USER_ID_FOR_TEST],
                'https://example.com/video.mp4',
                'https://example.com/thumbnail.jpg'
            )
            ->getResponse();

        $this->assertSame(self::RESPONSE_BODY_FOR_MOCK, json_encode($res));

        $request = $container[0]['request'];
        $this->assertSame('POST', $request->getMethod());

        $this->assertCorrectRequestHeaders($request);

        $expectedBody = $this->generateCommonPartOfExpectedBody();
        $expectedBody->content = new stdClass();
        $expectedBody->content->contentType = 3;
        $expectedBody->content->toType = 1;
        $expectedBody->content->originalContentUrl = 'https://example.com/video.mp4';
        $expectedBody->content->previewImageUrl = 'https://example.com/thumbnail.jpg';
        $this->assertCorrectRequestBody($request, json_encode($expectedBody));
    }

    public function testSendAudio()
    {
        $container = [];
        $sender = $this->generateSender(
            $this->generateHandlerStack(
                $container,
                $this->generateMockHandler()
            )
        );

        $res = $sender
            ->sendAudio(
                [self::TO_USER_ID_FOR_TEST],
                'https://example.com/audio.m4a',
                '12345'
            )
            ->getResponse();

        $this->assertSame(self::RESPONSE_BODY_FOR_MOCK, json_encode($res));

        $request = $container[0]['request'];
        $this->assertSame('POST', $request->getMethod());

        $this->assertCorrectRequestHeaders($request);

        $expectedBody = $this->generateCommonPartOfExpectedBody();
        $expectedBody->content = new stdClass();
        $expectedBody->content->contentType = 4;
        $expectedBody->content->toType = 1;
        $expectedBody->content->originalContentUrl = 'https://example.com/audio.m4a';
        $expectedBody->content->contentMetadata = ['AUDLEN' => '12345'];
        $this->assertCorrectRequestBody($request, json_encode($expectedBody));
    }

    public function testSendLocation()
    {
        $container = [];
        $sender = $this->generateSender(
            $this->generateHandlerStack(
                $container,
                $this->generateMockHandler()
            )
        );

        $res = $sender
            ->sendLocation(
                [self::TO_USER_ID_FOR_TEST],
                'The text',
                'The title',
                35.65,
                139.73
            )
            ->getResponse();

        $this->assertSame(self::RESPONSE_BODY_FOR_MOCK, json_encode($res));

        $request = $container[0]['request'];
        $this->assertSame('POST', $request->getMethod());

        $this->assertCorrectRequestHeaders($request);

        $expectedBody = $this->generateCommonPartOfExpectedBody();
        $expectedBody->content = new stdClass();
        $expectedBody->content->contentType = 7;
        $expectedBody->content->toType = 1;
        $expectedBody->content->text = 'The text';
        $expectedBody->content->location = [
            'title' => 'The title',
            'latitude' => 35.65,
            'longitude' => 139.73,
        ];
        $this->assertCorrectRequestBody($request, json_encode($expectedBody));
    }

    public function testSendSticker()
    {
        $container = [];
        $sender = $this->generateSender(
            $this->generateHandlerStack(
                $container,
                $this->generateMockHandler()
            )
        );

        $res = $sender
            ->sendSticker(
                [self::TO_USER_ID_FOR_TEST],
                'sticker_id',
                'sticker_package_id',
                'sticker_version'
            )
            ->getResponse();

        $this->assertSame(self::RESPONSE_BODY_FOR_MOCK, json_encode($res));

        $request = $container[0]['request'];
        $this->assertSame('POST', $request->getMethod());

        $this->assertCorrectRequestHeaders($request);

        $expectedBody = $this->generateCommonPartOfExpectedBody();
        $expectedBody->content = new stdClass();
        $expectedBody->content->contentType = 8;
        $expectedBody->content->toType = 1;
        $expectedBody->content->contentMetadata = [
            'STKID' => 'sticker_id',
            'STKPKGID' => 'sticker_package_id',
            'STKVER' => 'sticker_version',
        ];
        $this->assertCorrectRequestBody($request, json_encode($expectedBody));
    }

    /**
     * @return MockHandler
     */
    private function generateMockHandler()
    {
        return new MockHandler([
            new Response(
                200,
                ['Content-Length' => strlen(self::RESPONSE_BODY_FOR_MOCK)],
                self::RESPONSE_BODY_FOR_MOCK
            ),
        ]);
    }

    /**
     * @param &array $container
     * @return HandlerStack
     */
    private function generateHandlerStack(&$container, $mock)
    {
        $history = Middleware::history($container);
        $stack = HandlerStack::create($mock);
        $stack->push($history);
        return $stack;
    }

    /**
     * @param HandlerStack $stack
     * @return Sender
     */
    private function generateSender($stack)
    {
        return new Sender(
            self::CHANNEL_ID_FOR_TEST,
            self::CHANNEL_SECRET_FOR_TEST,
            self::MID_FOR_TEST,
            ['handler' => $stack]
        );
    }

    /**
     * @param Request $request
     */
    private function assertCorrectRequestHeaders($request)
    {
        $request = (array)$request;
        $requestHeaders = $request["\0GuzzleHttp\Psr7\Request\0headerLines"];
        $this->assertSame(self::CHANNEL_ID_FOR_TEST, $requestHeaders['X-Line-ChannelID'][0]);
        $this->assertSame(self::CHANNEL_SECRET_FOR_TEST, $requestHeaders['X-Line-ChannelSecret'][0]);
        $this->assertSame(self::MID_FOR_TEST, $requestHeaders['X-Line-Trusted-User-With-ACL'][0]);
    }

    /**
     * @param Request $request
     * @param string $expectedBody json
     */
    private function assertCorrectRequestBody($request, $expectedBody)
    {
        $request = (array)$request;
        $requestBody = $request["\0GuzzleHttp\Psr7\Request\0stream"]->__toString();
        $this->assertSame($expectedBody, $requestBody);
    }

    /**
     * @return stdClass
     */
    private function generateCommonPartOfExpectedBody()
    {
        $expectedBody = new stdClass();
        $expectedBody->to = [self::TO_USER_ID_FOR_TEST];
        $expectedBody->toChannel = Sender::TO_CHANNEL;
        $expectedBody->eventType = Sender::EVENT_TYPE;
        return $expectedBody;
    }
}
