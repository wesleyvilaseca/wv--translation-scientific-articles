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
            'post_status'   => 'pending'
        );

        $post_id = wp_insert_post($post_info);

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
            foreach ($errors as $error) :
        ?>
                <span class="error"><?php echo $error ?></span>
        <?php
            endforeach;
        }
        ?>

        <label for="wv_translations_title"><?php esc_html_e('Title', PLUGIN_KEY); ?> *</label>
        <input type="text" name="wv_translations_title" id="wv_translations_title" value="<?= @$title ?>" required />
        <br />
        <label for="wv_translations_singer"><?php esc_html_e('Singer', PLUGIN_KEY); ?> *</label>
        <input type="text" name="wv_translations_singer" id="wv_translations_singer" value="<?= @$singer ?>" required />

        <br />
        <?php
        wp_editor(@$content, 'wv_translations_content', ['wpautop' => true, 'media_buttons' => false]);
        ?>
        </br />

        <fieldset id="additional-fields">
            <label for="wv_translations_transliteration"><?php esc_html_e('Has transliteration?', PLUGIN_KEY); ?></label>
            <select name="wv_translations_transliteration" id="wv_translations_transliteration">
                <option value="Yes" <?= @isset($transliteration) ? selected($transliteration, 'Yes') : ''  ?>><?php esc_html_e('Yes', PLUGIN_KEY); ?></option>
                <option value="No" <?= @isset($transliteration) ? selected($transliteration, 'No') : ''  ?>><?php esc_html_e('No', PLUGIN_KEY); ?></option>
            </select>
            <label for="wv_translations_video_url"><?php esc_html_e('Video URL', PLUGIN_KEY); ?></label>
            <input type="url" name="wv_translations_video_url" id="wv_translations_video_url" value="<?= @$video ?>" />
        </fieldset>
        <br />
        <input type="hidden" name="wv_translations_action" value="save">
        <input type="hidden" name="action" value="editpost">
        <input type="hidden" name="wv_translations_nonce" value="<?php echo wp_create_nonce('wv_translations_nonce'); ?>">
        <input type="hidden" name="submitted" id="submitted" value="true" />
        <input type="submit" name="submit_form" value="<?php esc_attr_e('Submit', PLUGIN_KEY); ?>" />
    </form>
</div>

<?php if (sizeof($results) > 0) : ?>
    <div class="translations-list">
        <table>
            <caption><?php esc_html_e('Your Translations', PLUGIN_KEY); ?></caption>
            <thead>
                <tr>
                    <th><?php esc_html_e('Date', PLUGIN_KEY); ?></th>
                    <th><?php esc_html_e('Title', PLUGIN_KEY); ?></th>
                    <th><?php esc_html_e('Transliteration', PLUGIN_KEY); ?></th>
                    <th><?php esc_html_e('Edit?', PLUGIN_KEY); ?></th>
                    <th><?php esc_html_e('Delete?', PLUGIN_KEY); ?></th>
                    <th><?php esc_html_e('Status', PLUGIN_KEY); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $result) : ?>
                    <tr>
                        <td><?= esc_html(date('M-d-Y', strtotime( $result->post_date))) ?></td>
                        <td><?= esc_html($result->post_title)?></td>
                        <td><?= $result->meta_value == 'Yes' ? esc_html__('Yes', PLUGIN_KEY) : esc_html__('No', PLUGIN_KEY) ?></td>
                        <?php $edit_post = add_query_arg('post', $result->ID,  home_url('/edit-translation')) ?>
                        <td><a href="<?= esc_url($edit_post) ?>"><?= esc_html_e('Edit', PLUGIN_KEY) ?></a></td>
                        <td><a onclick="return confirm('Are you sure you want to delete post: <?= $result->post_title ?> ?')" href="<?= get_delete_post_link($result->ID, '', true) ?>"> <?= esc_html_e('Delete', PLUGIN_KEY) ?></a></td>
                        <td><?= $result->meta_value == 'publish' ? esc_html__('Published', PLUGIN_KEY) : esc_html__('Pending', PLUGIN_KEY) ?></td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
<?php endif ?>

<script>
    if(window.history.replaceState){
        window.history.replaceState(null, null, window.location.href);
    }
</script>