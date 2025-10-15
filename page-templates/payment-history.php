<?php
/**
 * Template Name: Payment History
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

<!-- Payment History Content -->
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
                    <li><a href="<?php echo esc_url(home_url('/member-dashboard/')); ?>"><i class="bi bi-speedometer2"></i> <?php _e('Dashboard', 'somity-manager'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/submit-payment/')); ?>"><i class="bi bi-cash-stack"></i> <?php _e('Submit Payment', 'somity-manager'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/payment-history/')); ?>" class="active"><i class="bi bi-clock-history"></i> <?php _e('Payment History', 'somity-manager'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/installment-status/')); ?>"><i class="bi bi-calendar-check"></i> <?php _e('Installment Status', 'somity-manager'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/profile/')); ?>"><i class="bi bi-person-circle"></i> <?php _e('My Profile', 'somity-manager'); ?></a></li>
                    <li><a href="<?php echo wp_logout_url(); ?>" id="sidebarLogout"><i class="bi bi-box-arrow-right"></i> <?php _e('Logout', 'somity-manager'); ?></a></li>
                </ul>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-lg-9">
            <div class="dashboard-content">
                <h2 class="mb-4"><?php _e('Payment History', 'somity-manager'); ?></h2>
                
                <!-- Payment Summary -->
                <div class="row mb-4">
                    <?php
                        $overpayment_method = get_option('somity_overpayment_handling', 'next_installment');
                        $column_class = ($overpayment_method === 'credit_balance') ? 'col-md-3' : 'col-md-4';
                    ?>
                    <div class="<?php echo esc_attr($column_class); ?>">
                        <div class="stats-card">
                            <div class="stats-icon">
                                <i class="bi bi-cash-stack"></i>
                            </div>
                            <div class="stats-number"><?php echo esc_html($current_settings['currency_symbol']); ?><?php echo number_format(somity_get_member_total_paid($member_id), 2); ?></div>
                            <div class="stats-label"><?php _e('Total Paid', 'somity-manager'); ?></div>
                        </div>
                    </div>
                    <div class="<?php echo esc_attr($column_class); ?>">
                        <div class="stats-card">
                            <div class="stats-icon">
                                <i class="bi bi-check-circle-fill"></i>
                            </div>
                            <div class="stats-number"><?php echo somity_get_member_approved_payments_count($member_id); ?></div>
                            <div class="stats-label"><?php _e('Approved Payments', 'somity-manager'); ?></div>
                        </div>
                    </div>
                    <div class="<?php echo esc_attr($column_class); ?>">
                        <div class="stats-card">
                            <div class="stats-icon">
                                <i class="bi bi-clock-fill"></i>
                            </div>
                            <div class="stats-number"><?php echo somity_get_member_pending_payments_count($member_id); ?></div>
                            <div class="stats-label"><?php _e('Pending Payments', 'somity-manager'); ?></div>
                        </div>
                    </div>
                    <?php if ($overpayment_method === 'credit_balance') : ?>
                        <div class="<?php echo esc_attr($column_class); ?>">
                            <div class="stats-card">
                                <div class="stats-icon">
                                    <i class="bi bi bi-wallet2"></i>
                                </div>
                                <div class="stats-number"><?php echo esc_html(somity_get_member_credit_balance($member_id)); ?></div>
                                <div class="stats-label"><?php _e('Credit Balance', 'somity-manager'); ?></div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Search and Filter -->
                <div class="search-filter mb-4">
                    <form id="payments-filter-form" method="get" action="<?php echo esc_url(get_permalink()); ?>">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                                    <input type="text" name="search" class="form-control" placeholder="<?php _e('Search by transaction ID...', 'somity-manager'); ?>" value="<?php echo isset($_GET['search']) ? esc_attr($_GET['search']) : ''; ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select name="status" class="form-select">
                                    <option value="all" <?php selected(isset($_GET['status']) ? $_GET['status'] : 'all', 'all'); ?>><?php _e('All Statuses', 'somity-manager'); ?></option>
                                    <option value="pending" <?php selected(isset($_GET['status']) ? $_GET['status'] : 'all', 'pending'); ?>><?php _e('Pending', 'somity-manager'); ?></option>
                                    <option value="approved" <?php selected(isset($_GET['status']) ? $_GET['status'] : 'all', 'approved'); ?>><?php _e('Approved', 'somity-manager'); ?></option>
                                    <option value="rejected" <?php selected(isset($_GET['status']) ? $_GET['status'] : 'all', 'rejected'); ?>><?php _e('Rejected', 'somity-manager'); ?></option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="month" class="form-select">
                                    <option value="all" <?php selected(isset($_GET['month']) ? $_GET['month'] : 'all', 'all'); ?>><?php _e('All Months', 'somity-manager'); ?></option>
                                    <?php
                                    // Generate month options for the current year
                                    for ($i = 1; $i <= 12; $i++) {
                                        $month_name = date('F Y', mktime(0, 0, 0, $i, 1, date('Y')));
                                        echo '<option value="' . $i . '" ' . selected(isset($_GET['month']) ? $_GET['month'] : 'all', $i, false) . '>' . $month_name . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100"><?php _e('Filter', 'somity-manager'); ?></button>
                            </div>
                        </div>
                    </form>
                </div>
                
                <!-- Payments Table -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><?php _e('Payment History', 'somity-manager'); ?></h5>
                        <div>
                            <button id="export-payments" class="btn btn-sm btn-outline-primary"><i class="bi bi-download"></i> <?php _e('Export', 'somity-manager'); ?></button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="payments-table">
                                <thead>
                                    <tr>
                                        <th><?php _e('Date', 'somity-manager'); ?></th>
                                        <th><?php _e('Amount', 'somity-manager'); ?></th>
                                        <th><?php _e('Transaction ID', 'somity-manager'); ?></th>
                                        <th><?php _e('Payment Method', 'somity-manager'); ?></th>
                                        <th><?php _e('Status', 'somity-manager'); ?></th>
                                        <th><?php _e('Actions', 'somity-manager'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Get current page number
                                    $paged = max(1, get_query_var('paged') ? get_query_var('paged') : (isset($_GET['paged']) ? intval($_GET['paged']) : 1));
                                    
                                    // Get filter values
                                    $status = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : 'all';
                                    $search = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';
                                    $month = isset($_GET['month']) ? sanitize_text_field($_GET['month']) : 'all';
                                    
                                    // Get paginated payments
                                    $payments_data = somity_get_member_payments_paginated($member_id, 10, $paged, $status, $search, $month);
                                
                                    if ($payments_data['items']) {
                                        foreach ($payments_data['items'] as $payment) {
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
                                                <td><?php echo date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($payment->payment_date)); ?></td>
                                                <td><?php echo esc_html($current_settings['currency_symbol']); ?><?php echo number_format($payment->amount, 2); ?></td>
                                                <td><?php echo esc_html($payment->transaction_id); ?></td>
                                                <td><?php echo esc_html(ucwords(str_replace('_', ' ', $payment->payment_method))); ?></td>
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
                                        echo '<tr><td colspan="6" class="text-center">' . __('No payments found.', 'somity-manager') . '</td></tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-between align-items-center">
                        <div>
                            <?php 
                            $start_record = ($payments_data['current_page'] - 1) * 10 + 1;
                            $end_record = min($start_record + 9, $payments_data['total']);
                            echo sprintf(
                                __('Showing %d-%d of %d records', 'somity-manager'),
                                $start_record,
                                $end_record,
                                $payments_data['total']
                            );
                            ?>
                        </div>
                        <nav>
                            <ul class="pagination mb-0">
                                <?php
                                // Build query parameters array for pagination links
                                $query_params = array(
                                    'status' => $status,
                                    'search' => $search,
                                    'month' => $month
                                );
                                
                                // Remove empty parameters
                                foreach ($query_params as $key => $value) {
                                    if ($value === 'all' || $value === '') {
                                        unset($query_params[$key]);
                                    }
                                }
                                
                                // Get current page URL without query parameters
                                $current_url = get_permalink();
                                
                                // Previous page
                                if ($payments_data['current_page'] > 1) {
                                    $prev_page = $payments_data['current_page'] - 1;
                                    $prev_link = add_query_arg(array_merge($query_params, array('paged' => $prev_page)), $current_url);
                                    echo '<li class="page-item"><a class="page-link" href="' . esc_url($prev_link) . '">' . __('Previous', 'somity-manager') . '</a></li>';
                                } else {
                                    echo '<li class="page-item disabled"><a class="page-link" href="#" tabindex="-1">' . __('Previous', 'somity-manager') . '</a></li>';
                                }
                                
                                // Page numbers - only show a limited range
                                $start_page = max(1, $payments_data['current_page'] - 2);
                                $end_page = min($payments_data['pages'], $payments_data['current_page'] + 2);
                                
                                // First page
                                if ($start_page > 1) {
                                    $first_link = add_query_arg(array_merge($query_params, array('paged' => 1)), $current_url);
                                    echo '<li class="page-item"><a class="page-link" href="' . esc_url($first_link) . '">1</a></li>';
                                    if ($start_page > 2) {
                                        echo '<li class="page-item disabled"><a class="page-link" href="#">...</a></li>';
                                    }
                                }
                                
                                // Page numbers
                                for ($i = $start_page; $i <= $end_page; $i++) {
                                    if ($i == $payments_data['current_page']) {
                                        echo '<li class="page-item active"><a class="page-link" href="#">' . $i . '</a></li>';
                                    } else {
                                        $page_link = add_query_arg(array_merge($query_params, array('paged' => $i)), $current_url);
                                        echo '<li class="page-item"><a class="page-link" href="' . esc_url($page_link) . '">' . $i . '</a></li>';
                                    }
                                }
                                
                                // Last page
                                if ($end_page < $payments_data['pages']) {
                                    if ($end_page < $payments_data['pages'] - 1) {
                                        echo '<li class="page-item disabled"><a class="page-link" href="#">...</a></li>';
                                    }
                                    $last_link = add_query_arg(array_merge($query_params, array('paged' => $payments_data['pages'])), $current_url);
                                    echo '<li class="page-item"><a class="page-link" href="' . esc_url($last_link) . '">' . $payments_data['pages'] . '</a></li>';
                                }
                                
                                // Next page
                                if ($payments_data['current_page'] < $payments_data['pages']) {
                                    $next_page = $payments_data['current_page'] + 1;
                                    $next_link = add_query_arg(array_merge($query_params, array('paged' => $next_page)), $current_url);
                                    echo '<li class="page-item"><a class="page-link" href="' . esc_url($next_link) . '">' . __('Next', 'somity-manager') . '</a></li>';
                                } else {
                                    echo '<li class="page-item disabled"><a class="page-link" href="#" tabindex="-1">' . __('Next', 'somity-manager') . '</a></li>';
                                }
                                ?>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Export payments
    $('#export-payments').on('click', function() {
        var status = $('select[name="status"]').val();
        var search = $('input[name="search"]').val();
        var month = $('select[name="month"]').val();
        
        window.location.href = somityAjax.ajaxurl + '?action=export_member_payments&status=' + status + '&search=' + search + '&month=' + month + '&nonce=' + somityAjax.nonce;
    });
});
</script>

<?php get_footer(); ?>