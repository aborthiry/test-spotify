<?php

namespace Src\Classes;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\ClientException;
use Exception;

/**
 * 
 * 
 */

class DiscographySpotify
{

    protected $band_name;
    protected $token;

    public function __construct($band_name,$token){
        $this->band_name = $band_name;
        $this->token = $token;

    }

    /**
     * Conviente un arrelgo "album object" (Response Format Web API) en otro arreglo con el formato especificado
     *      
     * @param array $albums Album bject (Web API spotify)   
     * 
     * @return array $discography  Discography in the specified format     
     */

    public function albumsToArray($albums)
    {
        $discography = array();

        foreach ($albums['items'] as $item) {
            $disc = array();
            $disc['name'] = $item['name'];
            $disc['released'] = $item['release_date'];
            $disc['tracks'] = $item['total_tracks'];
            foreach ($item['images'] as $image) {
                if ($image['height'] == COVER_HEIGHT and $image['width'] == COVER_WIDTH) {
                    $disc['cover']['height'] = COVER_HEIGHT;
                    $disc['cover']['width'] = COVER_WIDTH;
                    $disc['cover']['url'] = $image['url'];
                }
            }
            $discography[] = $disc;
        }
        return $discography;
    }


    /**
     * Devuelve la discografia en el formato especificado (see comment below)
     * 
     * 
     * @return array $discography Discography in the specified format
     */


    public function getDiscography()
    {
        $client = new Client();
        try {
            $response = $client->request('GET', 'https://api.spotify.com/v1/search?q=artist:"' . $this->band_name . '"&limit=' . SEARCH_LIMIT . '&type=' . SEARCH_TYPE, [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => sprintf('Bearer %s', $this->token)
                ]
            ]);
        } catch (ClientException $e) {
            throw new Exception(Psr7\str($e->getResponse()), $e->getCode());
        }
        $contents = json_decode($response->getBody()->getContents(), true);
        $discography = $this->albumsToArray($contents['albums']);
        return $discography;
    }


    /** Comment: format required
     * 
     * [{
     *  "name": "Album Name",
     *  "released": "10-10-2010",
     *  "tracks": 10,
     *  "cover": {  
     *      "height": 640,
     *      "width": 640,
     *      "url": "https://i.scdn.co/image/6c951f3f334e05ffa"
     *  }
     *  },
     *  ...
     * ]
     * 
     */
}
