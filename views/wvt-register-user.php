<?php if (!isset($user)) : ?>
    <h3><?php esc_html_e('Create your account', PLUGIN_KEY); ?></h3>
    <form action="" method="post" name="user_registeration">
        <label for="username"><?php esc_html_e('Username', PLUGIN_KEY); ?> *</label>
        <input type="text" name="username" required /><br />
        <label for="firstname"><?php esc_html_e('First Name', PLUGIN_KEY); ?> *</label>
        <input type="text" name="firstname" required /><br />
        <label for="lastname"><?php esc_html_e('Last Name', PLUGIN_KEY); ?> *</label>
        <input type="text" name="lastname" required /><br />
        <label for="useremail"><?php esc_html_e('Email address', PLUGIN_KEY); ?> *</label>
        <input type="text" name="useremail" required /> <br />
        <label for="password"><?php esc_html_e('Password', PLUGIN_KEY); ?> *</label>
        <input type="password" name="password" required /> <br />
        <input type="submit" name="user_registeration" value="<?php echo esc_attr__('Sign Up', PLUGIN_KEY); ?>" />

        <input type="hidden" name="mv_translations_register_nonce" value="<?php echo wp_create_nonce('wv_translations_register_nonce'); ?>">
        <input type="hidden" name="submitted" id="submitted" value="true" />
    </form>
    <h3><?php esc_html_e('Or login', PLUGIN_KEY); ?></h3>
    <?php wp_login_form(); ?>
<?php endif ?>