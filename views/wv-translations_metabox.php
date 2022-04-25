<?php 
/**
 * if plugins not use tables native wordpress, get_post_meta dont resolve
 * $meta = get_post_meta($post->ID);
 * 
 * %d - int
 * %f - float
 * %s - string
 * %% - percent
 */

global $wpdb;

$query = $wpdb->prepare("select * from $wpdb->translationmeta where translation_id = %d", $post->ID);
$results = $wpdb->get_results($query, ARRAY_A);

?>
<input type="hidden" name="wv_translations_nonce" value="<?php echo wp_create_nonce("wv_translations_nonce") ?>">
<input type="hidden" name="wv_translations_action" value="<?php echo (empty($results[0]['meta_value']) || empty($results[1]['meta_value']) ? 'save' : 'update') ?>">

<table class="form-table wv-translations-metabox"> 
    <tr>
        <th>
            <label for="wv_translations_transliteration"><?php esc_html_e( 'Has transliteration?', PLUGIN_KEY ); ?></label>
        </th>
        <td>
            <select name="wv_translations_transliteration" id="wv_translations_transliteration">
                <option value="Yes" <?= isset($results[0]['meta_value']) ? selected($results[0]['meta_value'], 'Yes') : '' ?> ><?php esc_html_e( 'Yes', PLUGIN_KEY )?></option>';
                <option value="No"<?= isset($results[0]['meta_value']) ? selected($results[0]['meta_value'], 'No') : '' ?>><?php esc_html_e( 'No', PLUGIN_KEY )?></option>';
            </select>            
        </td>
    </tr>
    <tr>
        <th>
            <label for="wv_translations_video_url"><?php esc_html_e( 'Video URL', PLUGIN_KEY ); ?></label>
        </th>
        <td>
            <input 
                type="url" 
                name="wv_translations_video_url" 
                id="wv_translations_video_url" 
                class="regular-text video-url"
                value="<?php echo isset($results[1]['meta_value']) ? esc_url($results[1]['meta_value']) : '' ?>"
            >
        </td>
    </tr> 
</table>