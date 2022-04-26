<?php

function debug($var, $die = true)
{
    echo '<pre>';
    var_dump($var);
    echo '</pre>';

    if ($die)
        exit;
}

function wvt_resgister_user()
{
    if (isset($_POST['submitted'])) {
        if (isset($_POST['wv_translations_register_nonce'])) {
            if (!wp_verify_nonce($_POST['wv_translations_register_nonce'], 'wv_translations_register_nonce')) {
                return;
            }
        }

        global $reg_errors;
        $reg_errors = new WP_Error();

        $username = sanitize_user($_POST['username']);
        $firstname = sanitize_text_field($_POST['firstname']);
        $lastname = sanitize_text_field($_POST['lastname']);
        $useremail = sanitize_email($_POST['useremail']);
        $password = $_POST['password'];

        if (empty($username) || empty($firstname) || empty($lastname) || empty($useremail) || empty($password))
            $reg_errors->add('empty-field', esc_html__('Required form field is missing', PLUGIN_KEY));

        if (strlen($username) < 6)
            $reg_errors->add('username_length', esc_html__('User name too short, At least 6 characters is required', PLUGIN_KEY));

        if (username_exists($username))
            $reg_errors->add('user_name', esc_html__('Invalid credentials', PLUGIN_KEY));

        if (!validate_username($username))
            $reg_errors->add('username_invalid', esc_html__('The username you entered is not valid', PLUGIN_KEY));

        if (!is_email($useremail))
            $reg_errors->add('email_invalid', esc_html__('The email you entered is not valid', PLUGIN_KEY));

        if (email_exists($useremail))
            $reg_errors->add('email_exists', esc_html__('Email already exists', PLUGIN_KEY));

        if (strlen($password) < 6)
            $reg_errors->add('password_length', esc_html__('Password length must be greater than 6', PLUGIN_KEY));

        if (is_wp_error($reg_errors)) {
            foreach ($reg_errors->get_error_messages() as $error)
                echo "<div style='color:#ff0000; text-align:left'> $error </div>";
        }

        if (count($reg_errors->get_error_messages()) < 1) {
            $user_data = array(
                'user_login'    => $username,
                'first_name'    => $firstname,
                'last_name' => $lastname,
                'user_email'    => $useremail,
                'user_pass' => $password,
                'role'  => 'contributor'
            );
            $user = wp_insert_user($user_data);

            wp_login_form();
        }
    }
    require(WV_TRANSLATIONS_PATH . 'views/wvt-register-user.php');
}
