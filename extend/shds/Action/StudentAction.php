<?php

namespace shds\Action;

use shds\Response\UploadBabyWork;
use shds\Student;

class StudentAction extends Student
{

    public function AddOrGetId($name, $age, $sex, $cert): int
    {
        $babyId = $this->findBaby($cert);
        if ($babyId > 0) {
            return $babyId;
        } else {
            $this->addBaby($name, $age, $sex, $cert);
            $babyId = $this->AddOrGetId($name, $age, $sex, $cert);
            if ($babyId === 0) {
                throw new \ErrorException("新建学生错误");
            } else {
                return $babyId;
            }
        }
    }

    public function UploadOrUpdate($babyId, $tag_name, $tag_group_name, $title, $content, $oss_file_link, $teacher_name, $teacher_phone, $school_name_show): UploadBabyWork
    {
        $id = $this->GetUploadedId($babyId, $tag_name, $tag_group_name);
        if ($id === 0) {
            return $this->uploadBabyWork($babyId, $tag_name, $tag_group_name, $title, $content, $oss_file_link, $teacher_name, $teacher_phone, $school_name_show);
        } else {
            return $this->updateBabyWork($id, $babyId, $tag_name, $tag_group_name, $title, $content, $oss_file_link, $teacher_name, $teacher_phone, $school_name_show);
        }
    }
}