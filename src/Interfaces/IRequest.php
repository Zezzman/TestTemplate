<?php
namespace App\Interfaces;
/**
 * 
 */
interface IRequest
{
    function valid();

    static function empty();
}