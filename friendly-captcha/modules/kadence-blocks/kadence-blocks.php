<?php

add_filter('the_content', function ($content) {
    if (is_admin() || ! is_singular()) {
        return $content;
    }

    if (strpos($content, 'wp-block-kadence-advanced-form') === false) {
        return $content;
    }

    $plugin = FriendlyCaptcha_Plugin::$instance;
    if (! $plugin->is_configured()) {
        return $content;
    }

    $widget = frcaptcha_generate_widget_tag_from_plugin($plugin);

    return str_replace('</form>', $widget . '</form>', $content);
});

add_filter('kadence_blocks_advanced_form_submission_reject', function ($reject, $form_args, $processed_fields, $post_id) {
    $plugin = FriendlyCaptcha_Plugin::$instance;

    if (!$plugin->is_configured()) {
        return $reject;
    }

    $solution = frcaptcha_get_sanitized_frcaptcha_solution_from_post();

    if (empty($solution)) {
        return true;
    }

    $verification = frcaptcha_verify_captcha_solution(
        $solution,
        $plugin->get_sitekey(),
        $plugin->get_api_key(),
        'kadence-blocks'
    );

    if (!$verification['success']) {
        return true;
    }

    return false;
}, 10, 4);

add_filter('kadence_blocks_advanced_form_submission_reject_message', function ($message, $form_args, $processed_fields, $post_id) {
    return FriendlyCaptcha_Plugin::default_error_user_message();
}, 10, 4);

add_action('wp_footer', function () {
    if (is_admin() || ! is_singular()) {
        return;
    }

    global $post;

    if (has_block('kadence/advanced-form', $post)) {
        frcaptcha_enqueue_widget_scripts();

        wp_enqueue_script(
            'frcaptcha_kadence-reset',
            plugin_dir_url(__FILE__) . 'script.js',
            array(),
            null,
            true
        );
    }
});
