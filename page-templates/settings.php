<?php
/**
 * Template Name: Settings
 */

if (!is_user_logged_in() || !current_user_can('administrator')) {
    wp_redirect(home_url());
    exit;
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_settings'])) {
    // Verify nonce
    if (!isset($_POST['settings_nonce']) || !wp_verify_nonce($_POST['settings_nonce'], 'somity_settings_nonce')) {
        wp_die(__('Security check failed.', 'somity-manager'));
    }
    
    // Save settings
    $settings = array(
        'monthly_installment_amount' => floatval($_POST['monthly_installment_amount']),
        'late_payment_fee' => floatval($_POST['late_payment_fee']),
        'payment_methods' => array_map('sanitize_text_field', $_POST['payment_methods']),
        'currency_symbol' => sanitize_text_field($_POST['currency_symbol']),
        'currency_position' => sanitize_text_field($_POST['currency_position']),
        'admin_email' => sanitize_email($_POST['admin_email']),
        'auto_approve_payments' => isset($_POST['auto_approve_payments']) ? 1 : 0,
        'notify_admin_on_payment' => isset($_POST['notify_admin_on_payment']) ? 1 : 0,
        'notify_member_on_approval' => isset($_POST['notify_member_on_approval']) ? 1 : 0,
    );
    
    // Save each setting individually
    foreach ($settings as $key => $value) {
        update_option('somity_' . $key, $value);
    }
    
    // Set success message
    $success_message = __('Settings saved successfully.', 'somity-manager');
}

// Get current settings
 $current_settings = array(
    'monthly_installment_amount' => get_option('somity_monthly_installment_amount', 300.00),
    'late_payment_fee' => get_option('somity_late_payment_fee', 10.00),
    'payment_methods' => get_option('somity_payment_methods', array('bank_transfer', 'mobile_banking')),
    'currency_symbol' => get_option('somity_currency_symbol', '$'),
    'currency_position' => get_option('somity_currency_position', 'before'),
    'admin_email' => get_option('somity_admin_email', get_option('admin_email')),
    'auto_approve_payments' => get_option('somity_auto_approve_payments', 0),
    'notify_admin_on_payment' => get_option('somity_notify_admin_on_payment', 1),
    'notify_member_on_approval' => get_option('somity_notify_member_on_approval', 1),
);

get_header();
?>

<!-- Settings Content -->
<div class="container my-5">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="dashboard-sidebar">
                <div class="user-profile">
                    <div class="user-avatar">
                        <?php 
                        $current_user = wp_get_current_user();
                        $initials = substr($current_user->first_name, 0, 1) . substr($current_user->last_name, 0, 1);
                        echo esc_html($initials);
                        ?>
                    </div>
                    <div class="user-info">
                        <h4><?php echo esc_html($current_user->display_name); ?></h4>
                        <p><?php _e('Administrator', 'somity-manager'); ?></p>
                    </div>
                </div>
                
                <ul class="sidebar-menu">
                    <li><a href="<?php echo esc_url(home_url('/admin-dashboard/')); ?>"><i class="bi bi-speedometer2"></i> <?php _e('Dashboard', 'somity-manager'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/manage-members/')); ?>"><i class="bi bi-people"></i> <?php _e('Manage Members', 'somity-manager'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/approve-payments/')); ?>"><i class="bi bi-cash-stack"></i> <?php _e('Approve Payments', 'somity-manager'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/installments/')); ?>"><i class="bi bi-calendar-check"></i> <?php _e('Installments', 'somity-manager'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/reports/')); ?>"><i class="bi bi-graph-up"></i> <?php _e('Reports', 'somity-manager'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/settings/')); ?>" class="active"><i class="bi bi-gear"></i> <?php _e('Settings', 'somity-manager'); ?></a></li>
                    <li><a href="<?php echo wp_logout_url(); ?>" id="sidebarLogout"><i class="bi bi-box-arrow-right"></i> <?php _e('Logout', 'somity-manager'); ?></a></li>
                </ul>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-lg-9">
            <div class="dashboard-content">
                <h2 class="mb-4"><?php _e('Settings', 'somity-manager'); ?></h2>
                
                <?php if (isset($success_message)) : ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo esc_html($success_message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
                
                <form method="post" id="settings-form">
                    <?php wp_nonce_field('somity_settings_nonce', 'settings_nonce'); ?>
                    
                    <!-- General Settings -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><?php _e('General Settings', 'somity-manager'); ?></h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="monthly_installment_amount" class="form-label"><?php _e('Monthly Installment Amount', 'somity-manager'); ?></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><?php echo esc_html($current_settings['currency_symbol']); ?></span>
                                        <input type="number" class="form-control" id="monthly_installment_amount" name="monthly_installment_amount" value="<?php echo esc_attr($current_settings['monthly_installment_amount']); ?>" min="0" step="0.01" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="late_payment_fee" class="form-label"><?php _e('Late Payment Fee', 'somity-manager'); ?></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><?php echo esc_html($current_settings['currency_symbol']); ?></span>
                                        <input type="number" class="form-control" id="late_payment_fee" name="late_payment_fee" value="<?php echo esc_attr($current_settings['late_payment_fee']); ?>" min="0" step="0.01" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="currency_symbol" class="form-label"><?php _e('Currency Symbol', 'somity-manager'); ?></label>
                                    <input type="text" class="form-control" id="currency_symbol" name="currency_symbol" value="<?php echo esc_attr($current_settings['currency_symbol']); ?>" maxlength="5" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="currency_position" class="form-label"><?php _e('Currency Position', 'somity-manager'); ?></label>
                                    <select class="form-select" id="currency_position" name="currency_position" required>
                                        <option value="before" <?php selected($current_settings['currency_position'], 'before'); ?>><?php _e('Before amount (e.g., $100)', 'somity-manager'); ?></option>
                                        <option value="after" <?php selected($current_settings['currency_position'], 'after'); ?>><?php _e('After amount (e.g., 100$)', 'somity-manager'); ?></option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="admin_email" class="form-label"><?php _e('Admin Email', 'somity-manager'); ?></label>
                                <input type="email" class="form-control" id="admin_email" name="admin_email" value="<?php echo esc_attr($current_settings['admin_email']); ?>" required>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Payment Methods -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><?php _e('Payment Methods', 'somity-manager'); ?></h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="payment_methods[]" id="payment_method_bank_transfer" value="bank_transfer" <?php checked(in_array('bank_transfer', $current_settings['payment_methods'])); ?>>
                                        <label class="form-check-label" for="payment_method_bank_transfer">
                                            <?php _e('Bank Transfer', 'somity-manager'); ?>
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="payment_methods[]" id="payment_method_mobile_banking" value="mobile_banking" <?php checked(in_array('mobile_banking', $current_settings['payment_methods'])); ?>>
                                        <label class="form-check-label" for="payment_method_mobile_banking">
                                            <?php _e('Mobile Banking', 'somity-manager'); ?>
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="payment_methods[]" id="payment_method_cash" value="cash" <?php checked(in_array('cash', $current_settings['payment_methods'])); ?>>
                                        <label class="form-check-label" for="payment_method_cash">
                                            <?php _e('Cash', 'somity-manager'); ?>
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="payment_methods[]" id="payment_method_check" value="check" <?php checked(in_array('check', $current_settings['payment_methods'])); ?>>
                                        <label class="form-check-label" for="payment_method_check">
                                            <?php _e('Check', 'somity-manager'); ?>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Notification Settings -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><?php _e('Notification Settings', 'somity-manager'); ?></h5>
                        </div>
                        <div class="card-body">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="auto_approve_payments" id="auto_approve_payments" value="1" <?php checked($current_settings['auto_approve_payments'], 1); ?>>
                                <label class="form-check-label" for="auto_approve_payments">
                                    <?php _e('Auto-approve payments', 'somity-manager'); ?>
                                </label>
                                <div class="form-text"><?php _e('If enabled, payments will be automatically approved without manual review.', 'somity-manager'); ?></div>
                            </div>
                            
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="notify_admin_on_payment" id="notify_admin_on_payment" value="1" <?php checked($current_settings['notify_admin_on_payment'], 1); ?>>
                                <label class="form-check-label" for="notify_admin_on_payment">
                                    <?php _e('Notify admin when a payment is submitted', 'somity-manager'); ?>
                                </label>
                            </div>
                            
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="notify_member_on_approval" id="notify_member_on_approval" value="1" <?php checked($current_settings['notify_member_on_approval'], 1); ?>>
                                <label class="form-check-label" for="notify_member_on_approval">
                                    <?php _e('Notify member when their payment is approved', 'somity-manager'); ?>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Save Button -->
                    <div class="text-center">
                        <button type="submit" name="save_settings" class="btn btn-primary btn-lg">
                            <i class="bi bi-save"></i> <?php _e('Save Settings', 'somity-manager'); ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>