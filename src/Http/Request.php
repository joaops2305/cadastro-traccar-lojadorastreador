<?php

namespace SRC\Http;

class Request
{
    private $httpMethod;
    private $uri;
    private $queryPrams = [];
    private $postVars = [];
    private $heraders = [];

    private $router;

    public function __construct($router)
    {
        $this->router = $router;
        $this->queryPrams = $_GET ?? [];
        $this->postVars = $_POST ?? [];
        $this->heraders = getallheaders();
        $this->httpMethod = $_SERVER['REQUEST_METHOD'] ?? '';
        $this->setUri();
    }

    //
    private function setUri()
    {
        $this->uri = $_SERVER['REQUEST_URI'] ?? '';

        $xURI = explode('?', $this->uri);

        $this->uri = $xURI[0];
    }

    public function getRouter()
    {
        return $this->router;
    }

    //
    public function getHttpMethod()
    {
        return $this->httpMethod;
    }

    //
    public function getUri()
    {
        return $this->uri;
    }

    //
    public function getHeraders()
    {
        return $this->heraders;
    }

    //
    public function getQueryPrams()
    {
        return $this->queryPrams;
    }

    //
    public function getPostVars()
    {
        return $this->postVars;
    }
}
