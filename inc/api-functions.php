<?php
/**
 * API Functions for Somity Manager
 */

// Get total members count
function somity_get_total_members() {
    $users = get_users(array('role' => 'member'));
    return count($users);
}

// Get total savings amount
function somity_get_total_savings() {
    $args = array(
        'post_type' => 'payment',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'tax_query' => array(
            array(
                'taxonomy' => 'payment_status',
                'field' => 'slug',
                'terms' => 'approved',
            ),
        ),
    );
    
    $payments = get_posts($args);
    $total = 0;
    
    foreach ($payments as $payment) {
        $amount = get_post_meta($payment->ID, '_amount', true);
        $total += floatval($amount);
    }
    
    return $total;
}

// Get total installments count
function somity_get_total_installments() {
    $args = array(
        'post_type' => 'installment',
        'post_status' => 'publish',
        'posts_per_page' => -1,
    );
    
    $installments = get_posts($args);
    return count($installments);
}

// Get member balance
function somity_get_member_balance($member_id) {
    $args = array(
        'post_type' => 'payment',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'author' => $member_id,
        'tax_query' => array(
            array(
                'taxonomy' => 'payment_status',
                'field' => 'slug',
                'terms' => 'approved',
            ),
        ),
    );
    
    $payments = get_posts($args);
    $total = 0;
    
    foreach ($payments as $payment) {
        $amount = get_post_meta($payment->ID, '_amount', true);
        $total += floatval($amount);
    }
    
    return $total;
}



// Get member pending payments count
function somity_get_member_pending_payments($member_id) {
    $args = array(
        'post_type' => 'installment',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => '_member_id',
                'value' => $member_id,
            ),
        ),
        'tax_query' => array(
            array(
                'taxonomy' => 'installment_status',
                'field' => 'slug',
                'terms' => 'pending',
            ),
        ),
    );
    
    $installments = get_posts($args);
    return count($installments);
}

// Get member payments
function somity_get_member_payments($member_id, $limit = 10) {
    $args = array(
        'post_type' => 'payment',
        'post_status' => 'publish',
        'posts_per_page' => $limit,
        'author' => $member_id,
        'orderby' => 'date',
        'order' => 'DESC',
    );
    
    $payments = get_posts($args);
    $result = array();
    
    foreach ($payments as $payment) {
        $status_terms = wp_get_post_terms($payment->ID, 'payment_status');
        $status = !empty($status_terms) ? $status_terms[0]->slug : 'unknown';
        
        $result[] = (object) array(
            'id' => $payment->ID,
            'amount' => get_post_meta($payment->ID, '_amount', true),
            'transaction_id' => get_post_meta($payment->ID, '_transaction_id', true),
            'date' => $payment->post_date,
            'status' => $status,
        );
    }
    
    return $result;
}

// Get member upcoming installments
function somity_get_member_upcoming_installments($member_id, $limit = 5) {
    $args = array(
        'post_type' => 'installment',
        'post_status' => 'publish',
        'posts_per_page' => $limit,
        'meta_query' => array(
            'relation' => 'AND',
            array(
                'key' => '_member_id',
                'value' => $member_id,
            ),
            array(
                'key' => '_due_date',
                'value' => date('Y-m-d'),
                'compare' => '>=',
            ),
        ),
        'orderby' => 'meta_value',
        'meta_key' => '_due_date',
        'order' => 'ASC',
    );
    
    $installments = get_posts($args);
    $result = array();
    
    foreach ($installments as $installment) {
        $result[] = (object) array(
            'id' => $installment->ID,
            'amount' => get_post_meta($installment->ID, '_amount', true),
            'due_date' => get_post_meta($installment->ID, '_due_date', true),
        );
    }
    
    return $result;
}

// Get installment by ID
function somity_get_installment($installment_id) {
    $installment = get_post($installment_id);
    
    if (!$installment || $installment->post_type !== 'installment') {
        return false;
    }
    
    return (object) array(
        'id' => $installment->ID,
        'amount' => get_post_meta($installment->ID, '_amount', true),
        'due_date' => get_post_meta($installment->ID, '_due_date', true),
        'member_id' => get_post_meta($installment->ID, '_member_id', true),
    );
}

// Get pending payments count
function somity_get_pending_payments_count() {
    $args = array(
        'post_type' => 'payment',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'tax_query' => array(
            array(
                'taxonomy' => 'payment_status',
                'field' => 'slug',
                'terms' => 'pending',
            ),
        ),
    );
    
    $payments = get_posts($args);
    return count($payments);
}

// Get approved payments count
function somity_get_approved_payments_count() {
    $args = array(
        'post_type' => 'payment',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'tax_query' => array(
            array(
                'taxonomy' => 'payment_status',
                'field' => 'slug',
                'terms' => 'approved',
            ),
        ),
    );
    
    $payments = get_posts($args);
    return count($payments);
}

// Get rejected payments count
function somity_get_rejected_payments_count() {
    $args = array(
        'post_type' => 'payment',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'tax_query' => array(
            array(
                'taxonomy' => 'payment_status',
                'field' => 'slug',
                'terms' => 'rejected',
            ),
        ),
    );
    
    $payments = get_posts($args);
    return count($payments);
}

// Get recent payments
function somity_get_recent_payments($limit = 10) {
    $args = array(
        'post_type' => 'payment',
        'post_status' => 'publish',
        'posts_per_page' => $limit,
        'orderby' => 'date',
        'order' => 'DESC',
    );
    
    $payments = get_posts($args);
    $result = array();
    
    foreach ($payments as $payment) {
        $status_terms = wp_get_post_terms($payment->ID, 'payment_status');
        $status = !empty($status_terms) ? $status_terms[0]->slug : 'unknown';
        
        $result[] = (object) array(
            'id' => $payment->ID,
            'member_id' => $payment->post_author,
            'amount' => get_post_meta($payment->ID, '_amount', true),
            'transaction_id' => get_post_meta($payment->ID, '_transaction_id', true),
            'date' => $payment->post_date,
            'status' => $status,
        );
    }
    
    return $result;
}

// Get recent activity
function somity_get_recent_activity($limit = 10) {
    $args = array(
        'post_type' => 'activity',
        'post_status' => 'publish',
        'posts_per_page' => $limit,
        'orderby' => 'date',
        'order' => 'DESC',
    );
    
    $activities = get_posts($args);
    $result = array();
    
    foreach ($activities as $activity) {
        $type_terms = wp_get_post_terms($activity->ID, 'activity_type');
        $type = !empty($type_terms) ? $type_terms[0]->slug : 'unknown';
        
        $result[] = (object) array(
            'id' => $activity->ID,
            'description' => $activity->post_content,
            'date' => $activity->post_date,
            'type' => $type,
        );
    }
    
    return $result;
}

// Format time elapsed string
function somity_time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);
    
    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;
    
    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }
    
    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}

// AJAX handlers for payment submission
add_action('wp_ajax_submit_payment', 'somity_ajax_submit_payment');
add_action('wp_ajax_nopriv_submit_payment', 'somity_ajax_submit_payment');

function somity_ajax_submit_payment() {
    // Enable error logging for debugging
    error_log('Payment submission started');
    error_log('POST data: ' . print_r($_POST, true));
    error_log('FILES data: ' . print_r($_FILES, true));
    
    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'somity-nonce')) {
        error_log('Nonce verification failed');
        wp_send_json_error(array('message' => __('Security check failed.', 'somity-manager')));
    }
    
    // Check if user is logged in
    if (!is_user_logged_in()) {
        error_log('User not logged in');
        wp_send_json_error(array('message' => __('You must be logged in to submit a payment.', 'somity-manager')));
    }
    
    // Sanitize and validate input
    $amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;
    $transaction_id = isset($_POST['transaction_id']) ? sanitize_text_field($_POST['transaction_id']) : '';
    $payment_date = isset($_POST['payment_date']) ? sanitize_text_field($_POST['payment_date']) : '';
    $payment_method = isset($_POST['payment_method']) ? sanitize_text_field($_POST['payment_method']) : '';
    $payment_note = isset($_POST['payment_note']) ? sanitize_textarea_field($_POST['payment_note']) : '';
    $installment_id = isset($_POST['installment_id']) ? intval($_POST['installment_id']) : 0;
    
    error_log('Sanitized data: amount=' . $amount . ', transaction_id=' . $transaction_id . ', payment_date=' . $payment_date);
    
    // Validate required fields
    if (empty($amount) || empty($transaction_id) || empty($payment_date) || empty($payment_method)) {
        error_log('Validation failed: missing required fields');
        wp_send_json_error(array('message' => __('Please fill in all required fields.', 'somity-manager')));
    }
    
    // Create payment post
    $payment_data = array(
        'post_title' => 'Payment by ' . wp_get_current_user()->display_name,
        'post_content' => $payment_note,
        'post_status' => 'publish',
        'post_author' => get_current_user_id(),
        'post_type' => 'payment',
    );
    
    $payment_id = wp_insert_post($payment_data);
    
    if (is_wp_error($payment_id)) {
        error_log('Error creating payment post: ' . $payment_id->get_error_message());
        wp_send_json_error(array('message' => __('Error creating payment record.', 'somity-manager')));
    }
    
    error_log('Payment post created with ID: ' . $payment_id);
    
    // Save payment meta
    update_post_meta($payment_id, '_amount', $amount);
    update_post_meta($payment_id, '_transaction_id', $transaction_id);
    update_post_meta($payment_id, '_payment_date', $payment_date);
    update_post_meta($payment_id, '_payment_method', $payment_method);
    update_post_meta($payment_id, '_payment_note', $payment_note);
    
    // Set payment status to pending
    wp_set_post_terms($payment_id, 'pending', 'payment_status');
    
    // Handle file upload
    if (!empty($_FILES['payment_screenshot']['name'])) {
        error_log('File upload detected: ' . $_FILES['payment_screenshot']['name']);
        
        // Check if file is uploaded correctly
        if ($_FILES['payment_screenshot']['error'] !== UPLOAD_ERR_OK) {
            error_log('File upload error code: ' . $_FILES['payment_screenshot']['error']);
            
            // Get error message
            switch ($_FILES['payment_screenshot']['error']) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    $error_message = __('The uploaded file exceeds the maximum allowed size.', 'somity-manager');
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $error_message = __('The file was only partially uploaded.', 'somity-manager');
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $error_message = __('No file was uploaded.', 'somity-manager');
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    $error_message = __('Missing a temporary folder.', 'somity-manager');
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    $error_message = __('Failed to write file to disk.', 'somity-manager');
                    break;
                case UPLOAD_ERR_EXTENSION:
                    $error_message = __('A PHP extension stopped the file upload.', 'somity-manager');
                    break;
                default:
                    $error_message = __('Unknown upload error.', 'somity-manager');
                    break;
            }
            
            error_log('File upload error: ' . $error_message);
        } else {
            // Make sure the WordPress file handling functions are available
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/media.php');
            
            // Let WordPress handle the file upload
            $attachment_id = media_handle_upload('payment_screenshot', $payment_id);
            
            // Check for errors in file upload
            if (is_wp_error($attachment_id)) {
                // Log the error but don't fail the entire submission
                error_log('File upload error: ' . $attachment_id->get_error_message());
            } else {
                // If upload was successful, set as featured image
                set_post_thumbnail($payment_id, $attachment_id);
                error_log('File upload successful. Attachment ID: ' . $attachment_id);
            }
        }
    } else {
        error_log('No file uploaded');
    }
    
    // Update installment status if applicable
    if ($installment_id) {
        $installment = get_post($installment_id);
        if ($installment && $installment->post_type === 'installment') {
            wp_set_post_terms($installment_id, 'paid', 'installment_status');
            error_log('Installment status updated to paid');
        }
    }
    
    // Create activity record
    $activity_data = array(
        'post_title' => 'Payment Submitted',
        'post_content' => wp_get_current_user()->display_name . ' submitted a payment of ' . $amount,
        'post_status' => 'publish',
        'post_author' => get_current_user_id(),
        'post_type' => 'activity',
    );
    
    $activity_id = wp_insert_post($activity_data);
    
    if (!is_wp_error($activity_id)) {
        wp_set_post_terms($activity_id, 'payment', 'activity_type');
        error_log('Activity record created with ID: ' . $activity_id);
    }
    
    error_log('Payment submission completed successfully');
    
    wp_send_json_success(array(
        'message' => __('Your payment has been submitted successfully and is pending approval.', 'somity-manager'),
        'redirect' => home_url('/member-dashboard/'),
    ));
}

add_action('wp_ajax_approve_payment', 'somity_ajax_approve_payment');
function somity_ajax_approve_payment() {
    check_ajax_referer('somity-nonce', 'nonce');
    
    if (!current_user_can('administrator')) {
        wp_send_json_error(array('message' => __('You do not have permission to approve payments.', 'somity-manager')));
    }
    
    $payment_id = intval($_POST['payment_id']);
    $payment = get_post($payment_id);
    
    if (!$payment || $payment->post_type !== 'payment') {
        wp_send_json_error(array('message' => __('Invalid payment ID.', 'somity-manager')));
    }
    
    // Update payment status
    wp_set_post_terms($payment_id, 'approved', 'payment_status');
    
    // Get payment details
    $amount = get_post_meta($payment_id, '_amount', true);
    $member_id = $payment->post_author;
    $member = get_user_by('id', $member_id);
    
    // Create activity record
    $activity_data = array(
        'post_title' => 'Payment Approved',
        'post_content' => 'Payment of ' . $amount . ' by ' . $member->display_name . ' was approved',
        'post_status' => 'publish',
        'post_author' => get_current_user_id(),
        'post_type' => 'activity',
    );
    
    $activity_id = wp_insert_post($activity_data);
    
    if (!is_wp_error($activity_id)) {
        wp_set_post_terms($activity_id, 'payment', 'activity_type');
    }
    
    wp_send_json_success(array('message' => __('Payment has been approved successfully.', 'somity-manager')));
}

add_action('wp_ajax_reject_payment', 'somity_ajax_reject_payment');
function somity_ajax_reject_payment() {
    check_ajax_referer('somity-nonce', 'nonce');
    
    if (!current_user_can('administrator')) {
        wp_send_json_error(array('message' => __('You do not have permission to reject payments.', 'somity-manager')));
    }
    
    $payment_id = intval($_POST['payment_id']);
    $reason = sanitize_textarea_field($_POST['reason']);
    $payment = get_post($payment_id);
    
    if (!$payment || $payment->post_type !== 'payment') {
        wp_send_json_error(array('message' => __('Invalid payment ID.', 'somity-manager')));
    }
    
    // Update payment status
    wp_set_post_terms($payment_id, 'rejected', 'payment_status');
    
    // Save rejection reason
    update_post_meta($payment_id, '_rejection_reason', $reason);
    
    // Get payment details
    $amount = get_post_meta($payment_id, '_amount', true);
    $member_id = $payment->post_author;
    $member = get_user_by('id', $member_id);
    
    // Create activity record
    $activity_data = array(
        'post_title' => 'Payment Rejected',
        'post_content' => 'Payment of ' . $amount . ' by ' . $member->display_name . ' was rejected. Reason: ' . $reason,
        'post_status' => 'publish',
        'post_author' => get_current_user_id(),
        'post_type' => 'activity',
    );
    
    $activity_id = wp_insert_post($activity_data);
    
    if (!is_wp_error($activity_id)) {
        wp_set_post_terms($activity_id, 'payment', 'activity_type');
    }
    
    wp_send_json_success(array('message' => __('Payment has been rejected successfully.', 'somity-manager')));
}

add_action('wp_ajax_submit_contact_form', 'somity_ajax_submit_contact_form');
add_action('wp_ajax_nopriv_submit_contact_form', 'somity_ajax_submit_contact_form');
function somity_ajax_submit_contact_form() {
    check_ajax_referer('contact_nonce', 'nonce');
    
    $name = sanitize_text_field($_POST['name']);
    $email = sanitize_email($_POST['email']);
    $phone = sanitize_text_field($_POST['phone']);
    $subject = sanitize_text_field($_POST['subject']);
    $message = sanitize_textarea_field($_POST['message']);
    
    // Send email
    $to = get_option('admin_email');
    $headers = array('Content-Type: text/html; charset=UTF-8');
    $subject = 'Contact Form: ' . $subject;
    
    $body = '<h2>Contact Form Submission</h2>';
    $body .= '<p><strong>Name:</strong> ' . $name . '</p>';
    $body .= '<p><strong>Email:</strong> ' . $email . '</p>';
    $body .= '<p><strong>Phone:</strong> ' . $phone . '</p>';
    $body .= '<p><strong>Subject:</strong> ' . $subject . '</p>';
    $body .= '<p><strong>Message:</strong><br>' . nl2br($message) . '</p>';
    
    $sent = wp_mail($to, $subject, $body, $headers);
    
    if ($sent) {
        wp_send_json_success(array('message' => __('Your message has been sent successfully. We will get back to you soon.', 'somity-manager')));
    } else {
        wp_send_json_error(array('message' => __('Error sending message. Please try again.', 'somity-manager')));
    }
}

// AJAX handler for exporting payments
add_action('wp_ajax_export_payments', 'somity_ajax_export_payments');

function somity_ajax_export_payments() {
    check_ajax_referer('somity-nonce', 'nonce');
    
    if (!current_user_can('administrator')) {
        wp_die(__('You do not have permission to export payments.', 'somity-manager'));
    }
    
    $filterValue = isset($_GET['filter']) ? sanitize_text_field($_GET['filter']) : 'all';
    $searchTerm = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';
    $monthFilter = isset($_GET['month']) ? sanitize_text_field($_GET['month']) : 'all';
    
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="payments_export.csv"');
    
    $output = fopen('php://output', 'w');
    
    // Add CSV headers
    fputcsv($output, array(
        'ID',
        'Member',
        'Amount',
        'Transaction ID',
        'Payment Date',
        'Payment Method',
        'Status',
        'Note',
    ));
    
    // Build query arguments
    $args = array(
        'post_type' => 'payment',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'orderby' => 'date',
        'order' => 'DESC',
    );
    
    // Handle search filter
    if (!empty($searchTerm)) {
        $args['s'] = $searchTerm;
    }
    
    // Handle status filter
    if ($filterValue !== 'all') {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'payment_status',
                'field' => 'slug',
                'terms' => $filterValue,
            ),
        );
    }
    
    // Handle month filter
    if ($monthFilter !== 'all') {
        $month = intval($monthFilter);
        $year = date('Y');
        
        $args['meta_query'] = array(
            array(
                'key' => '_payment_date',
                'value' => $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '%',
                'compare' => 'LIKE',
            ),
        );
    }
    
    $payments_query = new WP_Query($args);
    
    // Add payment data
    if ($payments_query->have_posts()) {
        while ($payments_query->have_posts()) {
            $payments_query->the_post();
            $payment_id = get_the_ID();
            $member = get_user_by('id', get_the_author_meta('ID'));
            $status_terms = wp_get_post_terms($payment_id, 'payment_status');
            $status = !empty($status_terms) ? $status_terms[0]->name : 'Unknown';
            
            fputcsv($output, array(
                $payment_id,
                $member->display_name,
                get_post_meta($payment_id, '_amount', true),
                get_post_meta($payment_id, '_transaction_id', true),
                get_post_meta($payment_id, '_payment_date', true),
                get_post_meta($payment_id, '_payment_method', true),
                $status,
                get_post_meta($payment_id, '_payment_note', true),
            ));
        }
    }
    
    wp_reset_postdata();
    
    fclose($output);
    exit;
}


/**
 * Get member's monthly installment amount
 */
function somity_get_member_monthly_installment($member_id) {
    // Get the most recent installment for the member
    $args = array(
        'post_type' => 'installment',
        'post_status' => 'publish',
        'posts_per_page' => 1,
        'meta_query' => array(
            array(
                'key' => '_member_id',
                'value' => $member_id,
            ),
        ),
        'orderby' => 'meta_value',
        'meta_key' => '_due_date',
        'order' => 'DESC',
    );
    
    $installments = get_posts($args);
    
    if ($installments) {
        return floatval(get_post_meta($installments[0]->ID, '_amount', true));
    }
    
    // Default monthly installment if no installments found
    return 300.00;
}

/**
 * Get member's outstanding balance
 */
function somity_get_member_outstanding_balance($member_id) {
    // Get all installments for the member
    $args = array(
        'post_type' => 'installment',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => '_member_id',
                'value' => $member_id,
            ),
        ),
        'tax_query' => array(
            array(
                'taxonomy' => 'installment_status',
                'field' => 'slug',
                'terms' => 'pending',
            ),
        ),
    );
    
    $installments = get_posts($args);
    $outstanding_balance = 0;
    
    foreach ($installments as $installment) {
        $outstanding_balance += floatval(get_post_meta($installment->ID, '_amount', true));
    }
    
    return $outstanding_balance;
}

/**
 * Get member's total paid amount
 */
function somity_get_member_total_paid($member_id) {
    // Get all approved payments for the member
    $args = array(
        'post_type' => 'payment',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'author' => $member_id,
        'tax_query' => array(
            array(
                'taxonomy' => 'payment_status',
                'field' => 'slug',
                'terms' => 'approved',
            ),
        ),
    );
    
    $payments = get_posts($args);
    $total_paid = 0;
    
    foreach ($payments as $payment) {
        $total_paid += floatval(get_post_meta($payment->ID, '_amount', true));
    }
    
    return $total_paid;
}



/**
 * Get recent payments with pagination and filters
 */
function somity_get_recent_payments_paginated($per_page = 10, $page = 1, $status = 'all', $search = '', $month = 'all') {
    $offset = ($page - 1) * $per_page;
    
    $args = array(
        'post_type' => 'payment',
        'post_status' => 'publish',
        'posts_per_page' => $per_page,
        'offset' => $offset,
        'orderby' => 'date',
        'order' => 'DESC',
    );
    
    // Handle search filter
    if (!empty($search)) {
        $args['s'] = $search;
    }
    
    // Handle status filter
    if ($status !== 'all') {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'payment_status',
                'field' => 'slug',
                'terms' => $status,
            ),
        );
    }
    
    // Handle month filter
    if ($month !== 'all') {
        $month = intval($month);
        $year = date('Y');
        
        $args['meta_query'] = array(
            array(
                'key' => '_payment_date',
                'value' => $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '%',
                'compare' => 'LIKE',
            ),
        );
    }
    
    $payments_query = new WP_Query($args);
    
    $payments = array();
    
    if ($payments_query->have_posts()) {
        while ($payments_query->have_posts()) {
            $payments_query->the_post();
            $payment_id = get_the_ID();
            $status_terms = wp_get_post_terms($payment_id, 'payment_status');
            $payment_status = !empty($status_terms) ? $status_terms[0]->slug : 'unknown';
            
            $payments[] = (object) array(
                'id' => $payment_id,
                'member_id' => get_the_author_meta('ID'),
                'amount' => get_post_meta($payment_id, '_amount', true),
                'transaction_id' => get_post_meta($payment_id, '_transaction_id', true),
                'date' => get_post_meta($payment_id, '_payment_date', true),
                'status' => $payment_status,
            );
        }
    }
    
    wp_reset_postdata();
    
    return array(
        'items' => $payments,
        'total' => $payments_query->found_posts,
        'pages' => $payments_query->max_num_pages,
        'current_page' => $page
    );
}


/**
 * Get total payments count
 */
function somity_get_total_payments_count() {
    $args = array(
        'post_type' => 'payment',
        'post_status' => 'publish',
        'posts_per_page' => -1,
    );
    
    $payments_query = new WP_Query($args);
    
    return $payments_query->found_posts;
}



// /**
//  * Get all members with pagination
//  */
// function somity_get_members_paginated($per_page = 10, $page = 1, $status = 'all', $search = '') {
//     $offset = ($page - 1) * $per_page;
    
//     $args = array(
//         'role' => 'member',
//         'number' => $per_page,
//         'offset' => $offset,
//         'orderby' => 'registered',
//         'order' => 'DESC',
//     );
    
//     // Handle search filter
//     if (!empty($search)) {
//         $args['search'] = '*' . $search . '*';
//     }
    
//     // Handle status filter
//     if ($status !== 'all') {
//         $args['meta_key'] = '_member_status';
//         $args['meta_value'] = $status;
//     }
    
//     $members_query = new WP_User_Query($args);
    
//     $members = array();
    
//     if (!empty($members_query->get_results())) {
//         foreach ($members_query->get_results() as $member) {
//             $member_status = get_user_meta($member->ID, '_member_status', true);
//             if (empty($member_status)) {
//                 $member_status = 'pending';
//             }
            
//             $members[] = (object) array(
//                 'id' => $member->ID,
//                 'name' => $member->display_name,
//                 'email' => $member->user_email,
//                 'phone' => get_user_meta($member->ID, '_phone', true),
//                 'address' => get_user_meta($member->ID, '_address', true),
//                 'join_date' => $member->user_registered,
//                 'status' => $member_status,
//             );
//         }
//     }
    
//     return array(
//         'items' => $members,
//         'total' => $members_query->get_total(),
//         'pages' => ceil($members_query->get_total() / $per_page),
//         'current_page' => $page
//     );
// }

/**
 * Get member details by ID
 */
function somity_get_member_details($member_id) {
    $member = get_user_by('id', $member_id);
    
    if (!$member || !in_array('subscriber', $member->roles)) {
        error_log('Member not found or does not have member role for ID ' . $member_id);
        return false;
    }
    
    $member_status = get_user_meta($member->ID, '_member_status', true);
    if (empty($member_status)) {
        $member_status = 'pending';
    }
    
    return (object) array(
        'id' => $member->ID,
        'first_name' => $member->first_name,
        'last_name' => $member->last_name,
        'display_name' => $member->display_name,
        'email' => $member->user_email,
        'phone' => get_user_meta($member->ID, '_phone', true),
        'address' => get_user_meta($member->ID, '_address', true),
        'join_date' => $member->user_registered,
        'status' => $member_status,
        'balance' => somity_get_member_balance($member->ID),
        'monthly_installment' => somity_get_member_monthly_installment($member->ID),
        'outstanding_balance' => somity_get_member_outstanding_balance($member->ID),
        'total_paid' => somity_get_member_total_paid($member->ID),
    );
}

/**
 * Approve member
 */
function somity_approve_member($member_id) {
    error_log('Starting member approval for ID: ' . $member_id);
    
    $member = get_user_by('id', $member_id);
    
    if (!$member) {
        error_log('Member not found for ID: ' . $member_id);
        return false;
    }
    $member = new WP_User( $member_id );

    // Remove all current roles (optional, if you want to replace the role)
    $member->set_role( 'subscriber' ); // Replace 'subscriber' with the role you want
        
    error_log('Member found: ' . $member->display_name);
    if ( ! $member instanceof WP_User ) {
        error_log('Member is not a valid WP_User object');
        return false;
    }

    error_log('Member roles: ' . print_r($member->roles, true));

    if ( ! in_array( 'subscriber', (array) $member->roles ) ) {
        error_log('User does not have member role');
        return false;
    }

    // Update member status
    error_log('Updating member status to approved');
    $result = update_user_meta($member_id, '_member_status', 'approved');
    
    if (!$result) {
        error_log('Failed to update user meta');
        return false;
    }
    
    error_log('Member status updated successfully');
    
    // Send notification email to member
    error_log('Sending notification email');
    $subject = __('Your Account Has Been Approved', 'somity-manager');
    $message = sprintf(
        __('Hello %s,%sYour account on %s has been approved by the administrator.%sYou can now log in and participate in our savings program.%s%sThank you,%sThe %s Team', 'somity-manager'),
        $member->display_name,
        "\n\n",
        get_bloginfo('name'),
        "\n\n",
        "\n",
        home_url('/login/'),
        "\n\n",
        "\n",
        get_bloginfo('name')
    );
    
    wp_mail($member->user_email, $subject, $message);
    error_log('Notification email sent');
    
    return true;
}

/**
 * Reject member
 */
function somity_reject_member($member_id, $reason = '') {
    $member = get_user_by('id', $member_id);
    
    if (!$member) {
        error_log('Member rejection failed: User not found for ID ' . $member_id);
        return false;
    }
    
    // Check if user has the member role
    if (!in_array('member', (array) $member->roles)) {
        error_log('Member rejection failed: User does not have member role for ID ' . $member_id);
        return false;
    }
    
    // Update member status
    $result = update_user_meta($member_id, '_member_status', 'rejected');
    
    if (!$result) {
        error_log('Member rejection failed: Could not update user meta for ID ' . $member_id);
        return false;
    }
    
    // Save rejection reason
    update_user_meta($member_id, '_rejection_reason', $reason);
    
    // Create activity record only if activity post type exists
    if (post_type_exists('activity')) {
        $activity_data = array(
            'post_title' => 'Member Rejected',
            'post_content' => 'Member ' . $member->display_name . ' was rejected. Reason: ' . $reason,
            'post_status' => 'publish',
            'post_author' => get_current_user_id(),
            'post_type' => 'activity',
        );
        
        $activity_id = wp_insert_post($activity_data);
        
        if (!is_wp_error($activity_id) && taxonomy_exists('activity_type')) {
            wp_set_post_terms($activity_id, 'member', 'activity_type');
        }
    }
    
    // Send notification email to member
    $subject = __('Your Account Has Been Rejected', 'somity-manager');
    $message = sprintf(
        __('Hello %s,%sYour account on %s has been rejected by the administrator.%sReason: %s%sIf you believe this is a mistake, please contact the administrator.%s%sThank you,%sThe %s Team', 'somity-manager'),
        $member->display_name,
        "\n\n",
        get_bloginfo('name'),
        "\n\n",
        $reason,
        "\n\n",
        "\n",
        home_url('/contact/'),
        "\n\n",
        "\n",
        get_bloginfo('name')
    );
    
    wp_mail($member->user_email, $subject, $message);
    
    return true;
}

add_action('wp_ajax_approve_member', 'somity_ajax_approve_member');
function somity_ajax_approve_member() {
    // Check nonce
    if (!check_ajax_referer('somity-nonce', 'nonce', false)) {
        error_log('Nonce check failed');
        wp_send_json_error(array('message' => __('Security check failed.', 'somity-manager')));
    }
    
    // Check user capabilities
    if (!current_user_can('administrator')) {
        error_log('User does not have administrator capabilities');
        wp_send_json_error(array('message' => __('You do not have permission to approve members.', 'somity-manager')));
    }
    
    // Get and validate member ID
    $member_id = isset($_POST['member_id']) ? intval($_POST['member_id']) : 0;
    
    if (!$member_id) {
        error_log('Invalid member ID: ' . $member_id);
        wp_send_json_error(array('message' => __('Invalid member ID.', 'somity-manager')));
    }
    
    // Approve the member
    error_log('Attempting to approve member with ID: ' . $member_id);
    $result = somity_approve_member($member_id);
    error_log('Approval result: ' . ($result ? 'success' : 'failure'));
    wp_send_json_success(array('message' => __('Member has been approved successfully.', 'somity-manager')));
    if ($result) {
        wp_send_json_success(array('message' => __('Member has been approved successfully.', 'somity-manager')));
    } else {
        wp_send_json_error(array('message' => __('Error approving member.', 'somity-manager')));
    }
}

add_action('wp_ajax_reject_member', 'somity_ajax_reject_member');
function somity_ajax_reject_member() {
    // Check nonce
    if (!check_ajax_referer('somity-nonce', 'nonce', false)) {
        wp_send_json_error(array('message' => __('Security check failed.', 'somity-manager')));
    }
    
    // Check user capabilities
    if (!current_user_can('administrator')) {
        wp_send_json_error(array('message' => __('You do not have permission to reject members.', 'somity-manager')));
    }
    
    // Get and validate member ID
    $member_id = isset($_POST['member_id']) ? intval($_POST['member_id']) : 0;
    $reason = isset($_POST['reason']) ? sanitize_textarea_field($_POST['reason']) : '';
    
    if (!$member_id) {
        wp_send_json_error(array('message' => __('Invalid member ID.', 'somity-manager')));
    }
    
    if (empty($reason)) {
        wp_send_json_error(array('message' => __('Please provide a reason for rejection.', 'somity-manager')));
    }
    
    // Reject the member
    $result = somity_reject_member($member_id, $reason);
    
    if ($result) {
        wp_send_json_success(array('message' => __('Member has been rejected successfully.', 'somity-manager')));
    } else {
        wp_send_json_error(array('message' => __('Error rejecting member.', 'somity-manager')));
    }
}

/**
 * Get all members with pagination
 */
function somity_get_members_paginated($per_page = 1, $page = 1, $status = 'all', $search = '') {
    $offset = ($page - 1) * $per_page;
    
    $args = array(
        'role' => 'member',
        'number' => $per_page,
        'offset' => $offset,
        'orderby' => 'registered',
        'order' => 'DESC',
    );
    
    // Handle search filter
    if (!empty($search)) {
        $args['search'] = '*' . $search . '*';
    }
    
    // Handle status filter
    if ($status !== 'all') {
        $args['meta_key'] = '_member_status';
        $args['meta_value'] = $status;
    }
    
    $members_query = new WP_User_Query($args);
    
    $members = array();
    
    if (!empty($members_query->get_results())) {
        foreach ($members_query->get_results() as $member) {
            $member_status = get_user_meta($member->ID, '_member_status', true);
            if (empty($member_status)) {
                $member_status = 'pending';
            }
            
            $members[] = (object) array(
                'id' => $member->ID,
                'name' => $member->display_name,
                'email' => $member->user_email,
                'phone' => get_user_meta($member->ID, '_phone', true),
                'address' => get_user_meta($member->ID, '_address', true),
                'join_date' => $member->user_registered,
                'status' => $member_status,
            );
        }
    }
    
    return array(
        'items' => $members,
        'total' => $members_query->get_total(),
        'pages' => ceil($members_query->get_total() / $per_page),
        'current_page' => $page
    );
}



//Installment Functions
/**
 * Get all installments with pagination and filters
 */
function somity_get_installments_paginated($per_page = 10, $page = 1, $status = 'all', $search = '', $month = 'all') {
    $offset = ($page - 1) * $per_page;
    
    $args = array(
        'post_type' => 'installment',
        'post_status' => 'publish',
        'posts_per_page' => $per_page,
        'offset' => $offset,
        'orderby' => 'date',
        'order' => 'DESC',
        'paged' => $page, // Add paged parameter
    );
    
    // Handle search filter
    if (!empty($search)) {
        $args['s'] = $search;
    }
    
    // Handle status filter
    if ($status !== 'all') {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'installment_status',
                'field' => 'slug',
                'terms' => $status,
            ),
        );
    }
    
    // Handle month filter
    if ($month !== 'all') {
        $month = intval($month);
        $year = date('Y');
        
        $args['meta_query'] = array(
            array(
                'key' => '_due_date',
                'value' => $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '%',
                'compare' => 'LIKE',
            ),
        );
    }
    
    $installments_query = new WP_Query($args);
    
    $installments = array();
    
    if ($installments_query->have_posts()) {
        while ($installments_query->have_posts()) {
            $installments_query->the_post();
            $installment_id = get_the_ID();
            $status_terms = wp_get_post_terms($installment_id, 'installment_status');
            $status = !empty($status_terms) ? $status_terms[0]->slug : 'unknown';
            
            $member_id = get_post_meta($installment_id, '_member_id', true);
            $member = get_user_by('id', $member_id);
            
            $installments[] = (object) array(
                'id' => $installment_id,
                'amount' => get_post_meta($installment_id, '_amount', true),
                'due_date' => get_post_meta($installment_id, '_due_date', true),
                'member_id' => $member_id,
                'member_name' => $member ? $member->display_name : __('Unknown', 'somity-manager'),
                'status' => $status,
            );
        }
    }
    
    wp_reset_postdata();
    
    return array(
        'items' => $installments,
        'total' => $installments_query->found_posts,
        'pages' => $installments_query->max_num_pages,
        'current_page' => $page
    );
}

/**
 * Get installment by ID
 */
// function somity_get_installment($installment_id) {
//     $installment = get_post($installment_id);
    
//     if (!$installment || $installment->post_type !== 'installment') {
//         return false;
//     }
    
//     $status_terms = wp_get_post_terms($installment_id, 'installment_status');
//     $status = !empty($status_terms) ? $status_terms[0]->slug : 'unknown';
    
//     $member_id = get_post_meta($installment_id, '_member_id', true);
//     $member = get_user_by('id', $member_id);
    
//     return (object) array(
//         'id' => $installment->ID,
//         'amount' => get_post_meta($installment_id, '_amount', true),
//         'due_date' => get_post_meta($installment_id, '_due_date', true),
//         'member_id' => $member_id,
//         'member_name' => $member ? $member->display_name : __('Unknown', 'somity-manager'),
//         'status' => $status,
//     );
// }

/**
 * Create installment for a member
 */
function somity_create_installment($member_id, $amount, $due_date) {
    $member = get_user_by('id', $member_id);
    
    if (!$member || !in_array('subscriber', $member->roles)) {
        return false;
    }
    
    $installment_data = array(
        'post_title' => 'Installment for ' . $member->display_name,
        'post_content' => 'Monthly installment payment due on ' . $due_date,
        'post_status' => 'publish',
        'post_author' => get_current_user_id(),
        'post_type' => 'installment',
    );
    
    $installment_id = wp_insert_post($installment_data);
    
    if (is_wp_error($installment_id)) {
        return false;
    }
    
    // Save installment meta
    update_post_meta($installment_id, '_member_id', $member_id);
    update_post_meta($installment_id, '_amount', $amount);
    update_post_meta($installment_id, '_due_date', $due_date);
    
    // Set installment status to pending
    wp_set_post_terms($installment_id, 'pending', 'installment_status');
    
    // Create activity record
    $activity_data = array(
        'post_title' => 'Installment Created',
        'post_content' => 'Installment of ' . $amount . ' created for ' . $member->display_name,
        'post_status' => 'publish',
        'post_author' => get_current_user_id(),
        'post_type' => 'activity',
    );
    
    $activity_id = wp_insert_post($activity_data);
    
    if (!is_wp_error($activity_id)) {
        wp_set_post_terms($activity_id, 'installment', 'activity_type');
    }
    
    return $installment_id;
}

/**
 * Generate installments for a member for a year
 */
function somity_generate_yearly_installments($member_id, $amount, $year = null) {
    if (!$year) {
        $year = date('Y');
    }
    
    $member = get_user_by('id', $member_id);
    
    if (!$member || !in_array('subscriber', $member->roles)) {
        return false;
    }
    
    $created_installments = array();
    
    // Generate installments for each month of the year
    for ($month = 1; $month <= 12; $month++) {
        $due_date = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-01';
        
        // Check if installment already exists for this month
        $existing_args = array(
            'post_type' => 'installment',
            'post_status' => 'publish',
            'posts_per_page' => 1,
            'meta_query' => array(
                array(
                    'key' => '_member_id',
                    'value' => $member_id,
                ),
                array(
                    'key' => '_due_date',
                    'value' => $due_date,
                ),
            ),
        );
        
        $existing_query = new WP_Query($existing_args);
        
        if (!$existing_query->have_posts()) {
            $installment_id = somity_create_installment($member_id, $amount, $due_date);
            
            if ($installment_id) {
                $created_installments[] = $installment_id;
            }
        }
    }
    
    return $created_installments;
}

/**
 * Get total pending installments
 */
function somity_get_total_pending_installments() {
    $args = array(
        'post_type' => 'installment',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'tax_query' => array(
            array(
                'taxonomy' => 'installment_status',
                'field' => 'slug',
                'terms' => 'pending',
            ),
        ),
    );
    
    $installments_query = new WP_Query($args);
    
    return $installments_query->found_posts;
}

/**
 * Get total paid installments
 */
function somity_get_total_paid_installments() {
    $args = array(
        'post_type' => 'installment',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'tax_query' => array(
            array(
                'taxonomy' => 'installment_status',
                'field' => 'slug',
                'terms' => 'paid',
            ),
        ),
    );
    
    $installments_query = new WP_Query($args);
    
    return $installments_query->found_posts;
}

/**
 * Get total overdue installments
 */
function somity_get_total_overdue_installments() {
    $args = array(
        'post_type' => 'installment',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'tax_query' => array(
            array(
                'taxonomy' => 'installment_status',
                'field' => 'slug',
                'terms' => 'pending',
            ),
        ),
        'meta_query' => array(
            array(
                'key' => '_due_date',
                'value' => date('Y-m-d'),
                'compare' => '<',
                'type' => 'DATE',
            ),
        ),
    );
    
    $installments_query = new WP_Query($args);
    
    return $installments_query->found_posts;
}

/**
 * Get installment statistics
 */
function somity_get_installment_stats() {
    $total_pending = somity_get_total_pending_installments();
    $total_paid = somity_get_total_paid_installments();
    $total_overdue = somity_get_total_overdue_installments();
    
    return array(
        'total_pending' => $total_pending,
        'total_paid' => $total_paid,
        'total_overdue' => $total_overdue,
    );
}


// AJAX handlers for installments
add_action('wp_ajax_generate_installments', 'somity_ajax_generate_installments');
function somity_ajax_generate_installments() {
    check_ajax_referer('somity-nonce', 'nonce');
    
    if (!current_user_can('administrator')) {
        wp_send_json_error(array('message' => __('You do not have permission to generate installments.', 'somity-manager')));
    }
    
    $generate_for_all = isset($_POST['generate_for_all']) ? intval($_POST['generate_for_all']) : 0;
    $member_id = isset($_POST['member_id']) ? intval($_POST['member_id']) : 0;
    $amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;
    $year = isset($_POST['year']) ? intval($_POST['year']) : date('Y');
    
    if ($amount <= 0) {
        wp_send_json_error(array('message' => __('Invalid amount.', 'somity-manager')));
    }
    
    if ($generate_for_all) {
        // Get all approved members
        $members = get_users(array(
            'role' => 'subscriber',
            'meta_key' => '_member_status',
            'meta_value' => 'approved'
        ));
        
        $total_installments = 0;
        
        foreach ($members as $member) {
            $installments = somity_generate_yearly_installments($member->ID, $amount, $year);
            $total_installments += count($installments);
        }
        
        wp_send_json_success(array('message' => sprintf(__('Successfully generated %d installments for %d members.', 'somity-manager'), $total_installments, count($members))));
    } else {
        if (!$member_id) {
            wp_send_json_error(array('message' => __('Please select a member.', 'somity-manager')));
        }
        
        $installments = somity_generate_yearly_installments($member_id, $amount, $year);
        
        if (count($installments) > 0) {
            wp_send_json_success(array('message' => sprintf(__('Successfully generated %d installments.', 'somity-manager'), count($installments))));
        } else {
            wp_send_json_error(array('message' => __('No installments were generated. They may already exist.', 'somity-manager')));
        }
    }
}

add_action('wp_ajax_mark_installment_paid', 'somity_ajax_mark_installment_paid');
function somity_ajax_mark_installment_paid() {
    check_ajax_referer('somity-nonce', 'nonce');
    
    if (!current_user_can('administrator')) {
        wp_send_json_error(array('message' => __('You do not have permission to mark installments as paid.', 'somity-manager')));
    }
    
    $installment_id = isset($_POST['installment_id']) ? intval($_POST['installment_id']) : 0;
    
    if (!$installment_id) {
        wp_send_json_error(array('message' => __('Invalid installment ID.', 'somity-manager')));
    }
    
    $installment = get_post($installment_id);
    
    if (!$installment || $installment->post_type !== 'installment') {
        wp_send_json_error(array('message' => __('Invalid installment.', 'somity-manager')));
    }
    
    // Update installment status
    wp_set_post_terms($installment_id, 'paid', 'installment_status');
    
    // Get installment details
    $amount = get_post_meta($installment_id, '_amount', true);
    $member_id = get_post_meta($installment_id, '_member_id', true);
    $member = get_user_by('id', $member_id);
    
    // Create activity record
    $activity_data = array(
        'post_title' => 'Installment Paid',
        'post_content' => 'Installment of ' . $amount . ' by ' . $member->display_name . ' was marked as paid',
        'post_status' => 'publish',
        'post_author' => get_current_user_id(),
        'post_type' => 'activity',
    );
    
    $activity_id = wp_insert_post($activity_data);
    
    if (!is_wp_error($activity_id)) {
        wp_set_post_terms($activity_id, 'installment', 'activity_type');
    }
    
    wp_send_json_success(array('message' => __('Installment has been marked as paid successfully.', 'somity-manager')));
}

add_action('wp_ajax_export_installments', 'somity_ajax_export_installments');
function somity_ajax_export_installments() {
    check_ajax_referer('somity-nonce', 'nonce');
    
    if (!current_user_can('administrator')) {
        wp_die(__('You do not have permission to export installments.', 'somity-manager'));
    }
    
    $filterValue = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : 'all';
    $searchTerm = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';
    $monthFilter = isset($_GET['month']) ? sanitize_text_field($_GET['month']) : 'all';
    
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="installments_export.csv"');
    
    $output = fopen('php://output', 'w');
    
    // Add CSV headers
    fputcsv($output, array(
        'ID',
        'Member',
        'Amount',
        'Due Date',
        'Status',
    ));
    
    // Build query arguments
    $args = array(
        'post_type' => 'installment',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'orderby' => 'date',
        'order' => 'DESC',
    );
    
    // Handle search filter
    if (!empty($searchTerm)) {
        $args['s'] = $searchTerm;
    }
    
    // Handle status filter
    if ($filterValue !== 'all') {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'installment_status',
                'field' => 'slug',
                'terms' => $filterValue,
            ),
        );
    }
    
    // Handle month filter
    if ($monthFilter !== 'all') {
        $month = intval($monthFilter);
        $year = date('Y');
        
        $args['meta_query'] = array(
            array(
                'key' => '_due_date',
                'value' => $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '%',
                'compare' => 'LIKE',
            ),
        );
    }
    
    $installments_query = new WP_Query($args);
    
    // Add installment data
    if ($installments_query->have_posts()) {
        while ($installments_query->have_posts()) {
            $installments_query->the_post();
            $installment_id = get_the_ID();
            $member_id = get_post_meta($installment_id, '_member_id', true);
            $member = get_user_by('id', $member_id);
            $status_terms = wp_get_post_terms($installment_id, 'installment_status');
            $status = !empty($status_terms) ? $status_terms[0]->name : 'Unknown';
            
            fputcsv($output, array(
                $installment_id,
                $member->display_name,
                get_post_meta($installment_id, '_amount', true),
                get_post_meta($installment_id, '_due_date', true),
                $status,
            ));
        }
    }
    
    wp_reset_postdata();
    
    fclose($output);
    exit;
}