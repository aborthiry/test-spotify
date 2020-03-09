<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7;
use MyAPI\DiscographySpotify;

require '../vendor/autoload.php';
require '../src/config.php';

$app = new \Slim\App;
$app->get('/api/v1/albums', function (Request $request, Response $response, array $args) {

    $band_name = $request->getQueryParam('q', $default = null);

    if ( is_null($band_name) or empty($band_name) ){
        $error = array("message" => 'Ops! The band name is required');
        $response = new \Slim\Http\Response(500);
        $response->getBody()->write(json_encode($error));
        return $response;
    }

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

    $contents = json_decode($res_spotify->getBody()->getContents(), true);
    $ds = new DiscographySpotify();
    
    $discography = $ds->albumsToArray($contents['albums']);
    

    return $response->getBody()->write(json_encode($discography));
});


$app->run();
