<?php
namespace App\API;

use App\APIController;
/**
 * 
 */
final class BroadcastController extends APIController
{
    public function Options()
    {
        // respond with allowed options
        
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: GET");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    }

    public function Index()
    {
        self::respond(200, 'General Broadcast');
    }

    public function Notifications()
    {
        echo 'User Notifications';
    }

    public function Collection()
    {
        // collect all media on site and return a json file that represent them
        $files = (array) \App\Providers\FileProvider::scan('pictures/');
        $files = array_map(function ($item) {
            return [
                'name' => $item->name(),
                'description' => 'picture of nothing',
                'url' => config('LINKS.STORAGE') . 'pictures/' . \App\Helpers\DataCleanerHelper::cleanSpaces($item->name()),
            ];
        }, $files);
        $nav = [
            'home' => [
                'name' => 'home',
                'url' => config('LINKS.PUBLIC') . 'collection/',
            ],
            'images' => [
                'name' => 'images',
                'url' => config('LINKS.PUBLIC') . 'collection/' . 'images/',
            ]
        ];
        $collection = [
            'server' => config('DOMAIN'),
            'title' => 'front page',
            'description' => 'displaying all content on the server',
            'navigation' => $nav,
            'image' => $files,
        ];
        self::respond(200, $collection);
    }
}