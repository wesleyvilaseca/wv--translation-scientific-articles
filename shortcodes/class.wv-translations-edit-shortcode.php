<?php

if (!class_exists('WV_Translations_EditShortcode')) {
    class WV_Translations_EditShortcode
    {
        public function __construct()
        {
            add_shortcode('wv_translations_edit', [$this, 'add_shortcode']);
        }

        public function add_shortcode($atts = [], $content = null, $tag = '')
        {
            global $current_user;
            global $wpdb;
            $q = $wpdb->prepare(
                "SELECT ID, post_author, post_title, post_content, meta_key, meta_value
                    FROM $wpdb->posts AS p
                    INNER JOIN $wpdb->translationmeta AS tm
                    ON p.ID = tm.translation_id
                    WHERE p.ID = %d
                    AND p.post_author = %d
                    ORDER BY p.post_date DESC",
                $_GET['post'],
                $current_user->ID
            );
            $results = $wpdb->get_results($q, ARRAY_A);

            if (current_user_can('edit_post', $_GET['post'])) :
                ob_start();
                require(WV_TRANSLATIONS_PATH . 'views/wv-translations_edit_shortcode.php');
                return ob_get_clean();
            endif;
        }
    }
}
