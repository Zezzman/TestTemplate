<?php
namespace App\Interfaces;
/**
 * 
 */
interface IUserAuth
{
    public function hasRequiredSignUpFields();
    public function hasRequiredLoginFields();
}