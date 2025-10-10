<?php
/**
 * Template Name: Payment Details
 */

if (!is_user_logged_in()) {
    wp_redirect(wp_login_url());
    exit;
}

// Get payment ID from URL
 $payment_id = isset($_GET['payment_id']) ? intval($_GET['payment_id']) : 0;

if (!$payment_id) {
    wp_redirect(home_url('/member-dashboard/'));
    exit;
}

// Get payment details
 $payment = get_post($payment_id);

if (!$payment || $payment->post_type !== 'payment') {
    wp_redirect(home_url('/member-dashboard/'));
    exit;
}

// Get payment meta
 $amount = get_post_meta($payment_id, '_amount', true);
 $transaction_id = get_post_meta($payment_id, '_transaction_id', true);
 $payment_date = get_post_meta($payment_id, '_payment_date', true);
 $payment_method = get_post_meta($payment_id, '_payment_method', true);
 $payment_note = get_post_meta($payment_id, '_payment_note', true);
 $rejection_reason = get_post_meta($payment_id, '_rejection_reason', true);

// Get payment status
 $status_terms = wp_get_post_terms($payment_id, 'payment_status');
 $status = !empty($status_terms) ? $status_terms[0]->slug : 'unknown';

// Get member details
 $member = get_user_by('id', $payment->post_author);

get_header();
?>

<div class="container my-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><?php _e('Payment Details', 'somity-manager'); ?></h5>
                    <a href="<?php echo esc_url(home_url('/member-dashboard/')); ?>" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-arrow-left"></i> <?php _e('Back to Dashboard', 'somity-manager'); ?>
                    </a>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6><?php _e('Payment Information', 'somity-manager'); ?></h6>
                            <table class="table table-sm">
                                <tr>
                                    <th width="30%"><?php _e('Payment ID:', 'somity-manager'); ?></th>
                                    <td><?php echo esc_html($payment_id); ?></td>
                                </tr>
                                <tr>
                                    <th><?php _e('Amount:', 'somity-manager'); ?></th>
                                    <td>$<?php echo esc_html(number_format($amount, 2)); ?></td>
                                </tr>
                                <tr>
                                    <th><?php _e('Transaction ID:', 'somity-manager'); ?></th>
                                    <td><?php echo esc_html($transaction_id); ?></td>
                                </tr>
                                <tr>
                                    <th><?php _e('Payment Date:', 'somity-manager'); ?></th>
                                    <td><?php echo esc_html(date('F j, Y', strtotime($payment_date))); ?></td>
                                </tr>
                                <tr>
                                    <th><?php _e('Payment Method:', 'somity-manager'); ?></th>
                                    <td><?php echo esc_html(ucwords(str_replace('_', ' ', $payment_method))); ?></td>
                                </tr>
                                <tr>
                                    <th><?php _e('Status:', 'somity-manager'); ?></th>
                                    <td>
                                        <?php
                                        $status_class = '';
                                        $status_icon = '';
                                        
                                        switch ($status) {
                                            case 'approved':
                                                $status_class = 'status-approved';
                                                $status_icon = '✅';
                                                break;
                                            case 'pending':
                                                $status_class = 'status-pending';
                                                $status_icon = '⏳';
                                                break;
                                            case 'rejected':
                                                $status_class = 'status-rejected';
                                                $status_icon = '❌';
                                                break;
                                        }
                                        ?>
                                        <span class="status-badge <?php echo esc_attr($status_class); ?>">
                                            <?php echo esc_html($status_icon); ?> <?php echo esc_html(ucfirst($status)); ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php if ($status === 'rejected' && !empty($rejection_reason)) : ?>
                                <tr>
                                    <th><?php _e('Rejection Reason:', 'somity-manager'); ?></th>
                                    <td><?php echo esc_html($rejection_reason); ?></td>
                                </tr>
                                <?php endif; ?>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6><?php _e('Member Information', 'somity-manager'); ?></h6>
                            <table class="table table-sm">
                                <tr>
                                    <th width="30%"><?php _e('Name:', 'somity-manager'); ?></th>
                                    <td><?php echo esc_html($member->display_name); ?></td>
                                </tr>
                                <tr>
                                    <th><?php _e('Member ID:', 'somity-manager'); ?></th>
                                    <td>CSM-<?php echo date('Y'); ?>-<?php echo str_pad($member->ID, 3, '0', STR_PAD_LEFT); ?></td>
                                </tr>
                                <tr>
                                    <th><?php _e('Email:', 'somity-manager'); ?></th>
                                    <td><?php echo esc_html($member->user_email); ?></td>
                                </tr>
                                <tr>
                                    <th><?php _e('Submission Date:', 'somity-manager'); ?></th>
                                    <td><?php echo esc_html(date('F j, Y g:i A', strtotime($payment->post_date))); ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <?php if (!empty($payment_note)) : ?>
                    <div class="mb-4">
                        <h6><?php _e('Additional Notes', 'somity-manager'); ?></h6>
                        <div class="card bg-light">
                            <div class="card-body">
                                <?php echo esc_html($payment_note); ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (has_post_thumbnail($payment_id)) : ?>
                    <div class="mb-4">
                        <h6><?php _e('Payment Screenshot', 'somity-manager'); ?></h6>
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <?php echo get_the_post_thumbnail($payment_id, 'large', array('class' => 'img-fluid')); ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (current_user_can('administrator') && $status === 'pending') : ?>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-success approve-payment" data-id="<?php echo esc_attr($payment_id); ?>">
                            <i class="bi bi-check-lg"></i> <?php _e('Approve', 'somity-manager'); ?>
                        </button>
                        <button type="button" class="btn btn-danger reject-payment" data-id="<?php echo esc_attr($payment_id); ?>">
                            <i class="bi bi-x-lg"></i> <?php _e('Reject', 'somity-manager'); ?>
                        </button>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>