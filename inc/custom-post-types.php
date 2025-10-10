<?php
/**
 * Custom Post Types for Somity Manager
 */

// Register Payment post type
function somity_register_payment_post_type() {
    $labels = array(
        'name'                  => _x('Payments', 'Post type general name', 'somity-manager'),
        'singular_name'         => _x('Payment', 'Post type singular name', 'somity-manager'),
        'menu_name'             => _x('Payments', 'Admin Menu text', 'somity-manager'),
        'name_admin_bar'        => _x('Payment', 'Add New on Toolbar', 'somity-manager'),
        'add_new'               => __('Add New', 'somity-manager'),
        'add_new_item'          => __('Add New Payment', 'somity-manager'),
        'new_item'              => __('New Payment', 'somity-manager'),
        'edit_item'             => __('Edit Payment', 'somity-manager'),
        'view_item'             => __('View Payment', 'somity-manager'),
        'all_items'             => __('All Payments', 'somity-manager'),
        'search_items'          => __('Search Payments', 'somity-manager'),
        'parent_item_colon'     => __('Parent Payments:', 'somity-manager'),
        'not_found'             => __('No payments found.', 'somity-manager'),
        'not_found_in_trash'    => __('No payments found in Trash.', 'somity-manager'),
        'featured_image'        => _x('Payment Screenshot', 'Overrides the "Featured Image" phrase for this post type.', 'somity-manager'),
        'set_featured_image'    => _x('Set payment screenshot', 'Overrides the "Set featured image" phrase for this post type.', 'somity-manager'),
        'remove_featured_image' => _x('Remove payment screenshot', 'Overrides the "Remove featured image" phrase for this post type.', 'somity-manager'),
        'use_featured_image'    => _x('Use as payment screenshot', 'Overrides the "Use as featured image" phrase for this post type.', 'somity-manager'),
        'archives'              => _x('Payment Archives', 'The post type archive label used in nav menus.', 'somity-manager'),
        'insert_into_item'      => _x('Insert into payment', 'Overrides the "Insert into post" phrase for this post type.', 'somity-manager'),
        'uploaded_to_this_item' => _x('Uploaded to this payment', 'Overrides the "Uploaded to this post" phrase for this post type.', 'somity-manager'),
        'filter_items_list'     => _x('Filter payments list', 'Screen reader text for the filter links heading on the post type listing screen.', 'somity-manager'),
        'items_list_navigation' => _x('Payments list navigation', 'Screen reader text for the pagination heading on the post type listing screen.', 'somity-manager'),
        'items_list'            => _x('Payments list', 'Screen reader text for the items list heading on the post type listing screen.', 'somity-manager'),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => false,
        'publicly_queryable' => false,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'payment'),
        'capability_type'    => 'post',
        'has_archive'        => false,
        'hierarchical'       => false,
        'menu_position'      => 20,
        'menu_icon'          => 'dashicons-money-alt',
        'supports'           => array('title', 'author'),
        'show_in_rest'       => true,
    );

    register_post_type('payment', $args);
}
add_action('init', 'somity_register_payment_post_type');

// Register Installment post type
function somity_register_installment_post_type() {
    $labels = array(
        'name'                  => _x('Installments', 'Post type general name', 'somity-manager'),
        'singular_name'         => _x('Installment', 'Post type singular name', 'somity-manager'),
        'menu_name'             => _x('Installments', 'Admin Menu text', 'somity-manager'),
        'name_admin_bar'        => _x('Installment', 'Add New on Toolbar', 'somity-manager'),
        'add_new'               => __('Add New', 'somity-manager'),
        'add_new_item'          => __('Add New Installment', 'somity-manager'),
        'new_item'              => __('New Installment', 'somity-manager'),
        'edit_item'             => __('Edit Installment', 'somity-manager'),
        'view_item'             => __('View Installment', 'somity-manager'),
        'all_items'             => __('All Installments', 'somity-manager'),
        'search_items'          => __('Search Installments', 'somity-manager'),
        'parent_item_colon'     => __('Parent Installments:', 'somity-manager'),
        'not_found'             => __('No installments found.', 'somity-manager'),
        'not_found_in_trash'    => __('No installments found in Trash.', 'somity-manager'),
        'archives'              => _x('Installment Archives', 'The post type archive label used in nav menus.', 'somity-manager'),
        'insert_into_item'      => _x('Insert into installment', 'Overrides the "Insert into post" phrase for this post type.', 'somity-manager'),
        'uploaded_to_this_item' => _x('Uploaded to this installment', 'Overrides the "Uploaded to this post" phrase for this post type.', 'somity-manager'),
        'filter_items_list'     => _x('Filter installments list', 'Screen reader text for the filter links heading on the post type listing screen.', 'somity-manager'),
        'items_list_navigation' => _x('Installments list navigation', 'Screen reader text for the pagination heading on the post type listing screen.', 'somity-manager'),
        'items_list'            => _x('Installments list', 'Screen reader text for the items list heading on the post type listing screen.', 'somity-manager'),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => false,
        'publicly_queryable' => false,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'installment'),
        'capability_type'    => 'post',
        'has_archive'        => false,
        'hierarchical'       => false,
        'menu_position'      => 21,
        'menu_icon'          => 'dashicons-calendar-alt',
        'supports'           => array('title', 'author'),
        'show_in_rest'       => true,
    );

    register_post_type('installment', $args);
}
add_action('init', 'somity_register_installment_post_type');

// Register Activity post type
function somity_register_activity_post_type() {
    $labels = array(
        'name'                  => _x('Activities', 'Post type general name', 'somity-manager'),
        'singular_name'         => _x('Activity', 'Post type singular name', 'somity-manager'),
        'menu_name'             => _x('Activities', 'Admin Menu text', 'somity-manager'),
        'name_admin_bar'        => _x('Activity', 'Add New on Toolbar', 'somity-manager'),
        'add_new'               => __('Add New', 'somity-manager'),
        'add_new_item'          => __('Add New Activity', 'somity-manager'),
        'new_item'              => __('New Activity', 'somity-manager'),
        'edit_item'             => __('Edit Activity', 'somity-manager'),
        'view_item'             => __('View Activity', 'somity-manager'),
        'all_items'             => __('All Activities', 'somity-manager'),
        'search_items'          => __('Search Activities', 'somity-manager'),
        'parent_item_colon'     => __('Parent Activities:', 'somity-manager'),
        'not_found'             => __('No activities found.', 'somity-manager'),
        'not_found_in_trash'    => __('No activities found in Trash.', 'somity-manager'),
        'archives'              => _x('Activity Archives', 'The post type archive label used in nav menus.', 'somity-manager'),
        'insert_into_item'      => _x('Insert into activity', 'Overrides the "Insert into post" phrase for this post type.', 'somity-manager'),
        'uploaded_to_this_item' => _x('Uploaded to this activity', 'Overrides the "Uploaded to this post" phrase for this post type.', 'somity-manager'),
        'filter_items_list'     => _x('Filter activities list', 'Screen reader text for the filter links heading on the post type listing screen.', 'somity-manager'),
        'items_list_navigation' => _x('Activities list navigation', 'Screen reader text for the pagination heading on the post type listing screen.', 'somity-manager'),
        'items_list'            => _x('Activities list', 'Screen reader text for the items list heading on the post type listing screen.', 'somity-manager'),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => false,
        'publicly_queryable' => false,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'activity'),
        'capability_type'    => 'post',
        'has_archive'        => false,
        'hierarchical'       => false,
        'menu_position'      => 22,
        'menu_icon'          => 'dashicons-clock',
        'supports'           => array('title', 'editor', 'author'),
        'show_in_rest'       => true,
    );

    register_post_type('activity', $args);
}
add_action('init', 'somity_register_activity_post_type');

// Register custom taxonomies
function somity_register_taxonomies() {
    // Payment Status taxonomy
    $labels = array(
        'name'              => _x('Payment Statuses', 'taxonomy general name', 'somity-manager'),
        'singular_name'     => _x('Payment Status', 'taxonomy singular name', 'somity-manager'),
        'search_items'      => __('Search Payment Statuses', 'somity-manager'),
        'all_items'         => __('All Payment Statuses', 'somity-manager'),
        'parent_item'       => __('Parent Payment Status', 'somity-manager'),
        'parent_item_colon' => __('Parent Payment Status:', 'somity-manager'),
        'edit_item'         => __('Edit Payment Status', 'somity-manager'),
        'update_item'       => __('Update Payment Status', 'somity-manager'),
        'add_new_item'      => __('Add New Payment Status', 'somity-manager'),
        'new_item_name'     => __('New Payment Status Name', 'somity-manager'),
        'menu_name'         => __('Payment Status', 'somity-manager'),
    );

    $args = array(
        'hierarchical'      => false,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'payment-status'),
        'show_in_rest'      => true,
    );

    register_taxonomy('payment_status', array('payment'), $args);
    
    // Installment Status taxonomy
    $labels = array(
        'name'              => _x('Installment Statuses', 'taxonomy general name', 'somity-manager'),
        'singular_name'     => _x('Installment Status', 'taxonomy singular name', 'somity-manager'),
        'search_items'      => __('Search Installment Statuses', 'somity-manager'),
        'all_items'         => __('All Installment Statuses', 'somity-manager'),
        'parent_item'       => __('Parent Installment Status', 'somity-manager'),
        'parent_item_colon' => __('Parent Installment Status:', 'somity-manager'),
        'edit_item'         => __('Edit Installment Status', 'somity-manager'),
        'update_item'       => __('Update Installment Status', 'somity-manager'),
        'add_new_item'      => __('Add New Installment Status', 'somity-manager'),
        'new_item_name'     => __('New Installment Status Name', 'somity-manager'),
        'menu_name'         => __('Installment Status', 'somity-manager'),
    );

    $args = array(
        'hierarchical'      => false,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'installment-status'),
        'show_in_rest'      => true,
    );

    register_taxonomy('installment_status', array('installment'), $args);
    
    // Activity Type taxonomy
    $labels = array(
        'name'              => _x('Activity Types', 'taxonomy general name', 'somity-manager'),
        'singular_name'     => _x('Activity Type', 'taxonomy singular name', 'somity-manager'),
        'search_items'      => __('Search Activity Types', 'somity-manager'),
        'all_items'         => __('All Activity Types', 'somity-manager'),
        'parent_item'       => __('Parent Activity Type', 'somity-manager'),
        'parent_item_colon' => __('Parent Activity Type:', 'somity-manager'),
        'edit_item'         => __('Edit Activity Type', 'somity-manager'),
        'update_item'       => __('Update Activity Type', 'somity-manager'),
        'add_new_item'      => __('Add New Activity Type', 'somity-manager'),
        'new_item_name'     => __('New Activity Type Name', 'somity-manager'),
        'menu_name'         => __('Activity Type', 'somity-manager'),
    );

    $args = array(
        'hierarchical'      => false,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'activity-type'),
        'show_in_rest'      => true,
    );

    register_taxonomy('activity_type', array('activity'), $args);
}
add_action('init', 'somity_register_taxonomies');

// Create default terms for taxonomies
function somity_create_default_terms() {
    // Payment Status terms
    $payment_statuses = array(
        'pending'   => __('Pending', 'somity-manager'),
        'approved'  => __('Approved', 'somity-manager'),
        'rejected'  => __('Rejected', 'somity-manager'),
    );
    
    foreach ($payment_statuses as $slug => $name) {
        if (!term_exists($slug, 'payment_status')) {
            wp_insert_term($name, 'payment_status', array('slug' => $slug));
        }
    }
    
    // Installment Status terms
    $installment_statuses = array(
        'pending'   => __('Pending', 'somity-manager'),
        'paid'      => __('Paid', 'somity-manager'),
        'overdue'   => __('Overdue', 'somity-manager'),
    );
    
    foreach ($installment_statuses as $slug => $name) {
        if (!term_exists($slug, 'installment_status')) {
            wp_insert_term($name, 'installment_status', array('slug' => $slug));
        }
    }
    
    // Activity Type terms
    $activity_types = array(
        'payment'   => __('Payment', 'somity-manager'),
        'member'    => __('Member', 'somity-manager'),
        'system'    => __('System', 'somity-manager'),
    );
    
    foreach ($activity_types as $slug => $name) {
        if (!term_exists($slug, 'activity_type')) {
            wp_insert_term($name, 'activity_type', array('slug' => $slug));
        }
    }
}
add_action('after_setup_theme', 'somity_create_default_terms');

// Add custom meta boxes
function somity_add_meta_boxes() {
    // Payment meta box
    add_meta_box(
        'payment_details',
        __('Payment Details', 'somity-manager'),
        'somity_payment_details_meta_box',
        'payment',
        'normal',
        'high'
    );
    
    // Installment meta box
    add_meta_box(
        'installment_details',
        __('Installment Details', 'somity-manager'),
        'somity_installment_details_meta_box',
        'installment',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'somity_add_meta_boxes');

// Payment details meta box callback
function somity_payment_details_meta_box($post) {
    wp_nonce_field('somity_save_payment_details', 'payment_details_nonce');
    
    $amount = get_post_meta($post->ID, '_amount', true);
    $transaction_id = get_post_meta($post->ID, '_transaction_id', true);
    $payment_date = get_post_meta($post->ID, '_payment_date', true);
    $payment_method = get_post_meta($post->ID, '_payment_method', true);
    $payment_note = get_post_meta($post->ID, '_payment_note', true);
    $rejection_reason = get_post_meta($post->ID, '_rejection_reason', true);
    
    ?>
    <div class="form-field">
        <label for="amount"><?php _e('Amount', 'somity-manager'); ?></label>
        <input type="number" id="amount" name="amount" value="<?php echo esc_attr($amount); ?>" min="0" step="0.01" required>
    </div>
    
    <div class="form-field">
        <label for="transaction_id"><?php _e('Transaction ID', 'somity-manager'); ?></label>
        <input type="text" id="transaction_id" name="transaction_id" value="<?php echo esc_attr($transaction_id); ?>" required>
    </div>
    
    <div class="form-field">
        <label for="payment_date"><?php _e('Payment Date', 'somity-manager'); ?></label>
        <input type="date" id="payment_date" name="payment_date" value="<?php echo esc_attr($payment_date); ?>" required>
    </div>
    
    <div class="form-field">
        <label for="payment_method"><?php _e('Payment Method', 'somity-manager'); ?></label>
        <select id="payment_method" name="payment_method" required>
            <option value=""><?php _e('Select Payment Method', 'somity-manager'); ?></option>
            <option value="bank_transfer" <?php selected($payment_method, 'bank_transfer'); ?>><?php _e('Bank Transfer', 'somity-manager'); ?></option>
            <option value="mobile_banking" <?php selected($payment_method, 'mobile_banking'); ?>><?php _e('Mobile Banking', 'somity-manager'); ?></option>
            <option value="cash" <?php selected($payment_method, 'cash'); ?>><?php _e('Cash', 'somity-manager'); ?></option>
            <option value="check" <?php selected($payment_method, 'check'); ?>><?php _e('Check', 'somity-manager'); ?></option>
        </select>
    </div>
    
    <div class="form-field">
        <label for="payment_note"><?php _e('Payment Note', 'somity-manager'); ?></label>
        <textarea id="payment_note" name="payment_note" rows="3"><?php echo esc_textarea($payment_note); ?></textarea>
    </div>
    
    <?php if ($rejection_reason) : ?>
    <div class="form-field">
        <label for="rejection_reason"><?php _e('Rejection Reason', 'somity-manager'); ?></label>
        <textarea id="rejection_reason" name="rejection_reason" rows="3" readonly><?php echo esc_textarea($rejection_reason); ?></textarea>
    </div>
    <?php endif; ?>
    <?php
}

// Installment details meta box callback
function somity_installment_details_meta_box($post) {
    wp_nonce_field('somity_save_installment_details', 'installment_details_nonce');
    
    $amount = get_post_meta($post->ID, '_amount', true);
    $due_date = get_post_meta($post->ID, '_due_date', true);
    $member_id = get_post_meta($post->ID, '_member_id', true);
    
    ?>
    <div class="form-field">
        <label for="amount"><?php _e('Amount', 'somity-manager'); ?></label>
        <input type="number" id="amount" name="amount" value="<?php echo esc_attr($amount); ?>" min="0" step="0.01" required>
    </div>
    
    <div class="form-field">
        <label for="due_date"><?php _e('Due Date', 'somity-manager'); ?></label>
        <input type="date" id="due_date" name="due_date" value="<?php echo esc_attr($due_date); ?>" required>
    </div>
    
    <div class="form-field">
        <label for="member_id"><?php _e('Member', 'somity-manager'); ?></label>
        <select id="member_id" name="member_id" required>
            <option value=""><?php _e('Select Member', 'somity-manager'); ?></option>
            <?php
            $members = get_users(array('role' => 'member'));
            foreach ($members as $member) {
                echo '<option value="' . esc_attr($member->ID) . '" ' . selected($member_id, $member->ID, false) . '>' . esc_html($member->display_name) . '</option>';
            }
            ?>
        </select>
    </div>
    <?php
}

// Save payment meta box data
function somity_save_payment_details($post_id) {
    if (!isset($_POST['payment_details_nonce']) || !wp_verify_nonce($_POST['payment_details_nonce'], 'somity_save_payment_details')) {
        return;
    }
    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    if (isset($_POST['amount'])) {
        update_post_meta($post_id, '_amount', sanitize_text_field($_POST['amount']));
    }
    
    if (isset($_POST['transaction_id'])) {
        update_post_meta($post_id, '_transaction_id', sanitize_text_field($_POST['transaction_id']));
    }
    
    if (isset($_POST['payment_date'])) {
        update_post_meta($post_id, '_payment_date', sanitize_text_field($_POST['payment_date']));
    }
    
    if (isset($_POST['payment_method'])) {
        update_post_meta($post_id, '_payment_method', sanitize_text_field($_POST['payment_method']));
    }
    
    if (isset($_POST['payment_note'])) {
        update_post_meta($post_id, '_payment_note', sanitize_textarea_field($_POST['payment_note']));
    }
}
add_action('save_post_payment', 'somity_save_payment_details');

// Save installment meta box data
function somity_save_installment_details($post_id) {
    if (!isset($_POST['installment_details_nonce']) || !wp_verify_nonce($_POST['installment_details_nonce'], 'somity_save_installment_details')) {
        return;
    }
    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    if (isset($_POST['amount'])) {
        update_post_meta($post_id, '_amount', sanitize_text_field($_POST['amount']));
    }
    
    if (isset($_POST['due_date'])) {
        update_post_meta($post_id, '_due_date', sanitize_text_field($_POST['due_date']));
    }
    
    if (isset($_POST['member_id'])) {
        update_post_meta($post_id, '_member_id', intval($_POST['member_id']));
    }
}
add_action('save_post_installment', 'somity_save_installment_details');