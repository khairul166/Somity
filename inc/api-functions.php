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
    
    // Get current user
    $current_user = wp_get_current_user();
    $member_id = $current_user->ID;
    
    // Sanitize and validate input
    $amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;
    $transaction_id = isset($_POST['transaction_id']) ? sanitize_text_field($_POST['transaction_id']) : '';
    $payment_date = isset($_POST['payment_date']) ? sanitize_text_field($_POST['payment_date']) : '';
    $payment_method = isset($_POST['payment_method']) ? sanitize_text_field($_POST['payment_method']) : '';
    $payment_note = isset($_POST['payment_note']) ? sanitize_textarea_field($_POST['payment_note']) : '';

    
    error_log('Sanitized data: amount=' . $amount . ', transaction_id=' . $transaction_id . ', payment_date=' . $payment_date);
    
    // Validate required fields
    if (empty($amount) || empty($transaction_id) || empty($payment_date) || empty($payment_method)) {
        error_log('Validation failed: missing required fields');
        wp_send_json_error(array('message' => __('Please fill in all required fields.', 'somity-manager')));
    }
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'somity_payments';
    
    // Handle file upload
    $payment_screenshot = '';
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
            $attachment_id = media_handle_upload('payment_screenshot', 0);
            
            // Check for errors in file upload
            if (is_wp_error($attachment_id)) {
                // Log the error but don't fail the entire submission
                error_log('File upload error: ' . $attachment_id->get_error_message());
            } else {
                // If upload was successful, get the attachment URL
                $payment_screenshot = wp_get_attachment_url($attachment_id);
                error_log('File upload successful. Attachment URL: ' . $payment_screenshot);
            }
        }
    } else {
        error_log('No file uploaded');
    }
    $member_status = get_user_meta( $member_id, '_member_status', true );
    if($member_status != 'approved'){
        error_log('Member is not active: ' . $member_id);
        wp_send_json_error(array('message' => __('Please Wait for active your account', 'somity-manager')));
    }
    // Prepare data for insertion
    $data = array(
        'member_id' => $member_id,
        'amount' => $amount,
        'transaction_id' => $transaction_id,
        'payment_date' => $payment_date,
        'payment_method' => $payment_method,
        'status' => 'pending', // Default status is pending
        'payment_screenshot' => $payment_screenshot,
        'created_at' => current_time('mysql'),
        'updated_at' => current_time('mysql'),
    );
    
    // Format for database insertion
    $format = array('%d', '%f', '%s', '%s', '%s', '%s', '%s', '%s', '%s');
    
    // Insert payment data into the database
    $result = $wpdb->insert($table_name, $data, $format);
    
    if (!$result) {
        error_log('Error inserting payment into database: ' . $wpdb->last_error);
        wp_send_json_error(array('message' => __('Error saving payment record.', 'somity-manager')));
    }
    $payment_id = $wpdb->insert_id;
    error_log('Payment record created with ID: ' . $payment_id);
    

    
    // Create activity record
    $activity_data = array(
        'post_title' => 'Payment Submitted',
        'post_content' => $current_user->display_name . ' submitted a payment of ' . $amount,
        'post_status' => 'publish',
        'post_author' => $member_id,
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



/**
 * Get member's monthly installment amount
 */
function somity_get_member_monthly_installment($member_id) {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'somity_installments';
    
    // Get the most recent installment for the member
    $installment = $wpdb->get_row($wpdb->prepare(
        "SELECT amount FROM $table_name 
         WHERE member_id = %d 
         ORDER BY due_date DESC 
         LIMIT 1",
        $member_id
    ));
    
    if ($installment) {
        return floatval($installment->amount);
    }
    
    // Default monthly installment if no installments found
    return 300.00;
}

/**
 * Get member's outstanding balance
 */
function somity_get_member_outstanding_balance($member_id) {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'somity_installments';
    
    // Get all pending installments for the member
    $result = $wpdb->get_var($wpdb->prepare(
        "SELECT SUM(amount) FROM $table_name 
         WHERE member_id = %d AND status = 'pending'",
        $member_id
    ));
    
    return $result ? floatval($result) : 0;
}

/**
 * Get member's total paid amount
 */
function somity_get_member_total_paid($member_id) {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'somity_payments';
    
    // Get all approved payments for the member
    $result = $wpdb->get_var($wpdb->prepare(
        "SELECT SUM(amount) FROM $table_name 
         WHERE member_id = %d AND status = 'approved'",
        $member_id
    ));
    
    return $result ? floatval($result) : 0;
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



/**
 * Get all installments with pagination and filters
 */
function somity_get_installments_paginated($per_page = 10, $page = 1, $status = 'all', $search = '', $month = 'all') {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'somity_installments';
    $offset = ($page - 1) * $per_page;
    
    // Build the base query
    $query = "SELECT i.*, u.display_name as member_name 
              FROM $table_name i
              LEFT JOIN {$wpdb->users} u ON i.member_id = u.ID";
    
    $count_query = "SELECT COUNT(*) FROM $table_name i";
    
    // Initialize where conditions array
    $where_conditions = array();
    
    // Handle status filter
    if ($status !== 'all') {
        $where_conditions[] = $wpdb->prepare("i.status = %s", $status);
    }
    
    // Handle search filter
    if (!empty($search)) {
        $where_conditions[] = $wpdb->prepare("u.display_name LIKE %s", '%' . $wpdb->esc_like($search) . '%');
    }
    
    // Handle month filter
    if ($month !== 'all') {
        $where_conditions[] = $wpdb->prepare("MONTH(i.due_date) = %d", intval($month));
    }
    
    // Add where conditions to queries if any exist
    if (!empty($where_conditions)) {
        $where_clause = " WHERE " . implode(" AND ", $where_conditions);
        $query .= $where_clause;
        $count_query .= $where_clause;
    }
    
    // Add order and pagination to the main query
    $query .= " ORDER BY i.due_date ASC, i.id ASC";
    $query .= $wpdb->prepare(" LIMIT %d OFFSET %d", $per_page, $offset);
    
    // Get the results
    $installments = $wpdb->get_results($query);
    $total = $wpdb->get_var($count_query);
    
    // Calculate pagination
    $total_pages = $total > 0 ? ceil($total / $per_page) : 1;
    
    return array(
        'items' => $installments,
        'total' => $total,
        'pages' => $total_pages,
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
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'somity_installments';
    
    $member = get_user_by('id', $member_id);
    
    if (!$member || !in_array('subscriber', $member->roles)) {
        return false;
    }
    
    $data = array(
        'member_id' => $member_id,
        'amount' => $amount,
        'due_date' => $due_date,
        'status' => 'pending',
        'created_at' => current_time('mysql'),
        'updated_at' => current_time('mysql'),
    );
    
    $format = array('%d', '%f', '%s', '%s', '%s', '%s');
    
    $result = $wpdb->insert($table_name, $data, $format);
    
    if (!$result) {
        return false;
    }
    
    $installment_id = $wpdb->insert_id;
    
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
        global $wpdb;
        $table_name = $wpdb->prefix . 'somity_installments';
        
        $exists = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name 
             WHERE member_id = %d AND MONTH(due_date) = %d AND YEAR(due_date) = %d",
            $member_id, $month, $year
        ));
        
        if (!$exists) {
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
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'somity_installments';
    
    return $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'pending'");
}

/**
 * Get total paid installments
 */
function somity_get_total_paid_installments() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'somity_installments';
    
    return $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'paid'");
}

/**
 * Get total overdue installments
 */
function somity_get_total_overdue_installments() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'somity_installments';
    $today = date('Y-m-d');
    
    return $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table_name WHERE status = 'pending' AND due_date < %s",
        $today
    ));
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

/**
 * AJAX handler for marking installment as paid
 */
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
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'somity_installments';
    
    // Get installment details
    $installment = $wpdb->get_row($wpdb->prepare(
        "SELECT i.*, m.display_name as member_name FROM $table_name i
         INNER JOIN {$wpdb->users} m ON i.member_id = m.ID
         WHERE i.id = %d",
        $installment_id
    ));
    
    if (!$installment) {
        wp_send_json_error(array('message' => __('Installment not found.', 'somity-manager')));
    }
    
    // Update installment status
    $result = $wpdb->update(
        $table_name,
        array(
            'status' => 'paid',
            'updated_at' => current_time('mysql'),
        ),
        array('id' => $installment_id),
        array('%s', '%s') // both status and updated_at are strings
    );
    
    if ($result === false) {
        wp_send_json_error(array('message' => __('Error updating installment.', 'somity-manager')));
    }
    
    // Create activity record
    $activity_data = array(
        'post_title' => 'Installment Paid',
        'post_content' => 'Installment of ' . $installment->amount . ' by ' . $installment->member_name . ' was marked as paid',
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




/**
 * Get all payments with pagination and filters
 */
function somity_get_payments_paginated($per_page = 10, $page = 1, $status = 'all', $search = '', $month = 'all') {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'somity_payments';
    $offset = ($page - 1) * $per_page;
    
    // Build the base query
    $query = "SELECT p.*, u.display_name as member_name 
              FROM $table_name p
              LEFT JOIN {$wpdb->users} u ON p.member_id = u.ID";
    
    $count_query = "SELECT COUNT(*) FROM $table_name p";
    
    // Initialize where conditions array
    $where_conditions = array();
    
    // Handle status filter
    if ($status !== 'all') {
        $where_conditions[] = $wpdb->prepare("p.status = %s", $status);
    }
    
    // Handle search filter
    if (!empty($search)) {
        $where_conditions[] = $wpdb->prepare("u.display_name LIKE %s", '%' . $wpdb->esc_like($search) . '%');
    }
    
    // Handle month filter
    if ($month !== 'all') {
        $where_conditions[] = $wpdb->prepare("MONTH(p.payment_date) = %d", intval($month));
    }
    
    // Add where conditions to queries if any exist
    if (!empty($where_conditions)) {
        $where_clause = " WHERE " . implode(" AND ", $where_conditions);
        $query .= $where_clause;
        $count_query .= $where_clause;
    }
    
    // Add order and pagination to the main query
    $query .= " ORDER BY p.payment_date DESC, p.id DESC";
    $query .= $wpdb->prepare(" LIMIT %d OFFSET %d", $per_page, $offset);
    
    // Get the results
    $payments = $wpdb->get_results($query);
    $total = $wpdb->get_var($count_query);
    
    // Calculate pagination
    $total_pages = $total > 0 ? ceil($total / $per_page) : 1;
    
    return array(
        'items' => $payments,
        'total' => $total,
        'pages' => $total_pages,
        'current_page' => $page
    );
}

/**
 * Get total pending payments
 */
function somity_get_total_pending_payments() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'somity_payments';
    
    return $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'pending'");
}

/**
 * Get total approved payments
 */
function somity_get_total_approved_payments() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'somity_payments';
    
    return $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'approved'");
}

/**
 * Get total rejected payments
 */
function somity_get_total_rejected_payments() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'somity_payments';
    
    return $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'rejected'");
}

/**
 * Get payment statistics
 */
function somity_get_payment_stats() {
    $total_pending = somity_get_total_pending_payments();
    $total_approved = somity_get_total_approved_payments();
    $total_rejected = somity_get_total_rejected_payments();
    
    return array(
        'total_pending' => $total_pending,
        'total_approved' => $total_approved,
        'total_rejected' => $total_rejected,
    );
}

/**
 * Update payment status
 */
function somity_update_payment_status($payment_id, $status) {
    global $wpdb;

    $table_name = $wpdb->prefix . 'somity_payments';
    $installment_table = $wpdb->prefix . 'somity_installments';
    $credits_table = $wpdb->prefix . 'somity_credits';

    // Get payment details
    $payment = $wpdb->get_row($wpdb->prepare(
        "SELECT p.*, u.display_name as member_name FROM $table_name p
         LEFT JOIN {$wpdb->users} u ON p.member_id = u.ID
         WHERE p.id = %d",
        $payment_id
    ));

    if (!$payment) {
        return false;
    }

    // Update payment status
    $result = $wpdb->update(
        $table_name,
        array(
            'status' => $status,
            'updated_at' => current_time('mysql'),
        ),
        array('id' => $payment_id),
        array('%s', '%s'),
        array('%d')
    );

    if ($result === false) {
        return false;
    }

    if ($status === 'approved') {
        $payment_installment_id = get_post_meta($payment_id, '_installment_id', true);

        if ($payment_installment_id) {
            $installment = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM $installment_table WHERE id = %d", $payment_installment_id
            ));
        } else {
            $installment = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM $installment_table 
                 WHERE member_id = %d AND status IN ('pending', 'partial')
                 ORDER BY due_date ASC LIMIT 1",
                $payment->member_id
            ));
        }

        if ($installment) {
            $current_paid = floatval($installment->paid_amount);
            $installment_total = floatval($installment->amount);
            $payment_amount = floatval($payment->amount);

            $new_paid = $current_paid + $payment_amount;
            $remaining = $installment_total - $new_paid;

            // Update current installment
            $wpdb->update(
                $installment_table,
                array(
                    'paid_amount' => min($new_paid, $installment_total),
                    'remaining_balance' => max(0, $remaining),
                    'status' => $remaining <= 0 ? 'paid' : 'partial',
                    'updated_at' => current_time('mysql'),
                ),
                array('id' => $installment->id),
                array('%f', '%f', '%s', '%s'),
                array('%d')
            );

            // Handle overpayment
            if ($remaining < 0) {
                $overpayment = abs($remaining);
                $method = get_option('somity_overpayment_handling')['method'] ?? 'next_installment';

                if ($method === 'next_installment') {
                    $next_installment = $wpdb->get_row($wpdb->prepare(
                        "SELECT * FROM $installment_table 
                         WHERE member_id = %d AND status IN ('pending', 'partial')
                         AND id != %d
                         ORDER BY due_date ASC LIMIT 1",
                        $payment->member_id, $installment->id
                    ));

                    if ($next_installment) {
                        $next_paid = floatval($next_installment->paid_amount);
                        $next_total = floatval($next_installment->amount);
                        $next_remaining = $next_total - ($next_paid + $overpayment);

                        $wpdb->update(
                            $installment_table,
                            array(
                                'paid_amount' => $next_paid + $overpayment,
                                'remaining_balance' => $next_remaining,
                                'status' => $next_remaining <= 0 ? 'paid' : 'partial',
                                'updated_at' => current_time('mysql'),
                            ),
                            array('id' => $next_installment->id),
                            array('%f', '%f', '%s', '%s'),
                            array('%d')
                        );
                    } else {
                        error_log("Overpayment of {$overpayment} for member {$payment->member_id} with no next installment.");
                    }
                } elseif ($method === 'credit_balance') {
                    $wpdb->insert(
                        $credits_table,
                        array(
                            'member_id' => $payment->member_id,
                            'amount' => $overpayment,
                            'payment_id' => $payment_id,
                            'created_at' => current_time('mysql'),
                        ),
                        array('%d', '%f', '%d', '%s')
                    );
                }
            }
        }
    }

    // Activity log (unchanged)
    $activity_data = array(
        'post_title' => 'Payment ' . ucfirst($status),
        'post_content' => 'Payment of ' . $payment->amount . ' by ' . $payment->member_name . ' was ' . $status,
        'post_status' => 'publish',
        'post_author' => get_current_user_id(),
        'post_type' => 'activity',
    );

    $activity_id = wp_insert_post($activity_data);

    if (!is_wp_error($activity_id)) {
        wp_set_post_terms($activity_id, 'payment', 'activity_type');
    }

    return true;
}


add_action('wp_ajax_approve_payment', 'somity_ajax_approve_payment');
function somity_ajax_approve_payment() {
    check_ajax_referer('somity-nonce', 'nonce');
    
    if (!current_user_can('administrator')) {
        wp_send_json_error(array('message' => __('You do not have permission to approve payments.', 'somity-manager')));
    }
    
    $payment_id = isset($_POST['payment_id']) ? intval($_POST['payment_id']) : 0;
    
    if (!$payment_id) {
        wp_send_json_error(array('message' => __('Invalid payment ID.', 'somity-manager')));
    }
    
    $result = somity_update_payment_status($payment_id, 'approved');
    
    if ($result) {
        wp_send_json_success(array('message' => __('Payment has been approved successfully.', 'somity-manager')));
    } else {
        wp_send_json_error(array('message' => __('Error approving payment.', 'somity-manager')));
    }
}

/**
 * Apply payment to installment
 */
function somity_apply_payment_to_installment($payment_id, $installment_id) {
    global $wpdb;
    
    $payments_table = $wpdb->prefix . 'somity_payments';
    $installments_table = $wpdb->prefix . 'somity_installments';
    
    // Get payment details
    $payment = $wpdb->get_row($wpdb->prepare(
        "SELECT amount FROM $payments_table WHERE id = %d", $payment_id
    ));
    
    // Get installment details
    $installment = $wpdb->get_row($wpdb->prepare(
        "SELECT amount, paid_amount FROM $installments_table WHERE id = %d", $installment_id
    ));
    
    if (!$payment || !$installment) {
        return false;
    }
    
    // Calculate new paid amount
    $new_paid_amount = floatval($installment->paid_amount) + floatval($payment->amount);
    
    // Update installment
    $result = $wpdb->update(
        $installments_table,
        array(
            'paid_amount' => $new_paid_amount,
            'status' => ($new_paid_amount >= floatval($installment->amount)) ? 'paid' : 'pending',
            'updated_at' => current_time('mysql'),
        ),
        array('id' => $installment_id),
        array('%f', '%s', '%s'),
        array('%d')
    );
    
    return $result !== false;
}

add_action('wp_ajax_reject_payment', 'somity_ajax_reject_payment');
function somity_ajax_reject_payment() {
    check_ajax_referer('somity-nonce', 'nonce');
    
    if (!current_user_can('administrator')) {
        wp_send_json_error(array('message' => __('You do not have permission to reject payments.', 'somity-manager')));
    }
    
    $payment_id = isset($_POST['payment_id']) ? intval($_POST['payment_id']) : 0;
    
    if (!$payment_id) {
        wp_send_json_error(array('message' => __('Invalid payment ID.', 'somity-manager')));
    }
    
    $result = somity_update_payment_status($payment_id, 'rejected');
    
    if ($result) {
        wp_send_json_success(array('message' => __('Payment has been rejected successfully.', 'somity-manager')));
    } else {
        wp_send_json_error(array('message' => __('Error rejecting payment.', 'somity-manager')));
    }
}

add_action('wp_ajax_export_payments', 'somity_ajax_export_payments');
function somity_ajax_export_payments() {
    check_ajax_referer('somity-nonce', 'nonce');
    
    if (!current_user_can('administrator')) {
        wp_die(__('You do not have permission to export payments.', 'somity-manager'));
    }
    
    $status = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : 'all';
    $search = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';
    $month = isset($_GET['month']) ? sanitize_text_field($_GET['month']) : 'all';
    
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
    ));
    
    // Get all payments with filters
    $payments_data = somity_get_payments_paginated(-1, 1, $status, $search, $month);
    
    // Add payment data
    if ($payments_data['items']) {
        foreach ($payments_data['items'] as $payment) {
            fputcsv($output, array(
                $payment->id,
                $payment->member_name,
                $payment->amount,
                $payment->transaction_id,
                $payment->payment_date,
                $payment->payment_method,
                $payment->status,
            ));
        }
    }
    
    fclose($output);
    exit;
}


//===== Reports =====
/**
 * Generate report based on type and date range
 */
add_action('wp_ajax_generate_report', 'somity_ajax_generate_report');
function somity_ajax_generate_report() {
    check_ajax_referer('somity-nonce', 'nonce');
    
    if (!current_user_can('administrator')) {
        wp_send_json_error(array('message' => __('You do not have permission to generate reports.', 'somity-manager')));
    }
    
    $report_type = isset($_POST['report_type']) ? sanitize_text_field($_POST['report_type']) : '';
    $date_range = isset($_POST['date_range']) ? sanitize_text_field($_POST['date_range']) : '';
    $start_date = isset($_POST['start_date']) ? sanitize_text_field($_POST['start_date']) : '';
    $end_date = isset($_POST['end_date']) ? sanitize_text_field($_POST['end_date']) : '';
    
    // Calculate date range
    $dates = somity_calculate_date_range($date_range, $start_date, $end_date);
    
    // Generate report based on type
    switch ($report_type) {
        case 'payment_summary':
            $report = somity_generate_payment_summary_report($dates['start'], $dates['end']);
            break;
        case 'member_payments':
            $report = somity_generate_member_payments_report($dates['start'], $dates['end']);
            break;
        case 'installment_summary':
            $report = somity_generate_installment_summary_report($dates['start'], $dates['end']);
            break;
        case 'overdue_installments':
            $report = somity_generate_overdue_installments_report($dates['start'], $dates['end']);
            break;
        default:
            wp_send_json_error(array('message' => __('Invalid report type.', 'somity-manager')));
    }
    
    wp_send_json_success($report);
}

/**
 * Calculate date range based on selection
 */
function somity_calculate_date_range($date_range, $start_date = '', $end_date = '') {
    $now = current_time('timestamp');
    
    switch ($date_range) {
        case 'current_month':
            $start = date('Y-m-01', $now);
            $end = date('Y-m-t', $now);
            break;
        case 'last_month':
            $start = date('Y-m-01', strtotime('first day of last month', $now));
            $end = date('Y-m-t', strtotime('last day of last month', $now));
            break;
        case 'current_quarter':
            $current_quarter = ceil(date('n', $now) / 3);
            $start = date('Y-m-d', mktime(0, 0, 0, ($current_quarter - 1) * 3 + 1, 1, date('Y', $now)));
            $end = date('Y-m-d', mktime(0, 0, 0, $current_quarter * 3, date('t', mktime(0, 0, 0, $current_quarter * 3, 1, date('Y', $now))), date('Y', $now)));
            break;
        case 'last_quarter':
            $last_quarter = ceil(date('n', $now) / 3) - 1;
            if ($last_quarter == 0) {
                $last_quarter = 4;
                $year = date('Y', $now) - 1;
            } else {
                $year = date('Y', $now);
            }
            $start = date('Y-m-d', mktime(0, 0, 0, ($last_quarter - 1) * 3 + 1, 1, $year));
            $end = date('Y-m-d', mktime(0, 0, 0, $last_quarter * 3, date('t', mktime(0, 0, 0, $last_quarter * 3, 1, $year)), $year));
            break;
        case 'current_year':
            $start = date('Y-01-01', $now);
            $end = date('Y-12-31', $now);
            break;
        case 'custom_range':
            $start = $start_date;
            $end = $end_date;
            break;
        default:
            $start = date('Y-m-01', $now);
            $end = date('Y-m-t', $now);
    }
    
    return array(
        'start' => $start,
        'end' => $end
    );
}

/**
 * Generate payment summary report
 */
function somity_generate_payment_summary_report($start_date, $end_date) {
    global $wpdb;
    
    $payments_table = $wpdb->prefix . 'somity_payments';
    
    // Get payment statistics
    $total_payments = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $payments_table 
         WHERE DATE(payment_date) BETWEEN %s AND %s",
        $start_date, $end_date
    ));
    
    $approved_payments = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $payments_table 
         WHERE status = 'approved' AND DATE(payment_date) BETWEEN %s AND %s",
        $start_date, $end_date
    ));
    
    $rejected_payments = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $payments_table 
         WHERE status = 'rejected' AND DATE(payment_date) BETWEEN %s AND %s",
        $start_date, $end_date
    ));
    
    $pending_payments = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $payments_table 
         WHERE status = 'pending' AND DATE(payment_date) BETWEEN %s AND %s",
        $start_date, $end_date
    ));
    
    $total_amount = $wpdb->get_var($wpdb->prepare(
        "SELECT SUM(amount) FROM $payments_table 
         WHERE status = 'approved' AND DATE(payment_date) BETWEEN %s AND %s",
        $start_date, $end_date
    ));
    
    // Get payments by method
    $payments_by_method = $wpdb->get_results($wpdb->prepare(
        "SELECT payment_method, COUNT(*) as count, SUM(amount) as total 
         FROM $payments_table 
         WHERE status = 'approved' AND DATE(payment_date) BETWEEN %s AND %s 
         GROUP BY payment_method",
        $start_date, $end_date
    ));
    
    // Build report data
    $title = sprintf(__('Payment Summary Report (%s to %s)', 'somity-manager'), $start_date, $end_date);
    
    // Build table header
    $header = '<tr>
        <th>' . __('Date', 'somity-manager') . '</th>
        <th>' . __('Total Payments', 'somity-manager') . '</th>
        <th>' . __('Approved', 'somity-manager') . '</th>
        <th>' . __('Rejected', 'somity-manager') . '</th>
        <th>' . __('Pending', 'somity-manager') . '</th>
        <th>' . __('Total Amount', 'somity-manager') . '</th>
    </tr>';
    
    // Build table body
    $body = '<tr>
        <td>' . $start_date . ' - ' . $end_date . '</td>
        <td>' . $total_payments . '</td>
        <td>' . $approved_payments . '</td>
        <td>' . $rejected_payments . '</td>
        <td>' . $pending_payments . '</td>
        <td>' . number_format($total_amount, 2) . '</td>
    </tr>';
    
    // Build summary
    $summary = '<div class="col-md-3">
        <div class="card bg-light">
            <div class="card-body text-center">
                <h5>' . __('Total Payments', 'somity-manager') . '</h5>
                <h3>' . $total_payments . '</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h5>' . __('Approved', 'somity-manager') . '</h5>
                <h3>' . $approved_payments . '</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body text-center">
                <h5>' . __('Rejected', 'somity-manager') . '</h5>
                <h3>' . $rejected_payments . '</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <h5>' . __('Total Amount', 'somity-manager') . '</h5>
                <h3>' . number_format($total_amount, 2) . '</h3>
            </div>
        </div>
    </div>';
    
    // Build CSV data
    $csv_data = '"' . __('Date', 'somity-manager') . '","' . __('Total Payments', 'somity-manager') . '","' . __('Approved', 'somity-manager') . '","' . __('Rejected', 'somity-manager') . '","' . __('Pending', 'somity-manager') . '","' . __('Total Amount', 'somity-manager') . '"' . "\n";
    $csv_data .= '"' . $start_date . ' - ' . $end_date . '","' . $total_payments . '","' . $approved_payments . '","' . $rejected_payments . '","' . $pending_payments . '","' . number_format($total_amount, 2) . '"' . "\n";
    
    return array(
        'title' => $title,
        'header' => $header,
        'body' => $body,
        'summary' => $summary,
        'csv_data' => $csv_data,
        'filename' => 'payment_summary_' . $start_date . '_to_' . $end_date . '.csv'
    );
}

/**
 * Generate member payments report
 */
function somity_generate_member_payments_report($start_date, $end_date) {
    global $wpdb;
    
    $payments_table = $wpdb->prefix . 'somity_payments';
    
    // Get member payments
    $member_payments = $wpdb->get_results($wpdb->prepare(
        "SELECT p.member_id, u.display_name as member_name, 
                COUNT(*) as payment_count, SUM(p.amount) as total_amount,
                SUM(CASE WHEN p.status = 'approved' THEN p.amount ELSE 0 END) as approved_amount,
                SUM(CASE WHEN p.status = 'pending' THEN p.amount ELSE 0 END) as pending_amount
         FROM $payments_table p
         LEFT JOIN {$wpdb->users} u ON p.member_id = u.ID
         WHERE DATE(p.payment_date) BETWEEN %s AND %s
         GROUP BY p.member_id, u.display_name
         ORDER BY u.display_name",
        $start_date, $end_date
    ));
    
    // Build report data
    $title = sprintf(__('Member Payments Report (%s to %s)', 'somity-manager'), $start_date, $end_date);
    
    // Build table header
    $header = '<tr>
        <th>' . __('Member Name', 'somity-manager') . '</th>
        <th>' . __('Member ID', 'somity-manager') . '</th>
        <th>' . __('Payment Count', 'somity-manager') . '</th>
        <th>' . __('Total Amount', 'somity-manager') . '</th>
        <th>' . __('Approved Amount', 'somity-manager') . '</th>
        <th>' . __('Pending Amount', 'somity-manager') . '</th>
    </tr>';
    
    // Build table body
    $body = '';
    $csv_data = '"' . __('Member Name', 'somity-manager') . '","' . __('Member ID', 'somity-manager') . '","' . __('Payment Count', 'somity-manager') . '","' . __('Total Amount', 'somity-manager') . '","' . __('Approved Amount', 'somity-manager') . '","' . __('Pending Amount', 'somity-manager') . '"' . "\n";
    
    foreach ($member_payments as $payment) {
        $body .= '<tr>
            <td>' . esc_html($payment->member_name) . '</td>
            <td>CSM-' . date('Y') . '-' . str_pad($payment->member_id, 3, '0', STR_PAD_LEFT) . '</td>
            <td>' . $payment->payment_count . '</td>
            <td>' . number_format($payment->total_amount, 2) . '</td>
            <td>' . number_format($payment->approved_amount, 2) . '</td>
            <td>' . number_format($payment->pending_amount, 2) . '</td>
        </tr>';
        
        $csv_data .= '"' . $payment->member_name . '","CSM-' . date('Y') . '-' . str_pad($payment->member_id, 3, '0', STR_PAD_LEFT) . '","' . $payment->payment_count . '","' . number_format($payment->total_amount, 2) . '","' . number_format($payment->approved_amount, 2) . '","' . number_format($payment->pending_amount, 2) . '"' . "\n";
    }
    
    // Build summary
    $total_members = count($member_payments);
    $total_payments = array_sum(array_column($member_payments, 'payment_count'));
    $total_amount = array_sum(array_column($member_payments, 'total_amount'));
    $approved_amount = array_sum(array_column($member_payments, 'approved_amount'));
    $pending_amount = array_sum(array_column($member_payments, 'pending_amount'));
    
    $summary = '<div class="col-md-3">
        <div class="card bg-light">
            <div class="card-body text-center">
                <h5>' . __('Total Members', 'somity-manager') . '</h5>
                <h3>' . $total_members . '</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <h5>' . __('Total Payments', 'somity-manager') . '</h5>
                <h3>' . $total_payments . '</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h5>' . __('Approved Amount', 'somity-manager') . '</h5>
                <h3>' . number_format($approved_amount, 2) . '</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <h5>' . __('Pending Amount', 'somity-manager') . '</h5>
                <h3>' . number_format($pending_amount, 2) . '</h3>
            </div>
        </div>
    </div>';
    
    return array(
        'title' => $title,
        'header' => $header,
        'body' => $body,
        'summary' => $summary,
        'csv_data' => $csv_data,
        'filename' => 'member_payments_' . $start_date . '_to_' . $end_date . '.csv'
    );
}

/**
 * Generate installment summary report
 */
function somity_generate_installment_summary_report($start_date, $end_date) {
    global $wpdb;
    
    $installments_table = $wpdb->prefix . 'somity_installments';
    
    // Get installment statistics
    $total_installments = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $installments_table 
         WHERE DATE(due_date) BETWEEN %s AND %s",
        $start_date, $end_date
    ));
    
    $paid_installments = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $installments_table 
         WHERE status = 'paid' AND DATE(due_date) BETWEEN %s AND %s",
        $start_date, $end_date
    ));
    
    $pending_installments = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $installments_table 
         WHERE status = 'pending' AND DATE(due_date) BETWEEN %s AND %s",
        $start_date, $end_date
    ));
    
    $overdue_installments = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $installments_table 
         WHERE status = 'pending' AND due_date < CURDATE() AND DATE(due_date) BETWEEN %s AND %s",
        $start_date, $end_date
    ));
    
    $total_amount = $wpdb->get_var($wpdb->prepare(
        "SELECT SUM(amount) FROM $installments_table 
         WHERE DATE(due_date) BETWEEN %s AND %s",
        $start_date, $end_date
    ));
    
    $paid_amount = $wpdb->get_var($wpdb->prepare(
        "SELECT SUM(amount) FROM $installments_table 
         WHERE status = 'paid' AND DATE(due_date) BETWEEN %s AND %s",
        $start_date, $end_date
    ));
    
    // Build report data
    $title = sprintf(__('Installment Summary Report (%s to %s)', 'somity-manager'), $start_date, $end_date);
    
    // Build table header
    $header = '<tr>
        <th>' . __('Date', 'somity-manager') . '</th>
        <th>' . __('Total Installments', 'somity-manager') . '</th>
        <th>' . __('Paid', 'somity-manager') . '</th>
        <th>' . __('Pending', 'somity-manager') . '</th>
        <th>' . __('Overdue', 'somity-manager') . '</th>
        <th>' . __('Total Amount', 'somity-manager') . '</th>
        <th>' . __('Paid Amount', 'somity-manager') . '</th>
    </tr>';
    
    // Build table body
    $body = '<tr>
        <td>' . $start_date . ' - ' . $end_date . '</td>
        <td>' . $total_installments . '</td>
        <td>' . $paid_installments . '</td>
        <td>' . $pending_installments . '</td>
        <td>' . $overdue_installments . '</td>
        <td>' . number_format($total_amount, 2) . '</td>
        <td>' . number_format($paid_amount, 2) . '</td>
    </tr>';
    
    // Build summary
    $summary = '<div class="col-md-3">
        <div class="card bg-light">
            <div class="card-body text-center">
                <h5>' . __('Total Installments', 'somity-manager') . '</h5>
                <h3>' . $total_installments . '</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h5>' . __('Paid', 'somity-manager') . '</h5>
                <h3>' . $paid_installments . '</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <h5>' . __('Pending', 'somity-manager') . '</h5>
                <h3>' . $pending_installments . '</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body text-center">
                <h5>' . __('Overdue', 'somity-manager') . '</h5>
                <h3>' . $overdue_installments . '</h3>
            </div>
        </div>
    </div>';
    
    // Build CSV data
    $csv_data = '"' . __('Date', 'somity-manager') . '","' . __('Total Installments', 'somity-manager') . '","' . __('Paid', 'somity-manager') . '","' . __('Pending', 'somity-manager') . '","' . __('Overdue', 'somity-manager') . '","' . __('Total Amount', 'somity-manager') . '","' . __('Paid Amount', 'somity-manager') . '"' . "\n";
    $csv_data .= '"' . $start_date . ' - ' . $end_date . '","' . $total_installments . '","' . $paid_installments . '","' . $pending_installments . '","' . $overdue_installments . '","' . number_format($total_amount, 2) . '","' . number_format($paid_amount, 2) . '"' . "\n";
    
    return array(
        'title' => $title,
        'header' => $header,
        'body' => $body,
        'summary' => $summary,
        'csv_data' => $csv_data,
        'filename' => 'installment_summary_' . $start_date . '_to_' . $end_date . '.csv'
    );
}

/**
 * Generate overdue installments report
 */
function somity_generate_overdue_installments_report($start_date, $end_date) {
    global $wpdb;
    
    $installments_table = $wpdb->prefix . 'somity_installments';
    
    // Get overdue installments
    $overdue_installments = $wpdb->get_results($wpdb->prepare(
        "SELECT i.*, u.display_name as member_name
         FROM $installments_table i
         LEFT JOIN {$wpdb->users} u ON i.member_id = u.ID
         WHERE i.status = 'pending' AND i.due_date < CURDATE() AND DATE(i.due_date) BETWEEN %s AND %s
         ORDER BY i.due_date ASC",
        $start_date, $end_date
    ));
    
    // Build report data
    $title = sprintf(__('Overdue Installments Report (%s to %s)', 'somity-manager'), $start_date, $end_date);
    
    // Build table header
    $header = '<tr>
        <th>' . __('Member Name', 'somity-manager') . '</th>
        <th>' . __('Member ID', 'somity-manager') . '</th>
        <th>' . __('Amount', 'somity-manager') . '</th>
        <th>' . __('Due Date', 'somity-manager') . '</th>
        <th>' . __('Days Overdue', 'somity-manager') . '</th>
    </tr>';
    
    // Build table body
    $body = '';
    $csv_data = '"' . __('Member Name', 'somity-manager') . '","' . __('Member ID', 'somity-manager') . '","' . __('Amount', 'somity-manager') . '","' . __('Due Date', 'somity-manager') . '","' . __('Days Overdue', 'somity-manager') . '"' . "\n";
    
    $total_overdue = 0;
    
    foreach ($overdue_installments as $installment) {
        $due_date = new DateTime($installment->due_date);
        $today = new DateTime();
        $days_overdue = $today->diff($due_date)->format('%a');
        
        $body .= '<tr>
            <td>' . esc_html($installment->member_name) . '</td>
            <td>CSM-' . date('Y') . '-' . str_pad($installment->member_id, 3, '0', STR_PAD_LEFT) . '</td>
            <td>' . number_format($installment->amount, 2) . '</td>
            <td>' . date_i18n(get_option('date_format'), strtotime($installment->due_date)) . '</td>
            <td>' . $days_overdue . '</td>
        </tr>';
        
        $csv_data .= '"' . $installment->member_name . '","CSM-' . date('Y') . '-' . str_pad($installment->member_id, 3, '0', STR_PAD_LEFT) . '","' . number_format($installment->amount, 2) . '","' . date_i18n(get_option('date_format'), strtotime($installment->due_date)) . '","' . $days_overdue . '"' . "\n";
        
        $total_overdue += $installment->amount;
    }
    
    // Build summary
    $total_count = count($overdue_installments);
    
    $summary = '<div class="col-md-6">
        <div class="card bg-danger text-white">
            <div class="card-body text-center">
                <h5>' . __('Total Overdue Installments', 'somity-manager') . '</h5>
                <h3>' . $total_count . '</h3>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <h5>' . __('Total Overdue Amount', 'somity-manager') . '</h5>
                <h3>' . number_format($total_overdue, 2) . '</h3>
            </div>
        </div>
    </div>';
    
    return array(
        'title' => $title,
        'header' => $header,
        'body' => $body,
        'summary' => $summary,
        'csv_data' => $csv_data,
        'filename' => 'overdue_installments_' . $start_date . '_to_' . $end_date . '.csv'
    );
}


/**
 * Get member's approved payments count
 */
function somity_get_member_approved_payments_count($member_id) {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'somity_payments';
    
    return $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table_name 
         WHERE member_id = %d AND status = 'approved'",
        $member_id
    ));
}

/**
 * Get member's pending payments count
 */
function somity_get_member_pending_payments_count($member_id) {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'somity_payments';
    
    return $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table_name 
         WHERE member_id = %d AND status = 'pending'",
        $member_id
    ));
}

/**
 * Get member's overdue installments count
 */
function somity_get_member_overdue_installments($member_id) {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'somity_installments';
    
    return $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table_name 
         WHERE member_id = %d AND status = 'pending' AND due_date < CURDATE()",
        $member_id
    ));
}

/**
 * Get member's payments with pagination and filters
 */
function somity_get_member_payments_paginated($member_id, $per_page = 10, $page = 1, $status = 'all', $search = '', $month = 'all') {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'somity_payments';
    $offset = ($page - 1) * $per_page;
    
    // Build the base query
    $query = "SELECT * FROM $table_name WHERE member_id = %d";
    $count_query = "SELECT COUNT(*) FROM $table_name WHERE member_id = %d";
    
    // Initialize where conditions array
    $where_conditions = array();
    $prepare_values = array($member_id);
    
    // Handle status filter
    if ($status !== 'all') {
        $where_conditions[] = "status = %s";
        $prepare_values[] = $status;
    }
    
    // Handle search filter
    if (!empty($search)) {
        $where_conditions[] = "transaction_id LIKE %s";
        $prepare_values[] = '%' . $wpdb->esc_like($search) . '%';
    }
    
    // Handle month filter
    if ($month !== 'all') {
        $where_conditions[] = "MONTH(payment_date) = %d";
        $prepare_values[] = intval($month);
    }
    
    // Add where conditions to queries if any exist
    if (!empty($where_conditions)) {
        $where_clause = " AND " . implode(" AND ", $where_conditions);
        $query .= $where_clause;
        $count_query .= $where_clause;
    }
    
    // Add order and pagination to the main query
    $query .= " ORDER BY payment_date ASC";
    $query .= " LIMIT %d OFFSET %d";
    $prepare_values[] = $per_page;
    $prepare_values[] = $offset;
    
    // Prepare and execute the queries
    if (!empty($prepare_values)) {
        $payments = $wpdb->get_results($wpdb->prepare($query, $prepare_values));
        
        // For count query, we only need the where conditions
        $count_prepare_values = array_slice($prepare_values, 0, -2); // Remove limit and offset values
        $total = $wpdb->get_var($wpdb->prepare($count_query, $count_prepare_values));
    } else {
        $payments = $wpdb->get_results($query);
        $total = $wpdb->get_var($count_query);
    }
    
    // Calculate pagination
    $total_pages = $total > 0 ? ceil($total / $per_page) : 1;
    
    return array(
        'items' => $payments,
        'total' => $total,
        'pages' => $total_pages,
        'current_page' => $page
    );
}

/**
 * Get member's installments with pagination and filters
 */
function somity_get_member_installments_paginated($member_id, $per_page = 10, $page = 1, $status = 'all', $search = '', $year = 'all') {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'somity_installments';
    $offset = ($page - 1) * $per_page;
    
    // Build the base query
    $query = "SELECT * FROM $table_name WHERE member_id = %d";
    $count_query = "SELECT COUNT(*) FROM $table_name WHERE member_id = %d";
    
    // Initialize where conditions array
    $where_conditions = array();
    $prepare_values = array($member_id);
    
    // Handle status filter
    if ($status !== 'all') {
        $where_conditions[] = "status = %s";
        $prepare_values[] = $status;
    }
    
    // Handle search filter
    if (!empty($search)) {
        $where_conditions[] = "MONTHNAME(due_date) LIKE %s";
        $prepare_values[] = '%' . $wpdb->esc_like($search) . '%';
    }
    
    // Handle year filter
    if ($year !== 'all') {
        $where_conditions[] = "YEAR(due_date) = %d";
        $prepare_values[] = intval($year);
    }
    
    // Add where conditions to queries if any exist
    if (!empty($where_conditions)) {
        $where_clause = " AND " . implode(" AND ", $where_conditions);
        $query .= $where_clause;
        $count_query .= $where_clause;
    }
    
    // Add order and pagination to the main query
    $query .= " ORDER BY due_date ASC";
    $query .= " LIMIT %d OFFSET %d";
    $prepare_values[] = $per_page;
    $prepare_values[] = $offset;
    
    // Prepare and execute the queries
    if (!empty($prepare_values)) {
        $installments = $wpdb->get_results($wpdb->prepare($query, $prepare_values));
        
        // For count query, we only need the where conditions
        $count_prepare_values = array_slice($prepare_values, 0, -2); // Remove limit and offset values
        $total = $wpdb->get_var($wpdb->prepare($count_query, $count_prepare_values));
    } else {
        $installments = $wpdb->get_results($query);
        $total = $wpdb->get_var($count_query);
    }
    
    // Calculate pagination
    $total_pages = $total > 0 ? ceil($total / $per_page) : 1;
    
    return array(
        'items' => $installments,
        'total' => $total,
        'pages' => $total_pages,
        'current_page' => $page
    );
}

// AJAX handlers for member exports
add_action('wp_ajax_export_member_payments', 'somity_ajax_export_member_payments');
function somity_ajax_export_member_payments() {
    check_ajax_referer('somity-nonce', 'nonce');
    
    if (!is_user_logged_in()) {
        wp_die(__('You must be logged in to export payments.', 'somity-manager'));
    }
    
    $member_id = get_current_user_id();
    $status = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : 'all';
    $search = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';
    $month = isset($_GET['month']) ? sanitize_text_field($_GET['month']) : 'all';
    
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="payments_export.csv"');
    
    $output = fopen('php://output', 'w');
    
    // Add CSV headers
    fputcsv($output, array(
        'ID',
        'Amount',
        'Transaction ID',
        'Payment Date',
        'Payment Method',
        'Status',
    ));
    
    // Get all payments with filters
    $payments_data = somity_get_member_payments_paginated($member_id, -1, 1, $status, $search, $month);
    
    // Add payment data
    if ($payments_data['items']) {
        foreach ($payments_data['items'] as $payment) {
            fputcsv($output, array(
                $payment->id,
                $payment->amount,
                $payment->transaction_id,
                $payment->payment_date,
                $payment->payment_method,
                $payment->status,
            ));
        }
    }
    
    fclose($output);
    exit;
}

add_action('wp_ajax_export_member_installments', 'somity_ajax_export_member_installments');
function somity_ajax_export_member_installments() {
    check_ajax_referer('somity-nonce', 'nonce');
    
    if (!is_user_logged_in()) {
        wp_die(__('You must be logged in to export installments.', 'somity-manager'));
    }
    
    $member_id = get_current_user_id();
    $status = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : 'all';
    $search = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';
    $year = isset($_GET['year']) ? sanitize_text_field($_GET['year']) : 'all';
    
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="installments_export.csv"');
    
    $output = fopen('php://output', 'w');
    
    // Add CSV headers
    fputcsv($output, array(
        'ID',
        'Amount',
        'Due Date',
        'Status',
    ));
    
    // Get all installments with filters
    $installments_data = somity_get_member_installments_paginated($member_id, -1, 1, $status, $search, $year);
    
    // Add installment data
    if ($installments_data['items']) {
        foreach ($installments_data['items'] as $installment) {
            fputcsv($output, array(
                $installment->id,
                $installment->amount,
                $installment->due_date,
                $installment->status,
            ));
        }
    }
    
    fclose($output);
    exit;
}

/**
 * Get member's total installments
 */
function somity_get_member_total_installments($member_id) {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'somity_installments';
    
    return $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table_name WHERE member_id = %d",
        $member_id
    ));
}


/**
 * Get member's paid installments count
 */
function somity_get_member_paid_installments($member_id) {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'somity_installments';
    
    return $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table_name WHERE member_id = %d AND status = 'paid'",
        $member_id
    ));
}

/**
 * Get member's pending installments count
 */
function somity_get_member_pending_installments($member_id) {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'somity_installments';
    
    return $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table_name WHERE member_id = %d AND status = 'pending'",
        $member_id
    ));
}

// AJAX handler for changing member password
add_action('wp_ajax_change_member_password', 'somity_ajax_change_member_password');
function somity_ajax_change_member_password() {
    check_ajax_referer('somity-nonce', 'nonce');
    
    if (!is_user_logged_in()) {
        wp_send_json_error(array('message' => __('You must be logged in to change your password.', 'somity-manager')));
    }
    
    $current_user = wp_get_current_user();
    $current_password = isset($_POST['current_password']) ? $_POST['current_password'] : '';
    $new_password = isset($_POST['new_password']) ? $_POST['new_password'] : '';
    
    // Check if current password is correct
    if (!wp_check_password($current_password, $current_user->user_pass, $current_user->ID)) {
        wp_send_json_error(array('message' => __('Current password is incorrect.', 'somity-manager')));
    }
    
    // Update password
    $result = wp_update_user(array(
        'ID' => $current_user->ID,
        'user_pass' => $new_password
    ));
    
    if (is_wp_error($result)) {
        wp_send_json_error(array('message' => __('Error updating password. Please try again.', 'somity-manager')));
    }
    
    // Send success response
    wp_send_json_success(array('message' => __('Password changed successfully.', 'somity-manager')));
}

/**
 * Get member's recent payments
 */
function somity_get_member_recent_payments($member_id, $limit = 5) {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'somity_payments';
    
    return $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $table_name 
         WHERE member_id = %d 
         ORDER BY payment_date DESC 
         LIMIT %d",
        $member_id, $limit
    ));
}


/**
 * Get member's pending installments with details
 */
function somity_get_member_pending_installments_with_details($member_id) {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'somity_installments';
    
    return $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $table_name 
         WHERE member_id = %d AND status = 'pending' 
         ORDER BY due_date ASC",
        $member_id
    ));
}

/**
 * Get member's upcoming installments (including partial)
 */
function somity_get_member_upcoming_installments($member_id, $limit = 5) {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'somity_installments';
    
    return $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $table_name 
        WHERE member_id = %d 
        AND status IN (%s, %s)
        ORDER BY 
        CASE 
            WHEN status = 'partial' THEN 0 
            ELSE 1 
        END,
        due_date ASC 
        LIMIT %d",
        $member_id, 'pending', 'partial', $limit
    ));
}

/**
 * Add custom columns to somity_installments table
 */
function somity_add_installment_columns() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'somity_installments';
    $column_name1 = 'paid_amount';
    $column_name2 = 'remaining_balance';

    // Check if the columns already exist
    $check_column1 = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM INFORMATION_SCHEMA.COLUMNS 
         WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND COLUMN_NAME = %s",
        DB_NAME,
        $table_name,
        $column_name1
    ));

    $check_column2 = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM INFORMATION_SCHEMA.COLUMNS 
         WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND COLUMN_NAME = %s",
        DB_NAME,
        $table_name,
        $column_name2
    ));

    // Add paid_amount column if it doesn't exist
    if (empty($check_column1)) {
        $wpdb->query("ALTER TABLE $table_name ADD COLUMN paid_amount FLOAT DEFAULT 0");
    }

    // Add remaining_balance column if it doesn't exist
    if (empty($check_column2)) {
        $wpdb->query("ALTER TABLE $table_name ADD COLUMN remaining_balance FLOAT DEFAULT 0");
    }
}
add_action('after_theme_switch', 'somity_add_installment_columns');

function somity_create_credits_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'somity_credits';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id INT(11) NOT NULL AUTO_INCREMENT,
        member_id INT(11) NOT NULL,
        amount FLOAT NOT NULL,
        payment_id INT(11) NULL,
        created_at DATETIME NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
add_action('init', 'somity_create_credits_table');


function somity_get_member_credit_balance($member_id) {
    global $wpdb;
    $table = $wpdb->prefix . 'somity_credits';
    $result = $wpdb->get_var($wpdb->prepare(
        "SELECT SUM(amount) FROM $table WHERE member_id = %d", $member_id
    ));
    return floatval($result);
}

/**
 * Get total credit balance across all members
 */
function somity_get_total_credit_balance() {
    global $wpdb;
    $table = $wpdb->prefix . 'somity_credits';
    $result = $wpdb->get_var("SELECT SUM(amount) FROM $table");
    return $result ? floatval($result) : 0;
}