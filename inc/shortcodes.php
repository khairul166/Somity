<?php
/**
 * Shortcodes for Somity Manager
 */

// Member Dashboard shortcode
function somity_member_dashboard_shortcode() {
    if (!is_user_logged_in()) {
        return '<p>' . __('Please log in to view your dashboard.', 'somity-manager') . '</p>';
    }
    
    ob_start();
    get_template_part('page-templates/member-dashboard');
    return ob_get_clean();
}
add_shortcode('somity_member_dashboard', 'somity_member_dashboard_shortcode');

// Submit Payment shortcode
function somity_submit_payment_shortcode() {
    if (!is_user_logged_in()) {
        return '<p>' . __('Please log in to submit a payment.', 'somity-manager') . '</p>';
    }
    
    ob_start();
    get_template_part('page-templates/submit-payment');
    return ob_get_clean();
}
add_shortcode('somity_submit_payment', 'somity_submit_payment_shortcode');

// Admin Dashboard shortcode
function somity_admin_dashboard_shortcode() {
    if (!is_user_logged_in() || !current_user_can('administrator')) {
        return '<p>' . __('You do not have permission to view this page.', 'somity-manager') . '</p>';
    }
    
    ob_start();
    get_template_part('page-templates/admin-dashboard');
    return ob_get_clean();
}
add_shortcode('somity_admin_dashboard', 'somity_admin_dashboard_shortcode');

// Contact Form shortcode
function somity_contact_form_shortcode() {
    ob_start();
    get_template_part('page-templates/contact-page');
    return ob_get_clean();
}
add_shortcode('somity_contact_form', 'somity_contact_form_shortcode');

// Stats Counter shortcode
function somity_stats_counter_shortcode($atts) {
    $atts = shortcode_atts(array(
        'type' => 'members',
    ), $atts, 'somity_stats_counter');
    
    $count = 0;
    
    switch ($atts['type']) {
        case 'members':
            $count = somity_get_total_members();
            break;
        case 'savings':
            $count = somity_get_total_savings();
            break;
        case 'installments':
            $count = somity_get_total_installments();
            break;
        default:
            $count = somity_get_total_members();
    }
    
    return '<span class="stat-counter" data-count="' . esc_attr($count) . '">0</span>';
}
add_shortcode('somity_stats_counter', 'somity_stats_counter_shortcode');