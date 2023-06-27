<?php

namespace shds;

use shds\Response\AddBaby;
use shds\Response\FindBaby;
use shds\Response\UploadBabyWork;
use shds\Response\UploadFile;
use think\Exception;

class Student extends Login
{


    public function addBaby($name, $age, $sex, $cert)
    {
        $path = "/megagame/user/userInstitution/addBaby";
        $ret = \Net::PostJson(config('shds_remote_url') . $path, [], [
            'name' => $name,
            'idCardType' => 0,
            'idCardNo' => base64_encode($cert),
            'sex' => $sex,
            'age' => $age
        ], $this->token);
        $resp = new AddBaby($ret);
        if ($resp->isSuccess()) {
            return $resp->isSuccess();
        } else {
            throw new Exception($resp->getError());
        }
    }

    public function findBaby($cert): int
    {
        $path = '/megagame/user/baby/pageUserBaby';
        $ret = \Net::PostJson(config('shds_remote_url') . $path, [], [
            'pageNo' => 1,
            'pageSize' => 10,
            'queryType' => 0,
            'idCardNo' => $cert
        ], ['token' => $this->token]);
        $resp = new FindBaby($ret);
        if ($resp->isSuccess()) {
            return $resp->getId();
        } else {
            throw new Exception($resp->getError());
        }
    }

    public function uploadBabyWork($babyId, $MajorName, $GroupName, $title, $content, $imgs, $teacherName, $teacherTel, $teacherCompany)
    {
        $path = '/megagame/user/baby/uploadBabyWorks';

        $listdata = new Listdata();
        $activityId = $listdata->ActivityId(config('shds_remote_activity'));
        $majorId = $listdata->Major($MajorName);
        $groupId = $listdata->Group($GroupName);
        $ret = \Net::PostJson(config('shds_remote_url') . $path, [], [
            'imgs' => [
                $imgs
            ],
            'activityId' => $activityId,
            'sourceFile' => '',
            'majorId' => $majorId,
            'groupId' => $groupId,
            'teacherName' => $teacherName,
            'teacherCompany' => $teacherCompany,
            'teacherTel' => $teacherTel,
            'name' => $title,
            'synopsis' => $content,
            'babyId' => $babyId
        ], ['token' => $this->token]);
        $upload = new UploadBabyWork($ret);
        return $upload;
    }

    public function uploadFile($remote_link): string
    {
        $path = "/megagame/api/upload/uploadFile";
        $ret = \Net::PostBinary($remote_link, config('shds_remote_url') . $path, ["token" => $this->token]);
        $resp = new UploadFile($ret);
        if ($resp->isSuccess()) {
            return $resp->getFileUrl();
        } else {
            throw new Exception($resp->getError());
        }
    }


}
