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

    public function Collection(string $page = null)
    {
        // collect all media on site and return a json file to represent them
        if (empty($page)) {
            $files = (array) \App\Providers\FileProvider::scan('pictures/');
            $files = array_map(function ($item) {
                return [
                    'name' => $item->name(),
                    'description' => 'picture of nothing',
                    'url' => config('LINKS.STORAGE') . 'pictures/' . \App\Helpers\DataCleanerHelper::cleanSpaces($item->name()),
                ];
            }, $files);
            $nav = [
                [
                    'name' => 'home',
                    'url' => config('LINKS.PUBLIC') . 'collection/',
                ],
                [
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
                'layout' => [
                    'title' => [
                        'text' => 'title',
                        'html' => 'h1',
                    ],
                    'description',
                    'navigation' => [
                        'html' => 'a'
                    ],
                    'image' => [
                        'name',
                        'html' => 'img',
                        'url',
                        'description',
                    ]
                ]
            ];
            self::respond(200, $collection);
        } elseif ($page === 'images') {
            $files = (array) \App\Providers\FileProvider::scan('pictures/');
            $files = array_map(function ($item) {
                return [
                    'name' => $item->name(),
                    'description' => 'picture of nothing',
                    'url' => config('LINKS.STORAGE') . 'pictures/' . \App\Helpers\DataCleanerHelper::cleanSpaces($item->name()),
                ];
            }, $files);
            $nav = [
                [
                    'name' => 'home',
                    'url' => config('LINKS.PUBLIC') . 'collection/',
                ]
            ];
    
            $collection = [
                'server' => config('DOMAIN'),
                'title' => 'front page',
                'description' => 'displaying all content on the server',
                'navigation' => $nav,
                'image' => $files,
                'layout' => [
                    'navigation' => [
                        'html' => 'a'
                    ],
                    'image' => [
                        'name',
                        'html' => 'img',
                        'url',
                        'description',
                    ]
                ]
            ];
            self::respond(200, $collection);
        }
        
    }
}