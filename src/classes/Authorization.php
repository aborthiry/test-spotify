<?php

namespace MyAPI;


class Authorization
{
    private $access_token;
    private $expires_token;
    //base64_encode 

    public function __construct() {
        $this->expires_token = time();
    }
    public function getToken(){
        if ($this->expires_token >= time()){
            curl -X "POST" -H "Authorization: Basic ZDkyZGFiMTllNjJjNDZiYWFlODgzMDRkMWFjYzU1OWY6ODUwOGEwNzQ5NTdhNDA2MTk1YzI5ZDEyMWY2NWMzNjI=" -d grant_type=client_credentials https://accounts.spotify.com/api/token
            $client = new Client();
    try {
        $res_spotify = $client->request('GET', 'https://api.spotify.com/v1/search?q=artist:"'.$band_name.'"&limit='.SEARCH_LIMIT.'&type='.SEARCH_TYPE, [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => sprintf('Bearer %s', ACCESS_TOKEN)
            ]
        ]);
    } catch (ClientException $e) {

        $error = array("message" => Psr7\str($e->getResponse()));
        $response = new \Slim\Http\Response($e->getCode());
        $response->getBody()->write(json_encode($error));
        return $response;
    }
        }
        return $this->access_token;

    }
}   
    