<?php

if (!class_exists('WV_Translations_Post_Type')) {
    class WV_Translations_Post_Type
    {
        public function __construct()
        {
            add_action('init', [$this, 'create_post_type']);
            add_action('init', [$this, 'create_taxonomy']);
            add_action('init', [$this, 'register_metadata_table']);
            add_action('add_meta_boxes', [$this, 'add_meta_boxes']);
            add_action('wp_insert_post', [$this, 'save_post'], 10, 2);

            add_action('delete_post', [$this, 'delete_post']);

            add_action('pre_get_posts' , [$this, 'add_cpt_author']);
        }

        public function create_post_type()
        {
            register_post_type(
                'wv-translations',
                [
                    'label'         => 'Translation',
                    'description'   => 'Translations',
                    'labels'        => [
                        'name'          => 'Translations',
                        'singular_name' => 'Translation'
                    ],
                    'public'            => true,
                    //page-attributes serve para adicionar hierarquia dos posts  
                    'supports'              => ['title', 'editor', 'author', /*'page-attributes'*/],
                    'rewrite'               => ['slug' => 'translations'],
                    'hierarchical'          => false,
                    'show_ui'               => true,
                    'show_in_menu'          => true,
                    'menu_position'         => 5,
                    'show_in_admin_bar'     => true,
                    'show_in_nav_menus'     => true,
                    'can_export'            => true,
                    'has_archive'           => true, //this plugin will be archive theme file, in this case, a page
                    'exclude_from_search'   => false,
                    'publicly_queryable'    => true,
                    'show_in_rest'          => true,
                    'menu_icon'             => 'dashicons-testimonial'
                ]
            );
        }

        public function create_taxonomy()
        {
            register_taxonomy(
                'singers',
                PLUGIN_KEY,
                [
                    'labels' => [
                        'name' => __('Singers', PLUGIN_KEY),
                        'singular_name' => __('Singer', PLUGIN_KEY)
                    ],
                    'hierarchical' => false,
                    'show_in_rest' => true,
                    'public' => true,
                    'show_admin_column' => true
                ]
            );
        }

        public function register_metadata_table()
        {
            global $wpdb;
            $wpdb->translationmeta = $wpdb->prefix . "translationmeta";
        }

        public function add_meta_boxes()
        {
            add_meta_box(
                'wv_translations_meta_box', //id metabox
                esc_html__('Translations options', PLUGIN_KEY), //title metabox
                [$this, 'add_inner_meta_boxes'], //callback content metaboxe
                PLUGIN_KEY, //screen where metabox it will show up
                'normal', //contexto,
                'high', //priority
            );
        }

        public function add_inner_meta_boxes($post)
        {
            require_once(WV_TRANSLATIONS_PATH . 'views/wv-translations_metabox.php');
        }

        public static function save_post($post_id, $post)
        {

            if (isset($_POST['wv_translations_nonce'])) {
                if (!wp_verify_nonce($_POST['wv_translations_nonce'], 'wv_translations_nonce')) {
                    return;
                }
            }

            if (defined('DOING_AUTOSAVE') and DOING_AUTOSAVE) return;

            if (isset($_POST['post_type']) && $_POST['post_type'] === PLUGIN_KEY) {
                if (!current_user_can('edit_page', $post_id)) {
                    return;
                } elseif (!current_user_can('edit_post', $post_id)) {
                    return;
                }
            }

            if (isset($_POST['action']) && $_POST['action'] == 'editpost') {
                $transliteration = sanitize_text_field($_POST['wv_translations_transliteration']);
                $video = esc_url_raw($_POST['wv_translations_video_url']);

                global $wpdb;

                if ($_POST['wv_translations_action'] == 'save') {
                    if (
                        get_post_type($post) == PLUGIN_KEY and
                        $post->post_status != 'trash' and
                        $post->post_status != 'auto-draft' and
                        $post->post_status != 'draft' and
                        $wpdb->get_var($wpdb->prepare("SELECT translation_id FROM $wpdb->translationmeta WHERE translation_id = %d", $post_id)) == null
                    ) {
                        $wpdb->insert(
                            $wpdb->translationmeta,
                            [
                                'translation_id'    => $post_id,
                                'meta_key'  => 'wv_translations_transliteration',
                                'meta_value'    => $transliteration
                            ],
                            [
                                '%d', '%s', '%s'
                            ]
                        );
                        $wpdb->insert(
                            $wpdb->translationmeta,
                            [
                                'translation_id'    => $post_id,
                                'meta_key'  => 'wv_translations_video_url',
                                'meta_value'    => $video
                            ],
                            [
                                '%d', '%s', '%s'
                            ]
                        );
                    }
                } else {
                    if (get_post_type($post) == 'mv-translations') {
                        $wpdb->update(
                            $wpdb->translationmeta,
                            array(
                                'meta_value'    => $transliteration
                            ),
                            array(
                                'translation_id'    => $post_id,
                                'meta_key'  => 'wv_translations_transliteration',
                            ),
                            array('%s'),
                            array('%d', '%s')
                        );
                        $wpdb->update(
                            $wpdb->translationmeta,
                            array(
                                'meta_value'    => $video
                            ),
                            array(
                                'translation_id'    => $post_id,
                                'meta_key'  => 'wv_translations_video_url',
                            ),
                            array('%s'),
                            array('%d', '%s')
                        );
                    }
                }
            }
        }

        public function delete_post($post_id)
        {
            if (!current_user_can('delete_posts')) {
                return;
            }
            if (get_post_type($post) == PLUGIN_KEY) {
                global $wpdb;
                $wpdb->delete(
                    $wpdb->translationmeta,
                    array('translation_id' => $post_id),
                    array('%d')
                );
            }
        }

        public function add_cpt_author($query)
        {
            if(!is_admin() and $query->is_author() and $query->is_main_query()){
                $query->set('post_type', [PLUGIN_KEY, 'post']);
            }
        }
    }
}
