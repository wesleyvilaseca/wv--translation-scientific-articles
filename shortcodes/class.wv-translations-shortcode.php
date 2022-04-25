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
            // $atts = array_change_key_case((array) $atts, CASE_LOWER);
            // extract(
            //     shortcode_atts(
            //         [
            //             'id' => '',
            //             'orderby' => 'date',
            //         ],
            //         $atts,
            //         $tag
            //     )
            // );

            // if (!empty($id))
            //     $id = array_map('absint', $explode(',', $id));


            ob_start();
            require(WV_TRANSLATIONS_PATH . 'views/wv-translations_shortcode.php');
            return ob_get_clean();
        }
    }
}
