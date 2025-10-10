<?php
/**
 * Template Name: Member Details
 */

if (!is_user_logged_in() || !current_user_can('administrator')) {
    wp_redirect(home_url());
    exit;
}

// Get member ID from URL
 $member_id = isset($_GET['member_id']) ? intval($_GET['member_id']) : 0;

if (!$member_id) {
    wp_redirect(home_url('/manage-members/'));
    exit;
}

// Get member details
 $member = somity_get_member_details($member_id);

if (!$member) {
    wp_redirect(home_url('/manage-members/'));
    exit;
}

get_header();
?>

<!-- Member Details Content -->
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
                    <li><a href="<?php echo esc_url(home_url('/manage-members/')); ?>" class="active"><i class="bi bi-people"></i> <?php _e('Manage Members', 'somity-manager'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/approve-payments/')); ?>"><i class="bi bi-cash-stack"></i> <?php _e('Approve Payments', 'somity-manager'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/installments/')); ?>"><i class="bi bi-calendar-check"></i> <?php _e('Installments', 'somity-manager'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/reports/')); ?>"><i class="bi bi-graph-up"></i> <?php _e('Reports', 'somity-manager'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/settings/')); ?>"><i class="bi bi-gear"></i> <?php _e('Settings', 'somity-manager'); ?></a></li>
                    <li><a href="<?php echo wp_logout_url(); ?>" id="sidebarLogout"><i class="bi bi-box-arrow-right"></i> <?php _e('Logout', 'somity-manager'); ?></a></li>
                </ul>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-lg-9">
            <div class="dashboard-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><?php _e('Member Details', 'somity-manager'); ?></h2>
                    <a href="<?php echo esc_url(home_url('/manage-members/')); ?>" class="btn btn-outline-primary">
                        <i class="bi bi-arrow-left"></i> <?php _e('Back to Members', 'somity-manager'); ?>
                    </a>
                </div>
                
                <!-- Member Profile Card -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 text-center">
                                <div class="user-avatar mx-auto mb-3" style="width: 100px; height: 100px; font-size: 2.5rem; background-color: #6c5ce7;">
                                    <?php echo esc_html(substr($member->first_name, 0, 1) . substr($member->last_name, 0, 1)); ?>
                                </div>
                                <span class="status-badge status-<?php echo esc_attr($member->status); ?>">
                                    <?php 
                                    switch ($member->status) {
                                        case 'pending':
                                            echo '<i class="bi bi-clock-fill"></i> ' . __('Pending', 'somity-manager');
                                            break;
                                        case 'approved':
                                            echo '<i class="bi bi-check-circle-fill"></i> ' . __('Approved', 'somity-manager');
                                            break;
                                        case 'rejected':
                                            echo '<i class="bi bi-x-circle-fill"></i> ' . __('Rejected', 'somity-manager');
                                            break;
                                    }
                                    ?>
                                </span>
                            </div>
                            <div class="col-md-9">
                                <h3><?php echo esc_html($member->display_name); ?></h3>
                                <p class="text-muted mb-3">CSM-<?php echo date('Y'); ?>-<?php echo str_pad($member->id, 3, '0', STR_PAD_LEFT); ?></p>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong><?php _e('Email:', 'somity-manager'); ?></strong> <?php echo esc_html($member->email); ?></p>
                                        <p><strong><?php _e('Phone:', 'somity-manager'); ?></strong> <?php echo esc_html($member->phone); ?></p>
                                        <p><strong><?php _e('Address:', 'somity-manager'); ?></strong> <?php echo esc_html($member->address); ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong><?php _e('Join Date:', 'somity-manager'); ?></strong> <?php echo date_i18n(get_option('date_format'), strtotime($member->join_date)); ?></p>
                                        <p><strong><?php _e('Monthly Installment:', 'somity-manager'); ?></strong> $<?php echo number_format($member->monthly_installment, 2); ?></p>
                                        <?php if ($member->status === 'rejected') : ?>
                                            <p><strong><?php _e('Rejection Reason:', 'somity-manager'); ?></strong> <?php echo esc_html(get_user_meta($member->id, '_rejection_reason', true)); ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <?php if ($member->status === 'pending') : ?>
                                <div class="mt-3">
                                    <button type="button" class="btn btn-success approve-member" data-id="<?php echo esc_attr($member->id); ?>">
                                        <i class="bi bi-check-lg"></i> <?php _e('Approve Member', 'somity-manager'); ?>
                                    </button>
                                    <button type="button" class="btn btn-danger reject-member" data-id="<?php echo esc_attr($member->id); ?>">
                                        <i class="bi bi-x-lg"></i> <?php _e('Reject Member', 'somity-manager'); ?>
                                    </button>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Financial Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><?php _e('Financial Information', 'somity-manager'); ?></h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="info-box">
                                    <div class="info-box-icon bg-info">
                                        <i class="bi bi-wallet2"></i>
                                    </div>
                                    <div class="info-box-content">
                                        <span class="info-box-text"><?php _e('Current Balance', 'somity-manager'); ?></span>
                                        <span class="info-box-number">$<?php echo number_format($member->balance, 2); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box">
                                    <div class="info-box-icon bg-success">
                                        <i class="bi bi-cash-stack"></i>
                                    </div>
                                    <div class="info-box-content">
                                        <span class="info-box-text"><?php _e('Total Paid', 'somity-manager'); ?></span>
                                        <span class="info-box-number">$<?php echo number_format($member->total_paid, 2); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box">
                                    <div class="info-box-icon bg-warning">
                                        <i class="bi bi-exclamation-triangle"></i>
                                    </div>
                                    <div class="info-box-content">
                                        <span class="info-box-text"><?php _e('Outstanding Balance', 'somity-manager'); ?></span>
                                        <span class="info-box-number">$<?php echo number_format($member->outstanding_balance, 2); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Payments -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><?php _e('Recent Payments', 'somity-manager'); ?></h5>
                        <a href="<?php echo esc_url(home_url('/payment-history/?member_id=' . $member->id)); ?>" class="btn btn-sm btn-outline-primary">
                            <?php _e('View All', 'somity-manager'); ?>
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th><?php _e('Date', 'somity-manager'); ?></th>
                                        <th><?php _e('Amount', 'somity-manager'); ?></th>
                                        <th><?php _e('Transaction ID', 'somity-manager'); ?></th>
                                        <th><?php _e('Status', 'somity-manager'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $payments = somity_get_member_payments($member->id, 5);
                                    
                                    if (!empty($payments)) {
                                        foreach ($payments as $payment) {
                                            // Get status icon
                                            $status_icon = '';
                                            switch ($payment->status) {
                                                case 'pending':
                                                    $status_icon = '<i class="bi bi-clock-fill"></i>';
                                                    break;
                                                case 'approved':
                                                    $status_icon = '<i class="bi bi-check-circle-fill"></i>';
                                                    break;
                                                case 'rejected':
                                                    $status_icon = '<i class="bi bi-x-circle-fill"></i>';
                                                    break;
                                            }
                                            ?>
                                            <tr>
                                                <td><?php echo date_i18n(get_option('date_format'), strtotime($payment->date)); ?></td>
                                                <td>$<?php echo number_format($payment->amount, 2); ?></td>
                                                <td><?php echo esc_html($payment->transaction_id); ?></td>
                                                <td><span class="status-badge status-<?php echo esc_attr($payment->status); ?>"><?php echo esc_html(ucfirst($payment->status)); ?> <?php echo $status_icon; ?></span></td>
                                            </tr>
                                            <?php
                                        }
                                    } else {
                                        echo '<tr><td colspan="4" class="text-center">' . __('No payments found.', 'somity-manager') . '</td></tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Member Rejection Modal -->
<div class="modal fade" id="rejectionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php _e('Reject Member', 'somity-manager'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="rejection-form">
                    <div class="mb-3">
                        <label for="rejection-reason" class="form-label"><?php _e('Reason for Rejection', 'somity-manager'); ?></label>
                        <textarea class="form-control" id="rejection-reason" rows="3" required></textarea>
                    </div>
                    <input type="hidden" id="rejection-member-id">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php _e('Cancel', 'somity-manager'); ?></button>
                <button type="button" class="btn btn-danger" id="confirm-rejection"><?php _e('Reject Member', 'somity-manager'); ?></button>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>

<script>
(function($) {
    'use strict';
    
    $(document).ready(function() {
        // Approve member
        $('.approve-member').on('click', function() {
            var memberId = $(this).data('id');
            var $btn = $(this);
            
            if (confirm('<?php _e('Are you sure you want to approve this member?', 'somity-manager'); ?>')) {
                $.ajax({
                    type: 'POST',
                    url: somityAjax.ajaxurl,
                    data: {
                        action: 'approve_member',
                        member_id: memberId,
                        nonce: somityAjax.nonce
                    },
                    beforeSend: function() {
                        $btn.prop('disabled', true);
                    },
                    success: function(response) {
                        if (response.success) {
                            location.reload();
                        } else {
                            alert('<?php _e('Error: ', 'somity-manager'); ?>' + response.data.message);
                            $btn.prop('disabled', false);
                        }
                    },
                    error: function() {
                        alert('<?php _e('An error occurred. Please try again.', 'somity-manager'); ?>');
                        $btn.prop('disabled', false);
                    }
                });
            }
        });
        
        // Reject member
        $('.reject-member').on('click', function() {
            var memberId = $(this).data('id');
            $('#rejection-member-id').val(memberId);
            $('#rejectionModal').modal('show');
        });
        
        // Confirm rejection
        $('#confirm-rejection').on('click', function() {
            var memberId = $('#rejection-member-id').val();
            var reason = $('#rejection-reason').val();
            
            if (!reason) {
                alert('<?php _e('Please provide a reason for rejection.', 'somity-manager'); ?>');
                return;
            }
            
            $.ajax({
                type: 'POST',
                url: somityAjax.ajaxurl,
                data: {
                    action: 'reject_member',
                    member_id: memberId,
                    reason: reason,
                    nonce: somityAjax.nonce
                },
                beforeSend: function() {
                    $('#confirm-rejection').prop('disabled', true);
                },
                success: function(response) {
                    if (response.success) {
                        $('#rejectionModal').modal('hide');
                        location.reload();
                    } else {
                        alert('<?php _e('Error: ', 'somity-manager'); ?>' + response.data.message);
                        $('#confirm-rejection').prop('disabled', false);
                    }
                },
                error: function() {
                    alert('<?php _e('An error occurred. Please try again.', 'somity-manager'); ?>');
                    $('#confirm-rejection').prop('disabled', false);
                }
            });
        });
    });
})(jQuery);
</script>