<?php
namespace App\API;

use App\APIController;
use App\Helpers\DataCleanerHelper;
use App\Providers\FileProvider;
/**
 * 
 */
final class CollectionController extends APIController
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
        $folders = (array) \App\Providers\FileProvider::scanFolders(config('COLLECTION.DIRECTORIES', []));
        $collection = [
            'server' => config('DOMAIN'),
            'title' => config('APP.NAME'),
            'description' => 'Displaying content from the server',
            'navigation' => [],
            'images' => [],
        ];
        foreach ($folders as $folder => $files) {
            foreach ($files as $file) {
                $collection['navigation'][$folder] = [
                    'name' => \App\Helpers\DataCleanerHelper::dataMap($folder, '/', function ($result, $item) {return $item;}, 0, -1),
                    'url' => (config('LINKS.EXPAND')('PUBLIC') . $folder)
                ];
                if (\App\Providers\FileProvider::checkExtension($file->extension(), ['jpeg','jpg','png'])) {
                    $collection['images'][] = [
                        'name' => $file->name(),
                        'url' => $file->link(),
                    ];
                }
            }
        }
        self::respond(200, $collection);
    }
}