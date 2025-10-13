<?php
/**
 * Template Name: Submit Payment
 */

if (!is_user_logged_in()) {
    wp_redirect(wp_login_url());
    exit;
}

// Check if user is a subscriber
if (!current_user_can('subscriber')) {
    wp_redirect(home_url());
    exit;
}

 $current_user = wp_get_current_user();
 $member_id = $current_user->ID;

// Get currency settings
 $currency_symbol = get_option('somity_currency_symbol', '$');
 $currency_position = get_option('somity_currency_position', 'before');

// Get installment ID from URL if available
 $installment_id = isset($_GET['installment_id']) ? intval($_GET['installment_id']) : 0;

// Get installment details if ID is provided
 $installment = null;
if ($installment_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'somity_installments';
    $installment = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table_name WHERE id = %d AND member_id = %d",
        $installment_id, $member_id
    ));
}

get_header();
?>

<!-- Submit Payment Content -->
<div class="container my-5">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="dashboard-sidebar">
                <div class="user-profile">
                    <div class="user-avatar">
                        <?php 
                        $profile_picture_id = get_user_meta($member_id, 'profile_picture', true);
                        $profile_picture_url = $profile_picture_id ? wp_get_attachment_url($profile_picture_id) : '';
                        
                        if ($profile_picture_url) {
                            echo '<img src="' . esc_url($profile_picture_url) . '" alt="' . esc_attr($current_user->display_name) . '" class="rounded-circle">';
                        } else {
                            $initials = substr($current_user->first_name, 0, 1) . substr($current_user->last_name, 0, 1);
                            echo esc_html($initials);
                        }
                        ?>
                    </div>
                    <div class="user-info">
                        <h4><?php echo esc_html($current_user->display_name); ?></h4>
                        <p><?php _e('Member', 'somity-manager'); ?></p>
                        <p class="small text-muted">CSM-<?php echo date('Y'); ?>-<?php echo str_pad($member_id, 3, '0', STR_PAD_LEFT); ?></p>
                    </div>
                </div>
                
                <ul class="sidebar-menu">
                    <li><a href="<?php echo esc_url(home_url('/member-dashboard/')); ?>"><i class="bi bi-speedometer2"></i> <?php _e('Dashboard', 'somity-manager'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/submit-payment/')); ?>" class="active"><i class="bi bi-cash-stack"></i> <?php _e('Submit Payment', 'somity-manager'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/payment-history/')); ?>"><i class="bi bi-clock-history"></i> <?php _e('Payment History', 'somity-manager'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/installment-status/')); ?>"><i class="bi bi-calendar-check"></i> <?php _e('Installment Status', 'somity-manager'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/profile/')); ?>"><i class="bi bi-person-circle"></i> <?php _e('My Profile', 'somity-manager'); ?></a></li>
                    <li><a href="<?php echo wp_logout_url(); ?>" id="sidebarLogout"><i class="bi bi-box-arrow-right"></i> <?php _e('Logout', 'somity-manager'); ?></a></li>
                </ul>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-lg-9">
            <div class="dashboard-content">
                <h2 class="mb-4"><?php _e('Submit Payment', 'somity-manager'); ?></h2>
                
                <!-- Payment Form -->
                <div class="card">
                    <div class="card-body">
                        <div class="success-message" id="successMessage" style="display: none;">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-check-circle-fill text-success me-3" style="font-size: 1.5rem;"></i>
                                <div>
                                    <h5 class="mb-1"><?php _e('Payment Submitted Successfully!', 'somity-manager'); ?></h5>
                                    <p class="mb-0"><?php _e('Your payment has been submitted and is pending approval. You will receive a notification once it\'s processed.', 'somity-manager'); ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Debug Info -->
                        <div id="debugInfo" style="display: none; background: #f8f9fa; padding: 10px; margin-bottom: 15px; border-radius: 5px; font-size: 12px; max-height: 200px; overflow-y: auto;"></div>
                        
                        <form id="paymentForm" method="post" enctype="multipart/form-data">
                            <?php wp_nonce_field('somity-nonce', 'nonce'); ?>
                            <input type="hidden" name="action" value="submit_payment">
                            
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="installmentMonth" class="form-label"><?php _e('Installment Month', 'somity-manager'); ?></label>
                                    <select class="form-select" id="installmentMonth" name="installment_id">
                                        <option value=""><?php _e('Select Installment', 'somity-manager'); ?></option>
                                        <?php
                                        // Get upcoming installments
                                        $installments = somity_get_member_upcoming_installments($member_id, 12);
                                        
                                        if ($installments) {
                                            foreach ($installments as $installment) {
                                                $month_year = date('F Y', strtotime($installment->due_date));
                                                $selected = ($installment_id == $installment->id) ? 'selected' : '';
                                                echo '<option value="' . esc_attr($installment->id) . '" ' . $selected . '>' . esc_html($month_year) . ' - ' . $currency_symbol . number_format($installment->amount, 2) . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="paymentAmount" class="form-label"><?php _e('Payment Amount', 'somity-manager'); ?></label>
                                    <div class="input-group">
                                        <?php if ($currency_position === 'before') : ?>
                                        <span class="input-group-text"><?php echo esc_html($currency_symbol); ?></span>
                                        <?php endif; ?>
                                        <input type="number" class="form-control" id="paymentAmount" name="amount" placeholder="0.00" min="0.01" step="0.01" value="<?php echo $installment ? esc_attr($installment->amount) : ''; ?>" required>
                                        <?php if ($currency_position === 'after') : ?>
                                        <span class="input-group-text"><?php echo esc_html($currency_symbol); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="transactionId" class="form-label"><?php _e('Transaction ID', 'somity-manager'); ?></label>
                                    <input type="text" class="form-control" id="transactionId" name="transaction_id" placeholder="<?php _e('Enter transaction ID', 'somity-manager'); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="paymentDate" class="form-label"><?php _e('Payment Date', 'somity-manager'); ?></label>
                                    <input type="date" class="form-control" id="paymentDate" name="payment_date" value="<?php echo esc_attr(date('Y-m-d')); ?>" required>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="paymentMethod" class="form-label"><?php _e('Payment Method', 'somity-manager'); ?></label>
                                <select class="form-select" id="paymentMethod" name="payment_method" required>
                                    <option value=""><?php _e('Select Payment Method', 'somity-manager'); ?></option>
                                    <?php
                                    // Get payment methods from settings
                                    $payment_methods = get_option('somity_payment_methods', array('bank_transfer', 'mobile_banking'));
                                    
                                    $payment_method_labels = array(
                                        'bank_transfer' => __('Bank Transfer', 'somity-manager'),
                                        'mobile_banking' => __('Mobile Banking', 'somity-manager'),
                                        'cash' => __('Cash', 'somity-manager'),
                                        'check' => __('Check', 'somity-manager')
                                    );
                                    
                                    foreach ($payment_methods as $method) {
                                        if (isset($payment_method_labels[$method])) {
                                            echo '<option value="' . esc_attr($method) . '">' . esc_html($payment_method_labels[$method]) . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            
                            <div class="mb-4">
                                <label for="paymentScreenshot" class="form-label"><?php _e('Payment Screenshot/Receipt', 'somity-manager'); ?></label>
                                <div class="file-upload">
                                    <input type="file" class="form-control" id="paymentScreenshot" name="payment_screenshot" accept="image/*" required>
                                    <div class="file-upload-label">
                                        <i class="bi bi-cloud-arrow-up me-2"></i>
                                        <span id="fileLabel"><?php _e('Click to upload or drag and drop', 'somity-manager'); ?></span>
                                    </div>
                                </div>
                                <div class="form-text"><?php _e('Upload a screenshot of your payment confirmation or receipt (PNG, JPG up to 5MB)', 'somity-manager'); ?></div>
                                <!-- Image preview -->
                                <div id="imagePreview" style="margin-top: 10px; display: none;">
                                    <img src="" alt="Preview" style="max-width: 100%; max-height: 200px; border: 1px solid #ddd; border-radius: 4px;">
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="notes" class="form-label"><?php _e('Additional Notes (Optional)', 'somity-manager'); ?></label>
                                <textarea class="form-control" id="notes" name="payment_note" rows="3" placeholder="<?php _e('Enter any additional information about this payment', 'somity-manager'); ?>"></textarea>
                            </div>
                            
                            <?php if ($installment) : ?>
                            <input type="hidden" name="installment_id" value="<?php echo esc_attr($installment->id); ?>">
                            <?php endif; ?>
                            
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <button type="button" class="btn btn-outline-primary me-md-2" id="cancelBtn"><?php _e('Cancel', 'somity-manager'); ?></button>
                                <button type="submit" class="btn btn-primary"><?php _e('Submit Payment', 'somity-manager'); ?></button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Outstanding Balance -->
                <?php if (!$installment) : ?>
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0"><?php _e('Outstanding Balance', 'somity-manager'); ?></h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th><?php _e('Due Date', 'somity-manager'); ?></th>
                                        <th><?php _e('Amount', 'somity-manager'); ?></th>
                                        <th><?php _e('Status', 'somity-manager'); ?></th>
                                        <th><?php _e('Actions', 'somity-manager'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $outstanding_installments = somity_get_member_pending_installments_with_details($member_id);
                                    
                                    if ($outstanding_installments) {
                                        foreach ($outstanding_installments as $installment) {
                                            ?>
                                            <tr>
                                                <td><?php echo date_i18n(get_option('date_format'), strtotime($installment->due_date)); ?></td>
                                                <td><?php echo ($currency_position === 'before' ? $currency_symbol : '') . number_format($installment->amount, 2) . ($currency_position === 'after' ? $currency_symbol : ''); ?></td>
                                                <td>
                                                    <span class="status-badge status-pending <?php echo strtotime($installment->due_date) < time() ? 'overdue' : ''; ?>">
                                                        <?php _e('Pending', 'somity-manager'); ?>
                                                        <?php if (strtotime($installment->due_date) < time()) : ?>
                                                            <i class="bi bi-exclamation-triangle-fill"></i> <?php _e('Overdue', 'somity-manager'); ?>
                                                        <?php endif; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="<?php echo esc_url(home_url('/submit-payment/?installment_id=' . $installment->id)); ?>" class="btn btn-sm btn-primary">
                                                        <i class="bi bi-cash-stack"></i> <?php _e('Pay Now', 'somity-manager'); ?>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                    } else {
                                        echo '<tr><td colspan="4" class="text-center">' . __('No outstanding installments found.', 'somity-manager') . '</td></tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                            <div class="payment-summary mt-4 p-4 border rounded">
                <h5 class="mb-3"><?php _e('Payment Summary', 'somity-manager'); ?></h5>
                
                <div class="summary-item">
                    <span><?php _e('Member Name:', 'somity-manager'); ?></span>
                    <span><?php echo esc_html($current_user->display_name); ?></span>
                </div>
                
                <div class="summary-item">
                    <span><?php _e('Member ID:', 'somity-manager'); ?></span>
                    <span>CSM-<?php echo date('Y'); ?>-<?php echo str_pad($member_id, 3, '0', STR_PAD_LEFT); ?></span>
                </div>
                
                <div class="summary-item">
                    <span><?php _e('Monthly Installment:', 'somity-manager'); ?></span>
                    <span><?php echo ($currency_position === 'before' ? $currency_symbol : '') . number_format(somity_get_member_monthly_installment($member_id), 2) . ($currency_position === 'after' ? $currency_symbol : ''); ?></span>
                </div>
                
                <div class="summary-item">
                    <span><?php _e('Outstanding Balance:', 'somity-manager'); ?></span>
                    <span><?php echo ($currency_position === 'before' ? $currency_symbol : '') . number_format(somity_get_member_outstanding_balance($member_id), 2) . ($currency_position === 'after' ? $currency_symbol : ''); ?></span>
                </div>
                
                <div class="summary-item">
                    <span><?php _e('Total Paid:', 'somity-manager'); ?></span>
                    <span><?php echo ($currency_position === 'before' ? $currency_symbol : '') . number_format(somity_get_member_total_paid($member_id), 2) . ($currency_position === 'after' ? $currency_symbol : ''); ?></span>
                </div>
                
                <div class="mt-4">
                    <h6><?php _e('Payment Instructions', 'somity-manager'); ?></h6>
                    <ol class="small">
                        <li><?php _e('Select the installment month you\'re paying for', 'somity-manager'); ?></li>
                        <li><?php _e('Enter the exact amount and transaction ID', 'somity-manager'); ?></li>
                        <li><?php _e('Upload a clear screenshot of your payment confirmation', 'somity-manager'); ?></li>
                        <li><?php _e('Submit and wait for admin approval', 'somity-manager'); ?></li>
                    </ol>
                </div>
                
                <div class="mt-4 p-3 bg-light rounded">
                    <h6><?php _e('Bank Details', 'somity-manager'); ?></h6>
                    <p class="mb-1 small"><strong><?php _e('Bank Name:', 'somity-manager'); ?></strong> <?php echo esc_html(get_option('somity_bank_name', 'Community Savings Bank')); ?></p>
                    <p class="mb-1 small"><strong><?php _e('Account Name:', 'somity-manager'); ?></strong> <?php echo esc_html(get_option('somity_bank_account_name', 'Community Savings Somity')); ?></p>
                    <p class="mb-0 small"><strong><?php _e('Account Number:', 'somity-manager'); ?></strong> <?php echo esc_html(get_option('somity_bank_account_number', '1234567890')); ?></p>
                </div>
            </div>
            </div>

        </div>
        
        <!-- Sidebar
        <div class="col-lg-3">

        </div> -->
    </div>
</div>

<?php get_footer(); ?>