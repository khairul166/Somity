<?php
/**
 * Template Name: Member Dashboard
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
// Get user meta
 $profile_picture_id = get_user_meta($member_id, 'profile_picture', true);
 $profile_picture_url = $profile_picture_id ? wp_get_attachment_url($profile_picture_id) : '';

get_header();
?>

<!-- Member Dashboard Content -->
<div class="container my-5">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="dashboard-sidebar">
                <div class="user-profile">
                    <div class="user-avatar">
                        <?php 
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
                    <li><a href="<?php echo esc_url(home_url('/member-dashboard/')); ?>" class="active"><i class="bi bi-speedometer2"></i> <?php _e('Dashboard', 'somity-manager'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/submit-payment/')); ?>"><i class="bi bi-cash-stack"></i> <?php _e('Submit Payment', 'somity-manager'); ?></a></li>
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
                <h2 class="mb-4"><?php _e('Member Dashboard', 'somity-manager'); ?></h2>
                
                <!-- Welcome Message -->
                <div class="welcome-message mb-4">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h5 class="card-title"><?php printf(__('Welcome, %s!', 'somity-manager'), esc_html($current_user->display_name)); ?></h5>
                            <p class="card-text"><?php _e('Here you can view your account information, submit payments, and check your installment status.', 'somity-manager'); ?></p>
                        </div>
                    </div>
                </div>
                
                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="stats-card">
                            <div class="stats-icon">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                            </div>
                            <div class="stats-number">$<?php echo number_format(somity_get_member_outstanding_balance($member_id), 2); ?></div>
                            <div class="stats-label"><?php _e('Outstanding Balance', 'somity-manager'); ?></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stats-card">
                            <div class="stats-icon">
                                <i class="bi bi-check-circle-fill"></i>
                            </div>
                            <div class="stats-number">$<?php echo number_format(somity_get_member_total_paid($member_id), 2); ?></div>
                            <div class="stats-label"><?php _e('Total Paid', 'somity-manager'); ?></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stats-card">
                            <div class="stats-icon">
                                <i class="bi bi-clock-fill"></i>
                            </div>
                            <div class="stats-number"><?php echo esc_html(somity_get_member_pending_installments($member_id)); ?></div>
                            <div class="stats-label"><?php _e('Pending Installments', 'somity-manager'); ?></div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Payments -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><?php _e('Recent Payments', 'somity-manager'); ?></h5>
                        <a href="<?php echo esc_url(home_url('/payment-history/')); ?>" class="btn btn-sm btn-outline-primary"><?php _e('View All', 'somity-manager'); ?></a>
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
                                        <th><?php _e('Actions', 'somity-manager'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $payments = somity_get_member_recent_payments($member_id, 5);
                                    if ($payments) {
                                        foreach ($payments as $payment) {
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
                                                <td><?php echo date_i18n(get_option('date_format'), strtotime($payment->payment_date)); ?></td>
                                                <td>$<?php echo number_format($payment->amount, 2); ?></td>
                                                <td><?php echo esc_html($payment->transaction_id); ?></td>
                                                <td>
                                                    <span class="status-badge status-<?php echo esc_attr($payment->status); ?>">
                                                        <?php echo esc_html(ucfirst($payment->status)); ?> <?php echo $status_icon; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="<?php echo esc_url(home_url('/payment-details/?payment_id=' . $payment->id)); ?>" class="btn btn-sm btn-outline-primary" title="<?php _e('View Details', 'somity-manager'); ?>">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                    } else {
                                        echo '<tr><td colspan="5" class="text-center">' . __('No payments found.', 'somity-manager') . '</td></tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Upcoming Installments -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><?php _e('Upcoming Installments', 'somity-manager'); ?></h5>
                        <a href="<?php echo esc_url(home_url('/installment-status/')); ?>" class="btn btn-sm btn-outline-primary"><?php _e('View All', 'somity-manager'); ?></a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
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
                                    $installments = somity_get_member_upcoming_installments($member_id, 5);
                                    if ($installments) {
                                        foreach ($installments as $installment) {
                                            // Get status icon
                                            $status_icon = '';
                                            $status_class = '';
                                            
                                            switch ($installment->status) {
                                                case 'pending':
                                                    $status_icon = '<i class="bi bi-clock-fill"></i>';
                                                    $status_class = '';
                                                    
                                                    // Check if installment is overdue
                                                    if (strtotime($installment->due_date) < time()) {
                                                        $status_class = 'overdue';
                                                    }
                                                    break;
                                                case 'paid':
                                                    $status_icon = '<i class="bi bi-check-circle-fill"></i>';
                                                    break;
                                            }
                                            ?>
                                            <tr>
                                                <td><?php echo date_i18n(get_option('date_format'), strtotime($installment->due_date)); ?></td>
                                                <td>$<?php echo number_format($installment->amount, 2); ?></td>
                                                <td>
                                                    <span class="status-badge status-<?php echo esc_attr($installment->status); ?> <?php echo esc_attr($status_class); ?>">
                                                        <?php echo esc_html(ucfirst($installment->status)); ?> <?php echo $status_icon; ?>
                                                        <?php if ($status_class === 'overdue') : ?>
                                                            <i class="bi bi-exclamation-triangle-fill"></i> <?php _e('Overdue', 'somity-manager'); ?>
                                                        <?php endif; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if ($installment->status === 'pending') : ?>
                                                        <a href="<?php echo esc_url(home_url('/submit-payment/?installment_id=' . $installment->id)); ?>" class="btn btn-sm btn-primary">
                                                            <i class="bi bi-cash-stack"></i> <?php _e('Pay Now', 'somity-manager'); ?>
                                                        </a>
                                                    <?php else : ?>
                                                        <button class="btn btn-sm btn-outline-secondary" disabled><?php _e('Paid', 'somity-manager'); ?></button>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                    } else {
                                        echo '<tr><td colspan="4" class="text-center">' . __('No upcoming installments found.', 'somity-manager') . '</td></tr>';
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

<?php get_footer(); ?>