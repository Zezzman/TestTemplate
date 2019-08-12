<?php
namespace App\Interfaces;
/**
 * 
 */
interface IRequest
{
    public function valid();

    public static function empty();
}