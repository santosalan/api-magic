<?php

namespace SantosAlan\ApiMagic;

use GuzzleHttp\Client as GuzzleClient;

interface ApiMagicInterface
{

    public function __call ($name, $arguments = null);
    
    private function setElement($element);
    
    private function getElement();

    public function element($element);

    protected function proccess($name, $arguments);

    protected function verifyRoute($route);

    protected function apiRequest($url, $arguments, $element = null);

    protected function token();

}    