<?php
namespace App\API;

use System\APIController;
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
        self::respond(200, 'User Notifications');
    }
}