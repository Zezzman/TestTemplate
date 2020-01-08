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
    static function instance();
    /**
     * 
     */
    static function connect();
    /**
     * 
     */
    static function DB();
    /**
     * 
     */
    static function close();
    /**
     * 
     */
    static function prepare($query, array $params = null, int $fetch = null, bool $group = false);
    /**
     * 
     */
    static function lastID();
    /**
     * 
     */
    static function logError($user_id, $message, $ip = '');
}