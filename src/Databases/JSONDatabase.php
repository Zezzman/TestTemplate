<?php
namespace App\Databases;

use App\Interfaces\IDatabase;
use App\Controller;
use App\Helpers\FileHelpers;
use PDO;
use PDOException;
use App\Exceptions\RespondException;
/**
 *
 * @author  Francois Le Roux <francoisleroux97@gmail.com>
 */
class JSONDatabase implements IDatabase
{
    public const TYPE = 'JSON';
    private static $instance = null;
    private static $db = null;

    private $data = null;
    private $lastID = -1;


    private function __construct() {}
    public static function instance()
    {
        if (isset(self::$instance)) {
            return self::$instance;
        } else {
            return new self();
        }
    }
    /**
     * 
     */
    public static function connect()
    {
        if (! is_null(self::$db)) {
            return true;
        }
        $config = config('DATABASE.JSON');
        self::$db = self::instance();
        
        return true;
    }
    /**
     * 
     */
    public static function DB()
    {
        return self::$db;
    }
    /**
     * 
     */
    public static function close()
    {
        self::$db = null;
    }
    /**
     * 
     */
    public static function lastID()
    {
        if(! is_null(self::$db)){
            return self::$db->lastID;
        } else {
            return -1;
        }
    }
    /**
     * 
     */
    public static function logError(int $userID, string $message, $ip = null)
    {
        $fields = array(
            'user_id' => $userID,
            'ip' => $ip,
            'message' => $message,
            'page' => $_SERVER['REQUEST_URI']
        );
        // $statement = self::prepare("INSERT INTO `data_errors` (`user_id`, `ip`, `message`, `page`) VALUES (:user_id, :ip, :message, :page)");
        // $statement->execute($fields);
    }
}