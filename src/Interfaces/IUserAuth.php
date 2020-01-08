<?php
namespace App\Interfaces;
/**
 * 
 */
interface IUserAuth
{
    function hasRequiredSignUpFields();
    function hasRequiredLoginFields();
}