<?php
namespace App\Traits;

use App\Helpers\QueryHelper;
/**
 * 
 */
trait Feedback
{
    protected $types = ['message', 'warning', 'error'];
    protected $feedbacks = [];

    public function feedback(string $message, int $type = 0, string $name = '')
    {
        if ($type >= 0 && $type < count($this->types)) {
            $this->feedbacks[$type][] = ['type' => $this->types[$type], 'name' => $name, 'message' => $message];
        }
    }
    public function mergeFeedback($feedback)
    {
        if ($feedback->hasFeedback()) {
            $this->feedbacks = array_merge($this->feedbacks, $feedback->getFeedback());
        }
    }
    public function getFeedback()
    {
        return $this->feedbacks;
    }
    public function printFeedback(string $style = "{message}\n", int $length = 0)
    {
        return QueryHelper::insertCodes($this->feedbackWithType(0) ?? [], $style, true, $length);
    }
    public function hasFeedback()
    {
        return (count($this->feedbacks) > 0);
    }
    public function hasFeedbackWithName(int $type, string $name)
    {
        return ! is_null($this->feedbackWithName($type, $name));
    }
    public function feedbackWithName(int $type, string $name)
    {
        $feedbacks = $this->feedbackWithType($type);
        if (! is_null($feedbacks)) {
            foreach ($feedbacks as $feedback) {
                if ($feedback['name'] === $name) {
                    return $feedback;
                }
            }
        }
        
        return null;
    }
    public function hasFeedbackWithType(int $type)
    {
        return ! is_null($this->feedbackWithType($type));
    }
    public function feedbackWithType(int $type)
    {
        if (isset($this->feedbacks[$type])) {
            return $this->feedbacks[$type];
        }
        
        return null;
    }
}