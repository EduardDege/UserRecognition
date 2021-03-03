<?php

/**
 * Plugin Name: Quick Download Button 
 * Plugin URI: https://github.com/kusimo/quick-download-button
 * Description: Use to add download button link to post or page.
 * Version: 1.0.0
 * Author: Abidemi Kusimo
 *
 * @package quick-download-button
 */

defined('ABSPATH') || exit;

/**
 * Load translations for the plugin from the /languages/ folder.
 * 
 * @link https://developer.wordpress.org/reference/functions/load_plugin_textdomain/
 */

add_action('init', 'qdbu_quick_download_button_load_textdomain');

function qdbu_quick_download_button_load_textdomain()
{
    load_plugin_textdomain('quick-download-button', false, basename(__DIR__) . '/languages');
}

/**
 * Registers all block assets so that they can be enqueued through the Block Editor in
 * the corresponding context.
 *
 * @link https://wordpress.org/gutenberg/handbook/designers-developers/developers/block-api/block-registration/
 */
add_action('init', 'qdbu_quick_download_button_register_blocks');

function qdbu_quick_download_button_register_blocks()
{

    //If Block Editor is not active, bail.
    if (!function_exists('register_block_type')) {
        return;
    }

    /**
     * Use asset file to automatically set the dependency list for enqueuing the script
     */
    $quick_download_button_asset_file = include(plugin_dir_path(__FILE__) . 'build/index.asset.php');

    //Register the block editor script
    wp_register_script(
        'quick-download-button-editor-script',                       // label
        plugins_url('build/index.js', __FILE__),                     // script file
        $quick_download_button_asset_file['dependencies'],           // dependencies 
        filemtime(plugin_dir_path(__FILE__) . 'build/index.js')      // set version as file last modified time
    );

    //Localise script - download page ID
    wp_localize_script(
        'quick-download-button-editor-script',
        'qdbu_data',
        array(
            'download_page_id' => (int)get_option('qdbu_quick_download_button_page_id')
        )
    );

    // Register the block editor stylesheet.
    wp_register_style(
        'quick-download-button-editor-styles',                      // label
        plugins_url('css/editor.css', __FILE__),                    // CSS file
        array('wp-edit-blocks'),                                    // dependencies
        filemtime(plugin_dir_path(__FILE__) . 'css/editor.css')     // set version as file last modified time
    );

    // Register the front-end stylesheet.
    wp_register_style(
        'quick-download-button-front-end-styles',                     // label
        plugins_url('css/style.css', __FILE__),                       // CSS file
        array(),                                                      // dependencies
        filemtime(plugin_dir_path(__FILE__) . 'css/style.css')        // set version as file last modified time
    );

    //Register blocks script and styles
    register_block_type('quick-download-button/download-button', array(
        'editor_script' => 'quick-download-button-editor-script',                    // Calls registered script above
        'editor_style' => 'quick-download-button-editor-styles',                    // Calls registered stylesheet above
        'style' => 'quick-download-button-front-end-styles',
    ));

    if (function_exists('wp_set_script_translations')) {
        /**
         * Adds internationalization support. 
         * 
         * @link https://wordpress.org/gutenberg/handbook/designers-developers/developers/internationalization/
         * @link https://make.wordpress.org/core/2018/11/09/new-javascript-i18n-support-in-wordpress/
         */
        wp_set_script_translations('quick-download-button-editor-script', 'quick-download-button', plugin_dir_path(__FILE__) . '/languages');
    }
}



/**
 * Front end Script
 */


function qdbu_button_front_end_script()
{
    wp_enqueue_script(
        'quick-download-button-frontend-script',                    // label
        plugins_url('frontend/index.js', __FILE__),            // script file
        array('jquery'),        // dependencies
        filemtime(plugin_dir_path(__FILE__) . 'frontend/index.js')        // set version as file last modified time
        ,
        true
    );

    wp_localize_script('quick-download-button-frontend-script', 'quick_download_object', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'security' => wp_create_nonce('qdbutton_nonce_action'),
        'redirecturl' => qdbu_default_url()
    ));
}
add_action('wp_enqueue_scripts', 'qdbu_button_front_end_script');

add_action('wp_ajax_nopriv_quick-download-button-frontend-script', 'qdbu_download_ajax_referer');
add_action('wp_ajax_quick-download-button-frontend-script', 'qdbu_download_ajax_referer');


/**
 * Nonce to be used for download page 
 */

function qdbu_download_ajax_referer()
{
    //nonce-field is created on page
    check_ajax_referer('qdbutton_nonce_action', 'security');

    $return = array();
    $return . array_push($script_params);

    echo $return;

    wp_die(); // You missed this too
}

/**
 * Shortcode
 */

require_once('class/shortcode.class.php');
$downloadShortcode = new QDBU_QuickDownloadShortCode(__FILE__);
$downloadShortcode = new QDBU_QuickDownloadShortCode;


/**
 * Set default download page to 'quick-download-button/' 
 * Incase user change the page slug, we use download page by ID
 * Download page name can be renamed before using the plugin. Rename just once and before using 
 * */
if (!function_exists('qdbu_default_url')) {
    function qdbu_default_url()
    {

        $quick_download_button_default_page = site_url() . '/quick-download-button/';

        if (FALSE !== get_option('qdbu_quick_download_button_page_id')) {
            $quick_download_button_default_page = get_page_link(get_option('qdbu_quick_download_button_page_id'));
        }
        return $quick_download_button_default_page;
    }
}



add_filter('template_include', 'qdbu_download_button_plugin_templates');

/**
 * Get Custom Template for the download page
 *
 * @param  mixed $template
 * @return void
 */
function qdbu_download_button_plugin_templates($template)
{

    $quick_download_button_pid = get_option('qdbu_quick_download_button_page_id');

    //Let's check we are on the right page and template file exists
    if (is_page('quick-download-button') || is_page($quick_download_button_pid) && file_exists(plugin_dir_path( __DIR__ ) . 'quick-download-button/templates/qdb-page-template.php')) {

        $template = dirname(__FILE__) . '/templates/qdb-page-template.php';
    }
    return $template;
}

/**
 * Create download page after plugin activation
 */

require_once('class/create.downloadpage.class.php');

$create_download_page = new QDBU_CreateDownloadPage;

register_activation_hook(__FILE__, array($create_download_page, 'quickDownloadPage'));
