<?php
/**
 * Template Name: Admin Dashboard
 */

if (!is_user_logged_in() || !current_user_can('administrator')) {
    wp_redirect(home_url());
    exit;
}

get_header();
?>

<!-- Admin Dashboard Content -->
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
                    <li><a href="<?php echo esc_url(home_url('/admin-dashboard/')); ?>" class="active"><i class="bi bi-speedometer2"></i> <?php _e('Dashboard', 'somity-manager'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/manage-members/')); ?>"><i class="bi bi-people"></i> <?php _e('Manage Members', 'somity-manager'); ?></a></li>
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
                <h2 class="mb-4"><?php _e('Admin Dashboard', 'somity-manager'); ?></h2>
                
                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="stats-card">
                            <div class="stats-icon">
                                <i class="bi bi-people-fill"></i>
                            </div>
                            <div class="stats-number"><?php echo esc_html(somity_get_total_members()); ?></div>
                            <div class="stats-label"><?php _e('Total Members', 'somity-manager'); ?></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card">
                            <div class="stats-icon">
                                <i class="bi bi-clock-history"></i>
                            </div>
                            <div class="stats-number"><?php echo esc_html(somity_get_pending_payments_count()); ?></div>
                            <div class="stats-label"><?php _e('Pending Payments', 'somity-manager'); ?></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card">
                            <div class="stats-icon">
                                <i class="bi bi-check-circle-fill"></i>
                            </div>
                            <div class="stats-number"><?php echo esc_html(somity_get_approved_payments_count()); ?></div>
                            <div class="stats-label"><?php _e('Approved Payments', 'somity-manager'); ?></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card">
                            <div class="stats-icon">
                                <i class="bi bi-x-circle-fill"></i>
                            </div>
                            <div class="stats-number"><?php echo esc_html(somity_get_rejected_payments_count()); ?></div>
                            <div class="stats-label"><?php _e('Rejected Payments', 'somity-manager'); ?></div>
                        </div>
                    </div>
                </div>
                
                <!-- Search and Filter -->
                <div class="search-filter">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" id="payment-search" class="form-control" placeholder="<?php _e('Search by name or ID...', 'somity-manager'); ?>" value="<?php echo isset($_GET['search']) ? esc_attr($_GET['search']) : ''; ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select id="payment-filter" class="form-select">
                                <option value="all" <?php selected(isset($_GET['status']) ? $_GET['status'] : 'all', 'all'); ?>><?php _e('All Statuses', 'somity-manager'); ?></option>
                                <option value="pending" <?php selected(isset($_GET['status']) ? $_GET['status'] : 'all', 'pending'); ?>><?php _e('Pending', 'somity-manager'); ?></option>
                                <option value="approved" <?php selected(isset($_GET['status']) ? $_GET['status'] : 'all', 'approved'); ?>><?php _e('Approved', 'somity-manager'); ?></option>
                                <option value="rejected" <?php selected(isset($_GET['status']) ? $_GET['status'] : 'all', 'rejected'); ?>><?php _e('Rejected', 'somity-manager'); ?></option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select id="month-filter" class="form-select">
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
                            <button id="filter-btn" class="btn btn-primary w-100"><?php _e('Filter', 'somity-manager'); ?></button>
                        </div>
                    </div>
                </div>
                
                <!-- Payments Table -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><?php _e('Recent Payments', 'somity-manager'); ?></h5>
                        <div>
                            <button id="export-payments" class="btn btn-sm btn-outline-primary me-2"><i class="bi bi-download"></i> <?php _e('Export', 'somity-manager'); ?></button>
                            <a href="<?php echo esc_url(home_url('/submit-payment/')); ?>" class="btn btn-sm btn-primary"><i class="bi bi-plus-circle"></i> <?php _e('Add Payment', 'somity-manager'); ?></a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="payments-table">
                                <thead>
                                    <tr>
                                        <th><?php _e('Member', 'somity-manager'); ?></th>
                                        <th><?php _e('Month', 'somity-manager'); ?></th>
                                        <th><?php _e('Amount', 'somity-manager'); ?></th>
                                        <th><?php _e('Date', 'somity-manager'); ?></th>
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
                                    $payments_data = somity_get_recent_payments_paginated(10, $paged, $status, $search, $month);
                                    
                                    if ($payments_data['items']) {
                                        foreach ($payments_data['items'] as $payment) {
                                            $member = get_user_by('id', $payment->member_id);
                                            $member_initials = substr($member->first_name, 0, 1) . substr($member->last_name, 0, 1);
                                            
                                            // Generate a random color for the avatar
                                            $avatar_colors = array('#6c5ce7', '#fd79a8', '#fdcb6e', '#00b894', '#0984e3', '#a29bfe');
                                            $avatar_color = $avatar_colors[array_rand($avatar_colors)];
                                            
                                            // Get payment date
                                            $payment_date = date('M d, Y', strtotime($payment->date));
                                            
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
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="user-avatar me-2" style="width: 40px; height: 40px; font-size: 1rem; background-color: <?php echo esc_attr($avatar_color); ?>;">
                                                            <?php echo esc_html($member_initials); ?>
                                                        </div>
                                                        <div>
                                                            <div class="fw-bold"><?php echo esc_html($member->display_name); ?></div>
                                                            <div class="small text-muted">CSM-<?php echo date('Y'); ?>-<?php echo str_pad($member->ID, 3, '0', STR_PAD_LEFT); ?></div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><?php echo esc_html(date('F Y', strtotime($payment->date))); ?></td>
                                                <td>$<?php echo esc_html(number_format($payment->amount, 2)); ?></td>
                                                <td><?php echo esc_html($payment_date); ?></td>
                                                <td><span class="status-badge status-<?php echo esc_attr($payment->status); ?>"><?php echo esc_html(ucfirst($payment->status)); ?> <?php echo $status_icon; ?></span></td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <?php if ($payment->status == 'pending') : ?>
                                                            <button type="button" class="btn btn-sm btn-outline-success approve-payment" data-id="<?php echo esc_attr($payment->id); ?>" title="<?php _e('Approve', 'somity-manager'); ?>"><i class="bi bi-check-lg"></i></button>
                                                            <button type="button" class="btn btn-sm btn-outline-danger reject-payment" data-id="<?php echo esc_attr($payment->id); ?>" title="<?php _e('Reject', 'somity-manager'); ?>"><i class="bi bi-x-lg"></i></button>
                                                        <?php endif; ?>
                                                        <a href="<?php echo esc_url(home_url('/payment-details/?payment_id=' . $payment->id)); ?>" class="btn btn-sm btn-outline-primary" title="<?php _e('View', 'somity-manager'); ?>"><i class="bi bi-eye"></i></a>
                                                    </div>
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
                                
                                // Previous page
                                if ($payments_data['current_page'] > 1) {
                                    $prev_page = $payments_data['current_page'] - 1;
                                    $prev_link = add_query_arg(array_merge($query_params, array('paged' => $prev_page)));
                                    echo '<li class="page-item"><a class="page-link" href="' . esc_url($prev_link) . '">' . __('Previous', 'somity-manager') . '</a></li>';
                                } else {
                                    echo '<li class="page-item disabled"><a class="page-link" href="#" tabindex="-1">' . __('Previous', 'somity-manager') . '</a></li>';
                                }
                                
                                // Page numbers - only show a limited range
                                $start_page = max(1, $payments_data['current_page'] - 2);
                                $end_page = min($payments_data['pages'], $payments_data['current_page'] + 2);
                                
                                // First page
                                if ($start_page > 1) {
                                    $first_link = add_query_arg(array_merge($query_params, array('paged' => 1)));
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
                                        $page_link = add_query_arg(
    array_merge($query_params, array('paged' => $i)),
    get_permalink() // ensures correct base page
);

                                        echo '<li class="page-item"><a class="page-link" href="' . esc_url($page_link) . '">' . $i . '</a></li>';
                                    }
                                }
                                
                                // Last page
                                if ($end_page < $payments_data['pages']) {
                                    if ($end_page < $payments_data['pages'] - 1) {
                                        echo '<li class="page-item disabled"><a class="page-link" href="#">...</a></li>';
                                    }
                                    $last_link = add_query_arg(array_merge($query_params, array('paged' => $payments_data['pages'])));
                                    echo '<li class="page-item"><a class="page-link" href="' . esc_url($last_link) . '">' . $payments_data['pages'] . '</a></li>';
                                }
                                
                                // Next page
                                if ($payments_data['current_page'] < $payments_data['pages']) {
                                    $next_page = $payments_data['current_page'] + 1;
                                    $next_link = add_query_arg(
    array_merge($query_params, array('paged' => $next_page)),
    get_permalink()
);
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

<?php get_footer(); ?>