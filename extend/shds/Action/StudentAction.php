<?php

namespace shds\Action;

use shds\Student;

class StudentAction extends Student
{

    public function AddOrGetId($name, $age, $sex, $cert): int
    {
        $baby = $this->findBaby($cert);
        if ($baby > 0) {
            return $baby;
        } else {
            $this->addBaby($name, $age, $sex, $cert);
            return $this->findBaby($cert);
        }
    }
}