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