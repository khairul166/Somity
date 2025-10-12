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

// Get payment details from database
global $wpdb;
 $table_name = $wpdb->prefix . 'somity_payments';
 $payment = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $payment_id));

if (!$payment) {
    wp_redirect(home_url('/member-dashboard/'));
    exit;
}

// Get member details
 $member = get_user_by('id', $payment->member_id);

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
                                    <td><?php echo esc_html($payment->id); ?></td>
                                </tr>
                                <tr>
                                    <th><?php _e('Amount:', 'somity-manager'); ?></th>
                                    <td>$<?php echo esc_html(number_format($payment->amount, 2)); ?></td>
                                </tr>
                                <tr>
                                    <th><?php _e('Transaction ID:', 'somity-manager'); ?></th>
                                    <td><?php echo esc_html($payment->transaction_id); ?></td>
                                </tr>
                                <tr>
                                    <th><?php _e('Payment Date:', 'somity-manager'); ?></th>
                                    <td><?php echo esc_html(date_i18n(get_option('date_format'), strtotime($payment->payment_date))); ?></td>
                                </tr>
                                <tr>
                                    <th><?php _e('Payment Method:', 'somity-manager'); ?></th>
                                    <td><?php echo esc_html(ucwords(str_replace('_', ' ', $payment->payment_method))); ?></td>
                                </tr>
                                <tr>
                                    <th><?php _e('Status:', 'somity-manager'); ?></th>
                                    <td>
                                        <?php
                                        $status_class = '';
                                        $status_icon = '';
                                        
                                        switch ($payment->status) {
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
                                            <?php echo esc_html($status_icon); ?> <?php echo esc_html(ucfirst($payment->status)); ?>
                                        </span>
                                    </td>
                                </tr>
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
                                    <td><?php echo esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($payment->created_at))); ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <?php if (!empty($payment->payment_screenshot)) : ?>
                    <div class="mb-4">
                        <h6><?php _e('Payment Screenshot', 'somity-manager'); ?></h6>
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <img src="<?php echo esc_url($payment->payment_screenshot); ?>" alt="Payment Screenshot" class="img-fluid">
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (current_user_can('administrator') && $payment->status === 'pending') : ?>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-success approve-payment" data-id="<?php echo esc_attr($payment->id); ?>">
                            <i class="bi bi-check-lg"></i> <?php _e('Approve', 'somity-manager'); ?>
                        </button>
                        <button type="button" class="btn btn-danger reject-payment" data-id="<?php echo esc_attr($payment->id); ?>">
                            <i class="bi bi-x-lg"></i> <?php _e('Reject', 'somity-manager'); ?>
                        </button>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Payment Modal -->
<div class="modal fade" id="rejectPaymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php _e('Reject Payment', 'somity-manager'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="reject-payment-form">
                    <div class="mb-3">
                        <label for="rejection-reason" class="form-label"><?php _e('Reason for Rejection', 'somity-manager'); ?></label>
                        <textarea class="form-control" id="rejection-reason" rows="3" required></textarea>
                    </div>
                    <input type="hidden" id="payment-id" value="<?php echo esc_attr($payment_id); ?>">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php _e('Cancel', 'somity-manager'); ?></button>
                <button type="button" class="btn btn-danger" id="confirm-reject"><?php _e('Reject Payment', 'somity-manager'); ?></button>
            </div>
        </div>
    </div>
</div>

<!-- <script>
jQuery(document).ready(function($) {
    // Approve payment button
    $('.approve-payment').on('click', function() {
        var paymentId = $(this).data('id');
        var $btn = $(this);
        
        if (confirm('Are you sure you want to approve this payment?')) {
            $.ajax({
                type: 'POST',
                url: somityAjax.ajaxurl,
                data: {
                    action: 'approve_payment',
                    payment_id: paymentId,
                    nonce: somityAjax.nonce
                },
                beforeSend: function() {
                    $btn.prop('disabled', true);
                    $btn.html('<i class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></i> Processing...');
                },
                success: function(response) {
                    if (response.success) {
                        // Show success message
                        alert(response.data.message);
                        // Reload the page
                        location.reload();
                    } else {
                        // Show error message
                        alert(response.data.message);
                        $btn.prop('disabled', false);
                        $btn.html('<i class="bi bi-check-lg"></i> <?php _e('Approve', 'somity-manager'); ?>');
                    }
                },
                error: function(xhr, status, error) {
                    // Show error message
                    alert(somityAjax.texts.errorMessage);
                    console.log(xhr.responseText);
                    $btn.prop('disabled', false);
                    $btn.html('<i class="bi bi-check-lg"></i> <?php _e('Approve', 'somity-manager'); ?>');
                }
            });
        }
    });
    
    // Reject payment button
    $('.reject-payment').on('click', function() {
        $('#payment-id').val($(this).data('id'));
        $('#rejectPaymentModal').modal('show');
    });
    
    // Confirm reject payment
    $('#confirm-reject').on('click', function() {
        var paymentId = $('#payment-id').val();
        var rejectionReason = $('#rejection-reason').val();
        var $btn = $(this);
        
        if (!rejectionReason) {
            alert('Please provide a reason for rejection.');
            return;
        }
        
        $.ajax({
            type: 'POST',
            url: somityAjax.ajaxurl,
            data: {
                action: 'reject_payment',
                payment_id: paymentId,
                reason: rejectionReason,
                nonce: somityAjax.nonce
            },
            beforeSend: function() {
                $btn.prop('disabled', true);
                $btn.html('<i class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></i> Processing...');
            },
            success: function(response) {
                if (response.success) {
                    // Show success message
                    alert(response.data.message);
                    // Reload the page
                    location.reload();
                } else {
                    // Show error message
                    alert(response.data.message);
                    $btn.prop('disabled', false);
                    $btn.html('<?php _e('Reject Payment', 'somity-manager'); ?>');
                }
            },
            error: function(xhr, status, error) {
                // Show error message
                alert(somityAjax.texts.errorMessage);
                console.log(xhr.responseText);
                $btn.prop('disabled', false);
                $btn.html('<?php _e('Reject Payment', 'somity-manager'); ?>');
            }
        });
    });
});
</script> -->

<?php get_footer(); ?>