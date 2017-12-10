<?php
namespace Larabase54;

use Log;

class Logger
{
    /**
     * Debug: debug-level messages
     * @param $tag
     * @param $message
     * @param array $context
     */
    public static function debug($tag, $message, $context = array())
    {
        if (config('app.debug', false)) {
            Log::debug($tag . $message, $context);
        }
    }
    
    /**
     * Informational: informational messages
     * @param $tag
     * @param $message
     * @param array $context
     */
    public static function info($tag, $message, $context = array())
    {
        Log::info($tag . $message, $context);
    }
    
    /**
     * Notice: normal but significant condition
     * @param $tag
     * @param $message
     * @param array $context
     */
    public static function notice($tag, $message, $context = array())
    {
        Log::notice($tag . $message, $context);
    }
    
    /**
     * Warning: warning conditions
     * @param $tag
     * @param $message
     * @param array $context
     */
    public static function warning($tag, $message, $context = array())
    {
        Log::warning($tag . $message, $context);
    }
    
    /**
     * Error: error conditions
     * @param $tag
     * @param $message
     * @param array $context
     */
    public static function error($tag, $message, $context = array())
    {
        Log::error($tag . $message, $context);
    }
    
    /**
     * Critical: critical conditions
     * @param $tag
     * @param $message
     * @param array $context
     */
    public static function critical($tag, $message, $context = array())
    {
        Log::critical($tag . $message, $context = array());
    }
    
    /**
     * Alert: action must be taken immediately
     * @param $tag
     * @param $message
     * @param array $context
     */
    public static function alert($tag, $message, $context = array())
    {
        Log::alert($tag . $message, $context);
    }
    
    /**
     * Emergency: system is unusable
     * @param $tag
     * @param $message
     * @param array $context
     */
    public static function emergency($tag, $message, $context = array())
    {
        Log::emergency($tag . $message, $context);
    }
}