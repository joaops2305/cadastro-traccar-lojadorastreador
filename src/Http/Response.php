<?php

namespace SRC\Http;

class Response
{
    private $httpCode = 200;
    private $headers = [];
    private $contentType = 'text/html';
    private $content;

    public function __construct($httpCode, $content, $contentType = 'text/html')
    {
        $this->httpCode = $httpCode;
        $this->content  = $content;
        $this->setcontentType($contentType);
    }

    //
    public function setcontentType($contentType)
    {
        $this->contentType = $contentType;
        $this->addHeader('content-type', $contentType);
    }

    //
    public function addHeader($key, $value)
    {
        $this->headers[$key] = $value;
    }

    //
    public function sedHeaders()
    {
        http_response_code($this->httpCode);

        foreach ($this->headers as $key => $value) {
            @header($key . ': ' . $value);
        }
    }

    //
    public function sendResponse()
    {
        $this->sedHeaders();

        switch ($this->contentType) {
            case 'text/html':
                print $this->content;
                exit();
        }
    }
}
