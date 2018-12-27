<?php

namespace SantosAlan\ApiMagic;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\RequestOptions;

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

    private $toJson = false;

    private $element = null;

    private $auth = null;


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

    public function toJson()
    {
        $this->toJson = true;

        return $this;
    }

    public function element($element)
    {
        $this->setElement(trim($element));

        return $this;
    }

    public function auth($username, $password, $type = 'basic')
    {
        if ('basic' != lowercase($type)) {
            $this->auth = [$username, $password, $type];
        } else {
            $this->auth = [$username, $password];
        }

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

        $requestUrl = $url
                    . (!empty($arguments[1])
                            ? (substr($arguments[1][0], 0, 1) === '/'
                                ? implode('/',$arguments[1])
                                : '/' . implode('/',$arguments[1]))
                            : null);

        $params = !empty($arguments[2]) ? $arguments[2] : [];

        $headers = !empty($arguments[3]) ? $arguments[3] : [];

        $client = new GuzzleClient([
            'base_uri' => $this->host . $this->port . $this->prefix . '/',
            'headers' => $headers
        ]);

        if (!empty($this->tokenField)) {
            $params = array_merge($params, [$this->tokenField => $this->token()]);
        }

        $options = [];
        if ($this->toJson) {
            $options = [RequestOptions::JSON => $params];
        } else {
            if($arguments[0] === 'GET') {
                $values = [];
                foreach ($params as $key => $value) {
                   $values[] = $key . '=' . $value;
                }

                $requestUrl .= '?' . implode('&', $values);

            } else {
                $options = ['form_params' => $params];
            }
        }

        if ($this->auth) {
            $options['auth'] = $this->auth;
        }

        $data = $client->request(
            $arguments[0],
            $requestUrl,
            $options
        )->getBody();

        return $element ? '{"' . $element . '":' . $data . '}' : (string) $data;

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