<?php
/**
 * Template Name: Submit Payment
 */

if (!is_user_logged_in()) {
    wp_redirect(wp_login_url());
    exit;
}

get_header();
?>

<!-- Submit Payment Content -->
<div class="container my-5">
    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h2 class="mb-0"><?php _e('Submit Payment', 'somity-manager'); ?></h2>
                </div>
                <div class="card-body">
                    <div class="success-message" id="successMessage">
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
                        <?php wp_nonce_field('submit_payment', 'payment_nonce'); ?>
                        <input type="hidden" name="action" value="submit_payment">
                        
                        <div class="mb-4">
                            <label for="installmentMonth" class="form-label"><?php _e('Installment Month', 'somity-manager'); ?></label>
                            <select class="form-select" id="installmentMonth" name="installment_id" required>
                                <option value="" selected disabled><?php _e('Select Month', 'somity-manager'); ?></option>
                                <?php
                                $current_user = wp_get_current_user();
                                
                                // Try to get upcoming installments
                                $installments = somity_get_member_upcoming_installments($current_user->ID, 12);
                                
                                // If no installments found, create some default options
                                if (empty($installments)) {
                                    // Get current month and year
                                    $current_month = date('F Y');
                                    $next_month = date('F Y', strtotime('+1 month'));
                                    $month_after_next = date('F Y', strtotime('+2 months'));
                                    
                                    echo '<option value="current">' . esc_html($current_month) . '</option>';
                                    echo '<option value="next">' . esc_html($next_month) . '</option>';
                                    echo '<option value="next2">' . esc_html($month_after_next) . '</option>';
                                } else {
                                    foreach ($installments as $installment) {
                                        $month_year = date('F Y', strtotime($installment->due_date));
                                        echo '<option value="' . esc_attr($installment->id) . '">' . esc_html($month_year) . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        
                        <div class="mb-4">
                            <label for="paymentAmount" class="form-label"><?php _e('Payment Amount', 'somity-manager'); ?></label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="paymentAmount" name="amount" placeholder="300.00" min="1" step="0.01" required>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="transactionId" class="form-label"><?php _e('Transaction ID', 'somity-manager'); ?></label>
                            <input type="text" class="form-control" id="transactionId" name="transaction_id" placeholder="<?php _e('Enter transaction ID', 'somity-manager'); ?>" required>
                        </div>
                        
                        <div class="mb-4">
                            <label for="paymentDate" class="form-label"><?php _e('Payment Date', 'somity-manager'); ?></label>
                            <input type="date" class="form-control" id="paymentDate" name="payment_date" required>
                        </div>
                        
                        <div class="mb-4">
                            <label for="paymentMethod" class="form-label"><?php _e('Payment Method', 'somity-manager'); ?></label>
                            <select class="form-select" id="paymentMethod" name="payment_method" required>
                                <option value="" selected disabled><?php _e('Select Payment Method', 'somity-manager'); ?></option>
                                <option value="bank_transfer"><?php _e('Bank Transfer', 'somity-manager'); ?></option>
                                <option value="mobile_banking"><?php _e('Mobile Banking', 'somity-manager'); ?></option>
                                <option value="check"><?php _e('Check', 'somity-manager'); ?></option>
                                <option value="cash"><?php _e('Cash', 'somity-manager'); ?></option>
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
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="button" class="btn btn-outline-primary me-md-2" id="cancelBtn"><?php _e('Cancel', 'somity-manager'); ?></button>
                            <button type="submit" class="btn btn-primary"><?php _e('Submit Payment', 'somity-manager'); ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
<!-- Sidebar -->
<div class="col-lg-4">
    <div class="payment-summary">
        <h5 class="mb-3"><?php _e('Payment Summary', 'somity-manager'); ?></h5>
        
        <div class="summary-item">
            <span><?php _e('Member Name:', 'somity-manager'); ?></span>
            <span><?php echo esc_html(wp_get_current_user()->display_name); ?></span>
        </div>
        
        <div class="summary-item">
            <span><?php _e('Member ID:', 'somity-manager'); ?></span>
            <span>CSM-<?php echo date('Y'); ?>-<?php echo str_pad(wp_get_current_user()->ID, 3, '0', STR_PAD_LEFT); ?></span>
        </div>
        
        <div class="summary-item">
            <span><?php _e('Monthly Installment:', 'somity-manager'); ?></span>
            <span>$<?php echo number_format(somity_get_member_monthly_installment(wp_get_current_user()->ID), 2); ?></span>
        </div>
        
        <div class="summary-item">
            <span><?php _e('Outstanding Balance:', 'somity-manager'); ?></span>
            <span>$<?php echo number_format(somity_get_member_outstanding_balance(wp_get_current_user()->ID), 2); ?></span>
        </div>
        
        <div class="summary-item">
            <span><?php _e('Total Paid:', 'somity-manager'); ?></span>
            <span>$<?php echo number_format(somity_get_member_total_paid(wp_get_current_user()->ID), 2); ?></span>
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
</div>

<?php get_footer(); ?>