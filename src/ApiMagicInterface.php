<?php

namespace SantosAlan\ApiMagic;

use GuzzleHttp\Client as GuzzleClient;

interface ApiMagicInterface
{

    /**
     * Base URI
     * 
     * @var String
     */
    protected $host = null;

    protected $port = null;

    protected $prefix = null;

    protected $actionRoutes = null;

    protected $namedReturn = true;

    protected $tokenField = null;
    
    private $element = null;


    public function __call ($name, $arguments = null);
    
    private function setElement($element);
    
    private function getElement();

    public function element($element);

    protected function proccess($name, $arguments);

    protected function verifyRoute($route);

    /**
     * Request Curl
     * 
     * @param  Url $url Endereco de requisicao
     * @return Array      Dados retornados da requisicao
     */
    protected function apiRequest($url, $arguments, $element = null);

    /**
     * Gerar o token de request
     * 
     * @return String authToken
     */
    protected function token();

}    