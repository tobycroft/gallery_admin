<?php

namespace shds\Response;

class UploadBabyWork extends Resp
{

    protected int $code = 0;

    public function __construct($json)
    {
        parent::__construct($json);
        if (!$this->isSuccess()) {
            return $this;
        }
        $this->code = $this->data['code'];
        if ($this->code != 20000) {
            $this->is_success = false;
        }
        $this->error = $this->data['data'];
    }

    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }
}