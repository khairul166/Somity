<?php
/**
 * Template Name: Login Check
 */

// Redirect if user is not logged in
if (!is_user_logged_in()) {
    wp_redirect(home_url('/login/?login=failed'));
    exit;
}

// Get current user
 $current_user = wp_get_current_user();

// Check user status
 $member_status = get_user_meta($current_user->ID, '_member_status', true);

// If user is a member and status is pending
if (in_array('member', $current_user->roles) && $member_status === 'pending') {
    wp_redirect(home_url('/login/?account=pending'));
    exit;
}

// If user is a member and status is rejected
if (in_array('member', $current_user->roles) && $member_status === 'rejected') {
    wp_redirect(home_url('/login/?account=rejected'));
    exit;
}

// Redirect based on user role
if (in_array('administrator', $current_user->roles)) {
    wp_redirect(home_url('/admin-dashboard/'));
} else if (in_array('member', $current_user->roles)) {
    wp_redirect(home_url('/member-dashboard/'));
} else {
    wp_redirect(home_url());
}

exit;