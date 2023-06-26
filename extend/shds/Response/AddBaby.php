<?php

namespace shds\Response;

class AddBaby extends Resp
{
    public function __construct($json)
    {
        parent::__construct($json);
        if (!$this->isSuccess()){

        }
        $this->error = $this->data;
    }

    /**
     * @return string
     */
    public function getError(): string
    {
        return $this->error;
    }

}