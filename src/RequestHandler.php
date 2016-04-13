<?php
namespace OhSky\LineTrialBot;

class RequestHandler
{
    /**
     * @param string $json
     * @return array
     */
    public static function getEventList($json)
    {
        $obj = json_decode($json);
        return isset($obj->result) ? $obj->result : [];
    }
}
