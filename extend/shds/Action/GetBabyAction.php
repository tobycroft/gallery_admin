<?php

namespace shds\Action;

use shds\Student;

class GetBabyAction extends Student
{

    private int $id;


    public function getId($name, $age, $sex, $cert): int
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