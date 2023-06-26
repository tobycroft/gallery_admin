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

    private $major_ret;

    public int $activityId;


    public function ActivityId($name): int
    {
        $path = '/megagame/user/userWorks/getActivityList';
        $ret = \Net::PostJson(config('shds_remote_url') . $path, [], self::$jayParsedAry, $this->header);
        $resp = new GetActivityList($ret);
        if ($resp->isSuccess()) {
            if (count($resp->getRecords()) > 1) {
                $this->activityId = $resp->getMap()[$name];
                $this->setmajor();
                return $resp->getMap()[$name];
            } else {
                $this->activityId = $resp->getMap()[array_key_first($resp->getMap())];
                $this->setmajor();
                return $resp->getMap()[array_key_first($resp->getMap())];
            }
        } else {
            throw new Exception($resp->getError());
        }
    }

    private function setmajor()
    {
        $path = '/megagame/api/swMajor/getCurrentActivityMajor';
        $ret = \Net::PostJson(config('shds_remote_url') . $path, [], ['activityId' => $this->activityId], $this->header);
        $this->major_ret = new GetMajor($ret);
    }

    public function Major($name): int
    {
        if ($this->major_ret->isSuccess()) {
            return $this->major_ret->getMajorId($name);
        } else {
            throw new Exception($this->major_ret->getError());
        }
    }

    public function Group($major_name, $group_name): int
    {
        if ($this->major_ret->isSuccess()) {
            return $this->major_ret->getGroupId($major_name, $group_name);
        } else {
            throw new Exception($this->major_ret->getError());
        }
    }

}