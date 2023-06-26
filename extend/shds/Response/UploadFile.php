<?php

namespace shds\Response;

class UploadFile extends Resp
{

    protected int $code = 0;

    protected string $fileUrl = "";

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
        $this->data = $this->data["data"];
        $this->fileUrl = $this->data["fileUrl"];
    }

    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getFileUrl(): string
    {
        return $this->fileUrl;
    }
}