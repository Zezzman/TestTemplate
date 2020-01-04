<?php
namespace App\ViewModels;

use App\Interfaces\IViewModel;
use App\Helpers\QueryHelper;
use App\Helpers\ArrayHelper;
/**
 * 
 */
class ViewModel implements IViewModel
{
    public $messages = [];

    public function Messages(string $name = '', string $style = "{message}<br>", int $length = 0)
    {
        if ($name !== '') {
            return QueryHelper::scanCodes($this->messagesWithName($name) ?? [], $style, [], true, $length);
        }
        return QueryHelper::scanCodes(ArrayHelper::outerMerge($this->messages), $style, [], true, $length);
    }
    public function addMessage($message, string $name = '', array $attributes = null)
    {
        if (empty($name)) {
            $name = 'default';
        }
        if (is_string($message) || is_numeric($message)) {
            $message = ['name' => $name, 'message' => $message];
            if (! is_null($attributes)) {
                $message = array_merge($message, $attributes);
            }
            $this->messages[$name][] = $message;
        } else {
            $groups = (array) $message;
            foreach ($groups as $key => $content) {
                $message = [];
                if (is_string($content) || is_numeric($content)) {
                    $message['name'] = $name;
                    $message['message'] = $content;
                    if (! is_null($attributes)) {
                        $message = array_merge($message, $attributes);
                    }
                } else {
                    $message = (array) $content;
                    $message['name'] = $name;
                    if (! is_null($attributes)) {
                        $message = array_merge($message, $attributes);
                    }
                }
                $this->messages[$name][] = $message;
            }
        }
    }
    public function hasMessagesWithName(string $name)
    {
        return ! is_null($this->messagesWithName($name));
    }
    public function messagesWithName(string $name)
    {
        if (isset($this->messages[$name])) {
            return $this->messages[$name];
        }
        return null;
    }
    public function convertToMessage($feedback)
    {
        $feedbacks = $feedback->feedbackWithType(0);
        if (! is_null($feedbacks)) {
            foreach ($feedbacks as $feedback) {
                $this->addMessage($feedback['message'], $feedback['name']);
            }
        }
    }
}