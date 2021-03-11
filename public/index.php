<?php

/**
 * Endpoint test-spotify  
 * Retorna los albunes de una banda dada (parametro q por GET)
 * 
 * @author Ariel Borthiry <arielborthiry@gmail.com>
 * @link https://github.com/aborthiry/test-spotify
 */

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Src\Classes\DiscographySpotify;
use Src\Classes\Authorization;

require '../vendor/autoload.php';
require '../src/config.php';

$app = new \Slim\App;


$app->get('/api/v1/albums', function (Request $request, Response $response, array $args) {

    //band name is required
    $band_name = $request->getQueryParam('q', $default = null);
    if ( is_null($band_name) or empty($band_name) ){
        $error = array("message" => 'Ops! The band name is required');
        $response = new \Slim\Http\Response(400);
        return $response->getBody()->write(json_encode($error));
      
    }
    
    $token = Authorization::getToken();
    $ds = new DiscographySpotify($band_name,$token);

    try {        
        $discography = $ds->getDiscography();
    } catch (Exception $e) {
        $error = array("message" => $e->getMessage());
        $response = new \Slim\Http\Response($e->getCode());
        return $response->getBody()->write(json_encode($error));
        
    }
     
    return  $response->getBody()->write(json_encode($discography));
    

    
});


$app->run();
