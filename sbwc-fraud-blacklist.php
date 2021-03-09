<?php

/** Plugin Name: SBWC Fraud Blacklist
 * Description: Allows users to define blacklisted WooCommerce addresses and associated payment gateways to be hidden from user billing locations suspected of fraud.
 * Author: WC Bessinger
 * Version: 1.0.0
 */

if (!defined('ABSPATH')) :
    exit();
endif;

define('SBWCFB_PATH', plugin_dir_path(__FILE__));
define('SBWCFB_URL', plugin_dir_url(__FILE__));

add_action('plugins_loaded', 'sbwcfb_init');
function sbwcfb_init()
{
    // css & js
    add_action('admin_enqueue_scripts', 'sbwcfb_scripts');
    function sbwcfb_scripts()
    {
        wp_enqueue_script('sbwcfb-admin', SBWCFB_URL . 'assets/admin.js', ['jquery']);
        wp_enqueue_style('sbwcfb-admin', SBWCFB_URL . 'assets/admin.css');
    }

    // functions
    include SBWCFB_PATH . 'functions/admin.php';
}
