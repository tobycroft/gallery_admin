<?php

namespace shds\Action;

use shds\Student;

class StudentAction extends Student
{

    public function AddOrGetId($name, $age, $sex, $cert): int
    {
        $babyId = $this->findBaby($cert);
        var_dump($babyId);
        if ($babyId > 0) {
            return $babyId;
        } else {
            if ($this->addBaby($name, $age, $sex, $cert)) {
                
            }
            return $this->findBaby($cert);
        }
    }
}