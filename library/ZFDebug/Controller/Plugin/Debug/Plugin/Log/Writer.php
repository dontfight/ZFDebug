<?php
/**
 * ZFDebug Zend Additions
 *
 * @category   ZFDebug
 * @package    ZFDebug_Controller
 * @subpackage Plugins
 * @copyright  Copyright (c) 2008-2009 ZF Debug Bar Team (http://code.google.com/p/zfdebug)
 * @license    http://code.google.com/p/zfdebug/wiki/License     New BSD License
 * @version    $Id$
 */
class ZFDebug_Controller_Plugin_Debug_Plugin_Log_Writer extends Zend_Log_Writer_Abstract
{
    protected $messages = [ ];
    protected $errors = 0;

    public static function factory($config)
    {
        return new self();
    }

    public function getMessages()
    {
        return $this->messages;
    }

    public function getErrorCount()
    {
        return $this->errors;
    }

    /**
     * Write a message to the log.
     *
     * @param array $event event data
     *
     */
    protected function _write($event)
    {
        $output = '<tr>';
        $output .= '<td style="color:%color%;text-align:right;padding-right:1em">%priorityName%</td>';
        $output .= '<td style="color:%color%;text-align:right;padding-right:1em">%memory%</td>';
        $output .= '<td style="color:%color%;">%message%</td></tr>'; // (%priority%)
        $event['color'] = '#C9C9C9';
        // Count errors
        if ($event['priority'] < 7) {
            $event['color'] = 'green';
        }
        if ($event['priority'] < 6) {
            $event['color'] = '#fd9600';
        }
        if ($event['priority'] < 5) {
            $event['color'] = 'red';
            $this->errors++;
        }

        if ($event['priority'] == ZFDebug_Controller_Plugin_Debug_Plugin_Log::ZFLOG) {
            $event['priorityName'] = $event['message']['time'];
            $event['memory'] = $event['message']['memory'];
            $event['message'] = $event['message']['message'];
        } else {
            $event['message'] = $event['priorityName'] . ': ' . print_r($event['message'], 1);
            $event['priorityName'] = '&nbsp;';
            $event['memory'] = '&nbsp;';
        }
        foreach ($event as $name => $value) {
            if ('message' == $name) {
                if ((is_object($value) && !method_exists($value, '__toString'))) {
                    $value = gettype($value);
                } elseif (is_array($value)) {
                    $value = $value[1];
                }
            }
            $output = str_replace("%$name%", $value, $output);
        }
        $this->messages[] = $output;
    }
}
