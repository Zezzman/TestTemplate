<?php
namespace App\Interfaces;
/**
 * 
 */
interface IViewModel
{
    function Messages();
    function addMessage($message);
    function hasMessagesWithName(string $name);
    function messagesWithName(string $name);
    function convertToMessage($feedback);
}