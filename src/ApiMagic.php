<?php

namespace SantosAlan\ApiMagic;

use GuzzleHttp\Client as GuzzleClient;

class ApiMagic implements ApiMagicInterface
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


    public function __call ($name, $arguments = null)
    {
        $this->setElement($name);

        return $this->proccess($name, $arguments);
    }    

    private function setElement($element) 
    {
        $this->element = trim($this->element) == null ? $element : $this->element;
    }

    private function getElement()
    {
        if (! $this->namedReturn) {
            return null;
        } else {
            return $this->element;
        }
    }

    public function element($element)
    {
        $this->setElement(trim($element));

        return $this;
    }

    protected function proccess($name, $arguments)
    {

        if (! trim($this->host)) {
            throw new \Exception('ERROR - Set up HOST required.');
        }

        if ($this->verifyRoute($name)) {
            return $this->apiRequest($name, $arguments, $this->getElement());
        } else {
            return json_encode(['error' => 'Route not found!']);
        }
    }

    protected function verifyRoute($route)
    {
        if (! trim($this->actionRoutes)) {
            return true;
        }            

        $json = $this->apiRequest($this->actionRoutes, ['POST']);

        $routes = json_decode($json);

        foreach ($routes as $r) {
            if ($r->action === $route) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Request Curl
     * 
     * @param  Url $url Endereco de requisicao
     * @return Array      Dados retornados da requisicao
     */
    protected function apiRequest($url, $arguments, $element = null)
    {
        $client = new GuzzleClient(['base_uri' => $this->host . $this->port . $this->prefix . '/']);

        $requestUrl = $url 
                    . (!empty($arguments[1]) ? '/' . implode('/',$arguments[1]) : null);

        $params = !empty($arguments[2]) ? $arguments[2] : [];

        if (!empty($this->tokenField)) {
            $params = array_merge($params, [$this->tokenField => $this->token()]);
        }

        $data = $client->request(
            $arguments[0], 
            $requestUrl,
            [
                'form_params' => $params
            ]
        )->getBody();       

        return $element ? '{"' . $element . '":' . $data . '}' : $data;

    }

    /**
     * Gerar o token de request
     * 
     * @return String authToken
     */
    protected function token()
    {
        return '';
    }

}    