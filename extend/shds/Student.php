<?php

namespace shds;

use shds\Response\AddBaby;
use shds\Response\FindBaby;
use shds\Response\Resp;
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
        echo json_encode($record->getRecords());
    }

    public function uploadBabyWork($activityId, $activityName, $babyId, $groupId, $imgs, $majorId, $majorname, $name,)
    {
        $path = '/megagame/user/baby/uploadBabyWorks';
        $ret = \Net::PostJson(config('shds_remote_url') . $path, [], [
            'activityName' => '第二十七届全国中小学生绘画书法作品比赛',
            'imgs' => [
                'https://motherland-h5.oss-cn-beijing.aliyuncs.com/1687769113264/2023062616451340180877206391549.jpg'
            ],
            'activityId' => 24,
            'sourceFile' => '',
            'groupId' => 12,
            'majorId' => 16,
            'majorname' => '绘画',
            'teacherName' => '123',
            'teacherCompany' => '123',
            'teacherTel' => '123',
            'name' => '123',
            'synopsis' => '123',
            'babyId' => 813116
        ], ['token' => $this->token]);
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
