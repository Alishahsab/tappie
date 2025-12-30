<?php
// WordPress environment (only needed if this file is standalone - remove if in functions.php)
require( dirname(__FILE__) . '/../../../../wp-load.php' );

add_action('init', 'tappie_form_submit_actions');
function tappie_form_submit_actions() {

    if (!is_user_logged_in()) {
        return;
    }

    $uid = get_current_user_id();

    /* =======================================
       1. UPDATE PROFILE
    ======================================= */
    if (isset($_POST['tappie-update-profile']) && $_POST['tappie-update-profile'] === 'update profile') {
        check_admin_referer('tappie_update_profile', 'tappie_profile_nonce');

        // Sanitize inputs
        $ufname       = sanitize_text_field($_POST['ufname'] ?? '');
        $ulname       = sanitize_text_field($_POST['ulname'] ?? '');
        $ubio         = sanitize_textarea_field($_POST['ubio'] ?? '');
        $uphone       = sanitize_text_field($_POST['uphone'] ?? '');
        $ext_location = sanitize_text_field($_POST['ext_location'] ?? '');

        // === 1. Update core user fields in wp_users table ===
        wp_update_user([
            'ID'          => $uid,
            'first_name'  => $ufname,
            'last_name'   => $ulname,
            'description' => $ubio,
        ]);

        // === 2. Update custom meta in wp_usermeta table ===
        update_user_meta($uid, 'ext_phone', $uphone);
        update_user_meta($uid, 'ext_location', $ext_location);

        // === 3. Handle social profiles ===
        $saved_profiles = get_user_meta($uid, 'saved_profiles', true) ?: [];
        $available_socials = tappie_get_available_profiles();

        $updated_profiles = [];

        foreach ($available_socials as $social) {
            $key = $social['key'];
            if (isset($_POST[$key]) && !empty($_POST[$key])) {
                $updated_profiles[$key] = [
                    'key'         => $key,
                    'title'       => $social['title'] ?? ucfirst($key),
                    'url'         => sanitize_text_field($_POST[$key]),
                    'baseurl'     => $social['baseurl'] ?? '',
                    'social_id'   => $_POST[$key . '_id'] ?? $social['id'] ?? '',
                    'icon'        => $social['icon'] ?? '',
                    'social_class'=> $social['social_class'] ?? '',
                ];
            }
        }

        // Preserve special keys like 'contacts' if they exist
        if (!empty($saved_profiles['contacts'])) {
            $updated_profiles['contacts'] = $saved_profiles['contacts'];
        }

        update_user_meta($uid, 'saved_profiles', $updated_profiles);

        /* =======================================
           4. UPDATE USERNAME BASED ON FIRST + LAST NAME
        ======================================= */
        if (!empty($ufname) || !empty($ulname)) {
            $new_slug = sanitize_title("$ufname $ulname"); // e.g., danis-new-lastname

            // Get current username
            $user = get_user_by('id', $uid);
            $current_username = $user->user_login;

            // Check if new slug is available and different
            if ($new_slug !== $current_username && !username_exists($new_slug)) {
                wp_update_user([
                    'ID' => $uid,
                    'user_login' => $new_slug
                ]);
            }
        }

        // Redirect with success flag
        wp_redirect(add_query_arg('profile-updated', 'success', wp_get_referer() ?: site_url('/dashboard')));
        exit;
    }

    /* =======================================
       5. UPDATE PASSWORD
    ======================================= */
    if (isset($_POST['tappie-update-password'])) {
        $new_password = $_POST['cpassword'] ?? '';

        if (!empty($new_password)) {
            $user = wp_get_current_user();
            wp_set_password($new_password, $user->ID);

            // Re-login
            wp_set_auth_cookie($user->ID);
            wp_set_current_user($user->ID);
            do_action('wp_login', $user->user_login, $user);
        }

        wp_safe_redirect(site_url('/settings'));
        exit;
    }

    /* =======================================
       6. ADD / UPDATE TAPPIE PASSCODE
    ======================================= */
    if (isset($_POST['tappie-add-passcode'])) {
        $cpasscode = sanitize_text_field($_POST['cpasscode'] ?? '');

        if (!empty($cpasscode)) {
            $old_passcode = get_user_meta($uid, 'passcodes', true);

            if ($old_passcode) {
                $tappie_tag_url = get_option($old_passcode);
                delete_option($old_passcode);
                update_option($cpasscode, $tappie_tag_url);
            }

            update_user_meta($uid, 'passcodes', $cpasscode);
        }

        wp_safe_redirect(site_url('/settings'));
        exit;
    }

    /* =======================================
       7. DELETE MY ACCOUNT
    ======================================= */
    if (isset($_POST['tappie-delete-account']) && $_POST['tappie-delete-account'] === 'delete') {
        require_once(ABSPATH . 'wp-admin/includes/user.php');

        $current_user = wp_get_current_user();
        $user_tapcode = get_user_meta($current_user->ID, 'passcodes', true);

        if ($user_tapcode) {
            delete_option($user_tapcode);
        }

        wp_delete_user($current_user->ID);
        wp_safe_redirect(site_url());
        exit;
    }
}