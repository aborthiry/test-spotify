<?php

namespace Src\Classes;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7;
use Exception;

/**
 * Esta clase administra el flujo de autenticacion con spotify wep api (Client Credentials Flow).
 * Basicamente obtiene un token desde un client_id and client_secret definido en el conf.php
 * El token permite construir request con spotify Web API 
 */

class Authorization
{
    
    private static $instance = null;    

    private $access_token;
    private $expires_token;
    
    
    public function __construct()
    {
        $this->expires_token = time();
    }    
   
    /**
     * Obtiene el token mediante la api de spotify
     * 
     * @return string $access_token 
     */
    
    public function getToken()
    {
        if ($this->expires_token >= time()) {
            $client = new Client();
            try {
                $response = $client->request('POST', 'https://accounts.spotify.com/api/token', [
                    'headers' => [
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/x-www-form-urlencoded',
                        'Authorization' => 'Basic '.base64_encode(CLIENTE_ID.':'.CLIENTE_SECRET)
                    ],
                    'form_params' => [
                        'grant_type' => 'client_credentials',
                    ]
                ]);
            } catch (RequestException $e) {               
                throw new Exception(Psr7\str($e->getResponse()),$e->getCode());                
            }
            $obj = \GuzzleHttp\json_decode($response->getBody());
            
            $this->expires_token = time() + $obj->{'expires_in'};
            $this->access_token = $obj->{'access_token'};                                    
        }
        return $this->access_token;
    }

    
    
    
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    
    final public function __clone()
    {
        throw new Exception('Feature disabled.');
    }

    
    final public function __wakeup()
    {
        throw new Exception('Feature disabled.');
    }
}
