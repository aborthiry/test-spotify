<?php

namespace MyAPI;


class DiscographySpotify
{
    /**
     *  
     * 
     * @param array 
     * 
     * @return array
     *
     */
    public function albumsToArray($albums)
    {
        $discography = array();

        foreach ($albums['items'] as $item) {
            $disc = array();
            $disc['name'] = $item['name'];
            $disc['release'] = $item['release_date'];
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
}
