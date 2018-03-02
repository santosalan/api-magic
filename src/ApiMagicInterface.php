<?php

namespace SantosAlan\ApiMagic;

use GuzzleHttp\Client as GuzzleClient;

interface ApiMagicInterface
{

    public function __call ($name, $arguments = null);
    
    public function element($element);

}    