<?php
namespace App\Helpers;

use App\Helper;
/**
 * 
 */
final class TimeHelper extends Helper
{
    protected $datePhase = null;
    protected $dateString = null;
    protected $microtime = null;
    /**
     * Create date instance
     * 
     * @param       string      $dateString       time string
     * 
     * @return      self        date instance
     */
    public static function create(string $dateString = null, string $format = null)
    {
        $timer = new self();
        if (is_null($dateString) || empty($dateString)) {
            $timer->dateString = date_create();
        } else {
            if (is_null($format)) {
                $timer->dateString = date_create($dateString);
            } else {
                $timer->dateString = date_create_from_format($format, $dateString);
            }
        }
        if (! ($timer->dateString)) {
            $timer->dateString = false;
        } else {
            $timer->datePhase = $timer->dateString->format('a');
        }

        return $timer;
    }
    /**
     * Create date instance with elapsed time set
     * 
     * @return      self        date instance
     */
    public static function elapse()
    {
        $timer = self::create();
        $timer->microtime = microtime(true);
        return $timer;
    }
    /**
     * Capture elapsed microtime
     * 
     * @return      int        elapsed microtime
     */
    public function capture(bool $reset = false)
    {
        if (is_numeric($this->microtime)) {
            $elapseTime = (microtime(true) - $this->microtime);
            if($reset){
                $this->microtime = microtime(true);
            }
            return $elapseTime;
        }
        return -1;
    }
    /**
     * Format time based on dateString variable
     * 
     * @param   string      $format          format of time
     * 
     * @return   string     formated time
     */
    public function format(string $format = 'Y-m-d H:i:s')
    {
        if ($this->dateString) {
            return $this->dateString->format($format);
        }
        return '';
    }
    /**
     * Add an amount days/weeks/months/years to date instance
     * 
     * @param       int         $amount         amount to add
     * @param       string      $type           type of amount
     * 
     * @return      self        date instance
     */
    public function add(int $amount, string $type = 'days')
    {
        $interval = '';
        if ($type === 'days') {
            $interval = 'P'. abs($amount). 'D';
        } elseif ($type === 'months') {
            $interval = 'P'. abs($amount). 'M';
        } elseif ($type === 'years') {
            $interval = 'P'. abs($amount). 'Y';
        } elseif ($type === 'weeks') {
            $interval = 'P'. abs($amount). 'W';
        }
        if ($interval !== '') {
            $this->dateString->add(new \DateInterval($interval));
        }
        return $this;
    }
    /**
     * Subtract an amount days/weeks/months/years to date instance
     * 
     * @param       int         $amount         amount to add
     * @param       string      $type           type of amount
     * 
     * @return      self        date instance
     */
    public function subtract(int $amount, string $type = 'days')
    {
        $interval = '';
        if ($type === 'days') {
            $interval = 'P'. abs($amount). 'D';
        } elseif ($type === 'months') {
            $interval = 'P'. abs($amount). 'M';
        } elseif ($type === 'years') {
            $interval = 'P'. abs($amount). 'Y';
        } elseif ($type === 'weeks') {
            $interval = 'P'. abs($amount). 'W';
        }
        if ($interval !== '') {
            $this->dateString->sub(new \DateInterval($interval));
        }
        return $this;
    }
    /**
     * Set date instance timezone
     * 
     * @param       string      $timezone       DateTime Timezone
     * 
     * @return      bool        true if timezone is set
     */
    public function setTimezone(string $timezone)
    {
        if ($this->dateString) {
            try{
                $timezone = new \DateTimeZone($timezone);
                if ($timezone) {
                    $this->dateString->setTimezone($timezone);
                    $this->datePhase = $this->format('a');
                }
                return true;
            } catch (Exception $e) {
                throw new Exception("Timezone Set Error: (". $e. ")");
            }
        }
    }
    /**
     * smaller than date given
     * 
     * @param       string          $date               dateString
     * 
     * @return      bool            true if given date is larger than date instance
     */
    public function smallerThan($date = null)
    {
        if (! ($this->dateString)) {
            return false;
        }
        if (is_string($date)) {
            $date = self::create($date);
        } elseif (! is_object($date) && ! ($date instanceof self)){
            return false;
        }

        if (! ($date->dateString)) {
            return false;
        }
        $diff = date_diff($this->dateString, $date->dateString);
        return ($diff->invert === 0);
    }
    /**
     * larger than date given
     * 
     * @param       string          $date               dateString
     * 
     * @return      bool            true if given date is smaller than date instance
     */
    public function largerThan($date = null)
    {
        return ! $this->smallerThan($date);
    }
    /**
     * Difference between two DateTime objects
     * 
     * @param       DateTime        $date             time string
     * 
     * @return      date_diff      true if given time is larger than time instance
     */
    public function difference($date = null)
    {
        if (! ($this->dateString)) {
            return false;
        }
        if (is_string($date)) {
            $date = self::create($date);
        } elseif (! is_object($date) && ! ($date instanceof self)){
            return false;
        }

        if (! ($date->dateString)) {
            return false;
        }
        return date_diff($this->dateString, $date->dateString);
    }
}