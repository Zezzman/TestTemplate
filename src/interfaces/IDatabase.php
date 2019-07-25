<?php
namespace App\Interfaces;
/**
 *
 * @author  Francois Le Roux <francoisleroux97@gmail.com>
 */
interface IDatabase
{
    /**
     * 
     */
    public static function instance();
    /**
     * 
     */
    public static function connect();
    /**
     * 
     */
    public static function DB();
    /**
     * 
     */
    public static function close();
    /**
     * 
     */
    public static function prepare($query, array $params = null, int $fetch = null, bool $group = false);
    /**
     * 
     */
    public static function lastID();
    /**
     * 
     */
    public static function logError($user_id, $message, $ip = '');
}