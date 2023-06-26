<?php

namespace shds;

use shds\Response\GetActivityList;
use shds\Response\GetMajor;
use think\Exception;

class Listdata extends Login
{
    public static $jayParsedAry = [
        'pageNo' => 1,
        'pageSize' => 10000
    ];

    public function ActivityId(): int
    {
        $path = '/megagame/user/userWorks/getActivityList';
        $ret = \Net::PostJson(config('shds_remote_url') . $path, [], self::$jayParsedAry, $this->header);
        $resp = new GetActivityList($ret);
        if ($resp->isSuccess()) {
            if (count($resp->getRecords()) > 1) {
                return $resp->getMap()[config('shds_remote_activity')];
            } else {
                return $resp->getMap()[array_key_first($resp->getMap())];
            }
        } else {
            throw new Exception($resp->getError());
        }
    }

    public function Major($name)
    {
        $path = '/megagame/api/swMajor/getCurrentActivityMajor';
        $ret = \Net::PostJson(config('shds_remote_url') . $path, [], self::$jayParsedAry, $this->header);
        $resp = new GetMajor($ret);
        if ($resp->isSuccess()) {
            if (count($resp->getRecords()) > 1) {
                return $resp->getMap()[config('shds_remote_activity')];
            } else {
                return $resp->getMap()[array_key_first($resp->getMap())];
            }
        } else {
            throw new Exception($resp->getError());
        }
    }

    public function Group($major_name, $group_name)
    {
        $path = '/megagame/api/swMajor/getCurrentActivityMajor';
        $ret = \Net::PostJson(config('shds_remote_url') . $path, [], self::$jayParsedAry, $this->header);
        $resp = new GetMajor($ret);
        if ($resp->isSuccess()) {
            if (count($resp->getRecords()) > 1) {
                return $resp->getMap()[config('shds_remote_activity')];
            } else {
                return $resp->getMap()[array_key_first($resp->getMap())];
            }
        } else {
            throw new Exception($resp->getError());
        }
    }

}