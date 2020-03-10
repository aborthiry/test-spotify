<?php

/**
 * Endpoint test-spotify  
 * Returns one or more albums from the Spotify catalog of specific band 
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
        $response = new \Slim\Http\Response(500);
        $response->getBody()->write(json_encode($error));
        return $response;
    }
    

    $auth = Authorization::getInstance();
    $ds = DiscographySpotify::getInstance();

    try {
        $access_token = $auth->getToken();
        $discography = $ds->getDiscography($band_name,$access_token);
    } catch (Exception $e) {
        $error = array("message" => $e->getMessage());
        $response = new \Slim\Http\Response($e->getCode());
        $response->getBody()->write(json_encode($error));
        return $response;
    }
     
    return $response->getBody()->write(json_encode($discography));
});


$app->run();
