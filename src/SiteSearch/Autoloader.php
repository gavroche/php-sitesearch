<?php

namespace SiteSearch;

/**
 * Autoloads SiteSearch classes
 *
 * @package sitesearch
 */
class Autoloader
{
    /**
     * Register the autoloader
     *
     * @return  void
     */
    public static function register()
    {
        spl_autoload_register(array(new self, 'autoload'));
    }

    /**
     * Autoloader
     *
     * @param   string
     * @return  mixed
     */
    public static function autoload($class)
    {
        if (0 === stripos($class, 'SiteSearch')) {
            $file = preg_replace('{^SiteSearch\\\?}', '', $class);
            $file = str_replace('\\', '/', $file);
            $file = realpath(__DIR__ . (empty($file) ? '' : '/') . $file . '.php');
            if (is_file($file)) {
                require_once $file;
                return true;
            }
        }
        return null;
    }
}