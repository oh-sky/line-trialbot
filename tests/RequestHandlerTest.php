<?php
namespace OhSky\LineTrialBot\Tests;

use OhSky\LineTrialBot\RequestHandler;
use PHPUnit_Framework_TestCase;
use stdClass;

/**
 * Class RequestHandlerTest
 * @package OhSky\LineTrialBot\Tests
 */
class RequestHandlerTest extends PHPUnit_Framework_TestCase
{
    public function testGetEventList()
    {
        $data = new stdClass();
        $sampleList = [
            ['a'],
            ['b', 'c'],
            ['d', 'e', 'f'],
            'g',
        ];
        $data->result = $sampleList;

        $response = RequestHandler::getEventList(json_encode($data));
        $this->assertSame($sampleList, $response);
    }
}
