<?php
if (!is_user_logged_in())
    return wvt_resgister_user();


if (isset($_POST['wv_translations_nonce']))
    if (!wp_verify_nonce($_POST['wv_translations_nonce'], 'wv_translations_nonce')) return;


$error = [];
$hasError = false;

if (isset($_POST['submitted'])) {
    $title              = $_POST['wv_translations_title'];
    $content            = $_POST['wv_translations_content'];
    $singer             = $_POST['wv_translations_singer'];
    $transliteration    = $_POST['wv_translations_transliteration'];
    $video              = $_POST['wv_translations_video_url'];

    if (trim($title) === '') {
        $errors[] = esc_html__('Please, enter a title', PLUGIN_KEY);
        $hasError = true;
    }

    if (trim($content) === '') {
        $errors[] = esc_html__('Please, enter some content', PLUGIN_KEY);
        $hasError = true;
    }

    if (trim($singer) === '') {
        $errors[] = esc_html__('Please, enter some singer', PLUGIN_KEY);
        $hasError = true;
    }

    if ($hasError == false) {
        $post_info = array(
            'post_type' => PLUGIN_KEY,
            'post_title'    => sanitize_text_field($title),
            'post_content'  => wp_kses_post($content),
            'tax_input' => [
                'singers'   => sanitize_text_field($singer)
            ],
            'post_status'   => 'pending',
            'ID' => $_GET['post']
        );

        $post_id = wp_update_post($post_info);

        global $post;
        WV_Translations_Post_Type::save_post($post_id, $post);
    }
}
?>
<div class="mv-translations">
    <form action="" method="POST" id="translations-form">
        <h2><?php esc_html_e('Submit new translation', PLUGIN_KEY); ?></h2>

        <?php
        if ($errors != '') {
            foreach ($errors as $error) {
        ?>
                <span class="error">
                    <?php echo $error; ?>
                </span>
        <?php
            }
        }
        ?>

        <label for="wv_translations_title"><?php esc_html_e('Title', PLUGIN_KEY); ?> *</label>
        <input type="text" name="wv_translations_title" id="wv_translations_title" value="<?= esc_html($results[0]['post_title'])?>" required />
        <br />
        <label for="wv_translations_singer"><?php esc_html_e('Singer', PLUGIN_KEY); ?> *</label>
        <input type="text" name="wv_translations_singer" id="wv_translations_singer" value="<?= strip_tags(get_the_term_list($_GET['post'], 'singers', '', ', ')) ?>" required />

        <br />
        <?= wp_editor($results[0]['post_content'], 'wv_translations_content', array('wpautop' => true, 'media_buttons' => false));?>
        </br />

        <fieldset id="additional-fields">
            <label for="wv_translations_transliteration"><?php esc_html_e('Has transliteration?', PLUGIN_KEY); ?></label>
            <select name="wv_translations_transliteration" id="wv_translations_transliteration">
                <option value="Yes" <?php selected($results[0]['meta_value'], "Yes"); ?>><?php esc_html_e('Yes', PLUGIN_KEY); ?></option>
                <option value="No" <?php selected($results[0]['meta_value'], "No"); ?>><?php esc_html_e('No', PLUGIN_KEY); ?></option>
            </select>
            <label for="wv_translations_video_url"><?php esc_html_e('Video URL', PLUGIN_KEY); ?></label>
            <input type="url" name="wv_translations_video_url" id="wv_translations_video_url" value="<?= esc_url($results[1]['meta_value']) ?>" />
        </fieldset>
        <br />
        <input type="hidden" name="wv_translations_action" value="update">
        <input type="hidden" name="action" value="editpost">
        <input type="hidden" name="wv_translations_nonce" value="<?php echo wp_create_nonce('wv_translations_nonce'); ?>">
        <input type="hidden" name="submitted" id="submitted" value="true" />
        <input type="submit" name="submit_form" value="<?php esc_attr_e('Submit', PLUGIN_KEY); ?>" />
    </form>
</div>

<script>
    if(window.history.replaceState){
        window.history.replaceState(null, null, window.location.href);
    }
</script>