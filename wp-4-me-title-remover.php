<?php

/**
 * Plugin Name:   WP 4 Me Title Remover
 * Plugin URI:    http://dpeterson.org/wp-4-me-title-remover
 * Description:   Allows you to hide the title of any post, page or custom post type. Does not affecting menus or titles in the admin area.
 * Author:        Daryl Peterson
 * Author URI:    http://dpeterson.org
 * Version:       1.0
 * License:       https://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:   wp-4-me-title-remover
 *
 * @package   wp-4-me-title-remover
 * @category  Core
 * @author    Daryl Peterson (@gmail)
 * @license   GPLv2 or later
 * @since     1.0
 */

namespace WP4Me_Title_Remover;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}
const PLUGIN_NAME = "WP 4 Me Title Remover";
const PLUGIN_PREFIX = "wp4me_title_remover";
const PLUGIN_DOMAIN = "wp4me-title-remover";
const PLUGIN_VER_PHP = "7.0";
const PLUGIN_VER_WP = "5.0";
const PLUGIN_FILE = __FILE__;
const PLUGIN_INIT = true;


// Setup autoloading
if (file_exists(dirname(__FILE__) . '/vendor/autoload.php')) {
    require_once dirname(__FILE__) . '/vendor/autoload.php';
}

// instantiate the plugin class
$plugin = new Plugin();
$plugin->init();
