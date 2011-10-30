<?php

namespace DTL\MVC;

class DummyLogger
{
    // ZF Logger Compat
    public function debug($message, array $context = array())
    {
    }

    public function err($message, array $context = array())
    {
    }

    public function crit($message, array $context = array())
    {
    }

    public function alert($message, array $context = array())
    {
    }

    public function emerg($message, array $context = array())
    {
    }

    public function info($message, array $context = array())
    {
    }
}

/**
 * Logger 
 *
 * Static class to encapsulate MVC logging.
 *
 * Logging channels can be used for each component
 *
 * Logger::controller()->info('Foo', $context);
 * Logger::view()->debug('Bar');
 *
 * Channels can be initialized before the first request is made
 *
 * Logger::initController($monoLogInstance);
 * Logger::initView($monoLogInstance);
 *
 * If no logger given, dummy logger (defined above) is used.
 *
 * etc.
 * 
 * @author Daniel Leech <daniel@dantleech.com> 
 */
class Logger
{
    static $instances = array();

    public static function initController($instance)
    {
        self::$instances['controller'] = $instance;
    }

    private static function get($context)
    {
        if (isset(self::$instances[$context])) {
            return self::$instances[$context];
        } else {
            self::$instances[$context] = new DummyLogger;
            return self::get();
        }
    }

    public static function controller()
    {
        return self::get('controller');
    }
}
