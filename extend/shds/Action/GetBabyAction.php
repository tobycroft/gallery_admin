<?php

namespace shds\Action;

use shds\Student;

class GetBabyAction extends Student
{

    private int $id;


    public function getId($name, $age, $sex, $cert): int
    {
        $baby = $this->findBaby($cert);
        if (count($baby) > 0) {

        }else{

        }
    }
}