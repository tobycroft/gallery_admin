<?php

namespace shds;

use shds\Response\AddBaby;
use think\Exception;

class Listdata extends Login
{
    public static $jayParsedAry = [
        'pageNo' => 1,
        'pageSize' => 10000
    ];

    public function ActivityList($name)
    {
        $path = 'megagame/user/userWorks/getActivityList';
        $ret = \Net::PostJson(config('shds_remote_url') . $path, [], self::$jayParsedAry, $this->header);
        $resp = new AddBaby($ret);
        if ($resp->isSuccess()) {
            return true;
        } else {
            throw new Exception($resp->getError());
        }
    }
}