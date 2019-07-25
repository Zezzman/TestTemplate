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
    public $exception = null;

    public function Message(string $name = '', string $style = "{message}<br>", int $length = 0)
    {
        if ($name !== '') {
            return QueryHelper::insertCodes($this->messagesWithName($name) ?? [], $style, true, $length);
        }
        return QueryHelper::insertCodes(ArrayHelper::outerMerge($this->messages), $style, true, $length);
    }
    public function addMessage(string $message, string $name = '', array $attributes = null)
    {
        if (empty($name)) {
            $name = 'default';
        }
        $message = ['name' => $name, 'message' => $message];
        if (! is_null($attributes)) {
            $message = array_merge($message, $attributes);
        }
        $this->messages[$name][] = $message;
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
    public function Exception()
    {
        return (config('DEBUG') && ! is_null($this->exception)) ? $this->exception->getMessage() : '';
    }
}