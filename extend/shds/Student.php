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
        ]);
        $resp = new AddBaby($ret);
        if ($resp->isSuccess()) {
            return true;
        } else {
            throw new Exception($resp->getError());
        }
    }

    public function findBaby($cert)
    {
        $path = '/megagame/user/baby/pageUserBaby';
        $ret = \Net::PostJson(config('shds_remote_url') . $path, [], [
            'pageNo' => 1,
            'pageSize' => 10,
            'queryType' => 0,
            'idCardNo' => $cert
        ], ['token' => $this->token]);
        $record = new FindBaby($ret);
        return $record;
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

    public function uploadFile()
    {
        $path = "/megagame/api/upload/uploadFile";
        $fileData = ("https://static.familyeducation.org.cn/gallery/20230612/9de27752e3aff3e7257989f4c7eda315.jpg");
        echo $this->token;
        $ret = \Net::PostBinary($fileData, config('shds_remote_url') . $path, ["token" => $this->token]);
        $resp = new UploadFile($ret);
        return $resp;
    }


}
