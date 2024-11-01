<?php
/**
 * Removes titles from posts.
 *
 * @package     wp-4-me-title-remover
 * @category    Core
 * @author      Daryl Peterson (@gmail)
 * @license     https://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace WP4Me_Title_Remover;

/**
 * Write to log files
 *
 * @author Daryl Peterson <daryl.peterson@gmail.com>
 */
class Logger
{

    /**
     * Write formatted debug log
     *
     *
     * $info[] =
     * 0 - class,
     * 1 - function,
     * 2 - line,
     * 3 - wp_error
     *
     * @param array|object|string $context
     * @param array ...$info
     * @return void
     */
    public static function write($context, ...$info)
    {
        $map = [
            'class',
            'function',
            'line',
            'wp_error'
        ];
        extract((array) map($map, $info));

        if (!defined('WP_DEBUG') | !defined('WP_DEBUG_LOG')) {
            return false;
        }

        if (WP_DEBUG !== true | WP_DEBUG_LOG !== true) {
            return false;
        }

        $log = "\n\n";

        if (($class) && is_string($class)) {
            $log .= "CLASS    : $class\n";
        }

        if (($function) && is_string($function)) {
            $log .= "FUNCTION : $function\n";
        }

        if (($line)) {
            $log .= "LINE     : $line\n";
        }

        $log .= "ACTION   : " . current_action() . "\n\n";

        if (is_array($context) || is_object($context)) {
            $log .= print_r($context, true);
        } else {
            $log .= $context;
        }

        if (is_wp_error($wp_error)) {

            /* @var $wp_error \WP_Error */
            $log .= "ERROR     : \n" . $wp_error->get_error_code();
            $log .= "ERROR MSG : \n" . $wp_error->get_error_message();
        }

        $log .= "\n";
        $log .= str_repeat("*", 80);
        $log .= "\n";

        error_log($log);
    }

    /**
     * Capture varible info using var_dump and write it to log
     *
     * @param string|array|object|int|float $context 
     * @param string $class
     * @param string $function
     * @param string $line
     * @return void
     */
    public static function dump($context, $class = '', $function = '', $line = '')
    {
        ob_start();
        var_dump($context);
        $result = ob_get_clean();
        self::write($result, $class, $function, $line);
    }
}
