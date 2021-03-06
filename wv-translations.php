<?php

/**
 * Plugin Name: WV Translations
 * Plugin URI: https://www.wordpress.org/wv-translations
 * Description: My plugin's description
 * Version: 1.0
 * Requires at least: 5.9
 * Requires PHP: 7.4
 * Author: Wesley Vila Seca
 * Author URI: https://www.codevila.com.br
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wv-translations
 * Domain Path: /languages
 */
/*
WV Translations is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
WV Translations is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with WV Translations. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!class_exists('WV_Translations')) {

    class WV_Translations
    {

        public function __construct()
        {
            $this->define_constants();

            require_once(WV_TRANSLATIONS_PATH . 'post-types/class.' . PLUGIN_KEY . '-cpt.php');
            require_once(WV_TRANSLATIONS_PATH . 'shortcodes/class.' . PLUGIN_KEY . '-shortcode.php');
            require_once(WV_TRANSLATIONS_PATH . 'shortcodes/class.' . PLUGIN_KEY . '-edit-shortcode.php');
            require_once(WV_TRANSLATIONS_PATH . 'functions/functions.php');

            $WVTranslatiosPostTypes = new WV_Translations_Post_Type;
            $WVTranslationsShortCode = new WV_Translations_Shortcode;
            $WVTranslationsEditShortCode = new WV_Translations_EditShortcode;

            add_filter('single_template', [$this, 'load_single_template']);
        }

        public function define_constants()
        {
            // Path/URL to root of this plugin, with trailing slash.
            define('WV_TRANSLATIONS_PATH', plugin_dir_path(__FILE__));
            define('WV_TRANSLATIONS_URL', plugin_dir_url(__FILE__));
            define('PLUGIN_KEY', 'wv-translations');
            define('WV_TRANSLATIONS_VERSION', '1.0.0');
        }

        /**
         * Activate the plugin
         */
        public static function activate()
        {
            update_option('rewrite_rules', '');

            global $wpdb;
            $table_name = $wpdb->prefix . "translationmeta";
            $mvt_db_version = get_option('wv_translation_db_version');

            if (empty($mvt_db_version)) {

                global $wpdb;
                $createSql = "
                CREATE TABLE `" . $table_name . "` (
                    `meta_id` BIGINT(20) unsigned NOT NULL AUTO_INCREMENT,
                    `translation_id` BIGINT(20) NOT NULL DEFAULT '0',
                    `meta_key` VARCHAR(255) DEFAULT NULL,
                    `meta_value` LONGTEXT,
                    PRIMARY KEY (`meta_id`),
                    KEY `translation_id` (`translation_id`),
                    KEY `meta_key` (`meta_key`)
                )ENGINE=InnoDB " . $wpdb->get_charset_collate() . ";";

                require_once(ABSPATH . "/wp-admin/includes/upgrade.php");

                dbDelta($createSql, true);

                $mvt_db_version = "1.0";

                add_option('wv_translation_db_version', $mvt_db_version);
            }

            if ($wpdb->get_row("SELECT post_name FROM {$wpdb->prefix}posts WHERE post_name = 'submit-translation'") == null) {
                $page = [
                    'post_title' => __('Submit Translation', PLUGIN_KEY),
                    'post_name' => 'submit-translation',
                    'post_status' => 'publish',
                    'post_author' => wp_get_current_user()->ID,
                    'post_type' => 'page',
                    'post_content' => '<!-- wp:shortcode -->[wv_translations]<!-- /wp:shortcode -->'
                ];
                wp_insert_post($page);
            }

            if ($wpdb->get_row("SELECT post_name FROM {$wpdb->prefix}posts WHERE post_name = 'edit-translation'") == null) {
                $page = [
                    'post_title' => __('Edit Translation', PLUGIN_KEY),
                    'post_name' => 'edit-translation',
                    'post_status' => 'publish',
                    'post_author' => wp_get_current_user()->ID,
                    'post_type' => 'page',
                    'post_content' => '<!-- wp:shortcode -->[wv_translations_edit]<!-- /wp:shortcode -->'
                ];
                wp_insert_post($page);
            }
        }

        /**
         * Deactivate the plugin
         */
        public static function deactivate()
        {
            flush_rewrite_rules();
            unregister_post_type(PLUGIN_KEY);
        }

        /**
         * Uninstall the plugin
         */
        public static function uninstall()
        {
            delete_option( 'wv_translation_db_version' );

            global $wpdb;

            $wpdb->query(
                "DELETE FROM $wpdb->posts
                WHERE post_type = 'wv-translations'" 
            );

            $wpdb->query(
                "DELETE FROM $wpdb->posts
                WHERE post_type = 'page'
                AND post_name IN( 'submit-translation', 'edit-translation' )"
            );

            $wpdb->query( $wpdb->prepare(
                "DROP TABLE IF EXISTS %s",
                $wpdb->prefix . 'translationmeta'
            ));            
        }

        public function load_single_template($tpl)
        {
            if( is_singular( 'wv-translations' ) ){
                $tpl = WV_TRANSLATIONS_PATH . 'views/templates/single-wv-translations.php';
            }
            return $tpl;
        }
    }
}

// Plugin Instantiation
if (class_exists('WV_Translations')) {

    // Installation and uninstallation hooks
    register_activation_hook(__FILE__, array('WV_Translations', 'activate'));
    register_deactivation_hook(__FILE__, array('WV_Translations', 'deactivate'));
    register_uninstall_hook(__FILE__, array('WV_Translations', 'uninstall'));

    // Instatiate the plugin class
    $Wv_translations = new WV_Translations();
}
