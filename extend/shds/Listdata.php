<?php

namespace shds;

use shds\Response\ActivityList;
use shds\Response\GetActivityList;
use shds\Response\GetMajor;
use think\Exception;

class Listdata extends Login
{


    public static $jayParsedAry = [
        'pageNo' => 1,
        'pageSize' => 10000
    ];

    private GetMajor $major_ret;

    public int $activityId;

    private string $major_name;


    public function GetUploadedId($babyId, $major_name, $group_name): int
    {
        $path = '/megagame/user/baby/v1/activityList';
        $ret = \Net::PostJson(config('shds_remote_url') . $path, ['babyId' => $babyId], self::$jayParsedAry, $this->header);
        $resp = new ActivityList($ret);
        if ($resp->isSuccess()) {
            return $resp->getUploadId($major_name, $group_name);
        } else {
            throw new Exception($resp->getError());
        }
    }

    public function ActivityResp(): GetActivityList
    {
        $path = '/megagame/user/userWorks/getActivityList';
        $ret = \Net::PostJson(config('shds_remote_url') . $path, [], self::$jayParsedAry, $this->header);
        $resp = new GetActivityList($ret);
        if ($resp->isSuccess()) {
            $this->activityId = $resp->getActivityId(config('shds_remote_activity'));
            $this->setmajor();
            return $resp;
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
        $this->major_name = $name;
        if ($this->major_ret->isSuccess()) {
            return $this->major_ret->getMajorId($name);
        } else {
            throw new Exception($this->major_ret->getError());
        }
    }

    public function Group($group_name): int
    {
        if ($this->major_ret->isSuccess()) {
            return $this->major_ret->getGroupId($this->major_name, $group_name);
        } else {
            throw new Exception($this->major_ret->getError());
        }
    }

}