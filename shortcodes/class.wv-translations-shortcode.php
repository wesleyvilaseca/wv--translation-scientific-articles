<?php

if (!class_exists('WV_Translations_Shortcode')) {
    class WV_Translations_Shortcode
    {
        public function __construct()
        {
            add_shortcode('wv_translations', [$this, 'add_shortcode']);
        }

        public function add_shortcode($atts = [], $content = null, $tag = '')
        {
            global $current_user;
            global $wpdb;
            $q = $wpdb->prepare(
                "SELECT ID, post_author, post_date, post_title, post_status, meta_key, meta_value 
                FROM $wpdb->posts as p
                INNER JOIN $wpdb->translationmeta as tm 
                ON p.ID = tm.translation_id
                WHERE p.post_author = %d
                AND tm.meta_key = 'wv_translations_transliteration'
                AND p.post_status IN ('publish', 'pending')
                ORDER BY p.post_date DESC
                ",
                $current_user->ID
            );
            $results = $wpdb->get_results($q);

            ob_start();
            require(WV_TRANSLATIONS_PATH . 'views/wv-translations_shortcode.php');
            return ob_get_clean();
        }
    }
}
