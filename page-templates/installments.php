<?php
/**
 * Template Name: Installments
 */

if (!is_user_logged_in() || !current_user_can('administrator')) {
    wp_redirect(home_url());
    exit;
}

get_header();
?>

<!-- Installments Content -->
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
                    <li><a href="<?php echo esc_url(home_url('/installments/')); ?>" class="active"><i class="bi bi-calendar-check"></i> <?php _e('Installments', 'somity-manager'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/reports/')); ?>"><i class="bi bi-graph-up"></i> <?php _e('Reports', 'somity-manager'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/settings/')); ?>"><i class="bi bi-gear"></i> <?php _e('Settings', 'somity-manager'); ?></a></li>
                    <li><a href="<?php echo wp_logout_url(); ?>" id="sidebarLogout"><i class="bi bi-box-arrow-right"></i> <?php _e('Logout', 'somity-manager'); ?></a></li>
                </ul>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-lg-9">
            <div class="dashboard-content">
                <h2 class="mb-4"><?php _e('Installments Management', 'somity-manager'); ?></h2>
                
                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="stats-card">
                            <div class="stats-icon">
                                <i class="bi bi-clock-history"></i>
                            </div>
                            <div class="stats-number"><?php echo esc_html(somity_get_total_pending_installments()); ?></div>
                            <div class="stats-label"><?php _e('Pending Installments', 'somity-manager'); ?></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stats-card">
                            <div class="stats-icon">
                                <i class="bi bi-check-circle-fill"></i>
                            </div>
                            <div class="stats-number"><?php echo esc_html(somity_get_total_paid_installments()); ?></div>
                            <div class="stats-label"><?php _e('Paid Installments', 'somity-manager'); ?></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stats-card">
                            <div class="stats-icon">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                            </div>
                            <div class="stats-number"><?php echo esc_html(somity_get_total_overdue_installments()); ?></div>
                            <div class="stats-label"><?php _e('Overdue Installments', 'somity-manager'); ?></div>
                        </div>
                    </div>
                </div>
                
                <!-- Search and Filter -->
                <div class="search-filter mb-4">
                    <form id="installments-filter-form" method="get">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                                    <input type="text" name="search" class="form-control" placeholder="<?php _e('Search by member name...', 'somity-manager'); ?>" value="<?php echo isset($_GET['search']) ? esc_attr($_GET['search']) : ''; ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select name="status" class="form-select">
                                    <option value="all" <?php selected(isset($_GET['status']) ? $_GET['status'] : 'all', 'all'); ?>><?php _e('All Statuses', 'somity-manager'); ?></option>
                                    <option value="pending" <?php selected(isset($_GET['status']) ? $_GET['status'] : 'all', 'pending'); ?>><?php _e('Pending', 'somity-manager'); ?></option>
                                    <option value="paid" <?php selected(isset($_GET['status']) ? $_GET['status'] : 'all', 'paid'); ?>><?php _e('Paid', 'somity-manager'); ?></option>
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
                
                <!-- Installments Table -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><?php _e('Installments List', 'somity-manager'); ?></h5>
                        <div>
                            <button id="generate-installments" class="btn btn-sm btn-outline-primary me-2"><i class="bi bi-calendar-plus"></i> <?php _e('Generate Installments', 'somity-manager'); ?></button>
                            <button id="export-installments" class="btn btn-sm btn-outline-primary"><i class="bi bi-download"></i> <?php _e('Export', 'somity-manager'); ?></button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="installments-table">
                                <thead>
                                    <tr>
                                        <th><?php _e('Member', 'somity-manager'); ?></th>
                                        <th><?php _e('Amount', 'somity-manager'); ?></th>
                                        <th><?php _e('Due Date', 'somity-manager'); ?></th>
                                        <th><?php _e('Status', 'somity-manager'); ?></th>
                                        <th><?php _e('Actions', 'somity-manager'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Get current page number from query string
                                    $paged = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
                                    
                                    // Get per page value
                                    $per_page = isset($_GET['per_page']) ? intval($_GET['per_page']) : 10;
                                    
                                    // Get filter values
                                    $status = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : 'all';
                                    $search = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';
                                    $month = isset($_GET['month']) ? sanitize_text_field($_GET['month']) : 'all';
                                    
                                    // Get paginated installments
                                    $installments_data = somity_get_installments_paginated($per_page, $paged, $status, $search, $month);
                                    
                                    
                                    if ($installments_data['items']) {
                                        foreach ($installments_data['items'] as $installment) {
                                            // Get status icon
                                            $status_icon = '';
                                            switch ($installment->status) {
                                                case 'pending':
                                                    $status_icon = '<i class="bi bi-clock-fill"></i>';
                                                    break;
                                                case 'paid':
                                                    $status_icon = '<i class="bi bi-check-circle-fill"></i>';
                                                    break;
                                            }
                                            
                                            // Check if installment is overdue
                                            $is_overdue = ($installment->status === 'pending' && strtotime($installment->due_date) < time());
                                            ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="user-avatar me-2" style="width: 40px; height: 40px; font-size: 1rem; background-color: #6c5ce7;">
                                                            <?php echo esc_html(substr($installment->member_name, 0, 1)); ?>
                                                        </div>
                                                        <div>
                                                            <div class="fw-bold"><?php echo esc_html($installment->member_name); ?></div>
                                                            <div class="small text-muted">CSM-<?php echo date('Y'); ?>-<?php echo str_pad($installment->member_id, 3, '0', STR_PAD_LEFT); ?></div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>$<?php echo number_format($installment->amount, 2); ?></td>
                                                <td><?php echo date_i18n(get_option('date_format'), strtotime($installment->due_date)); ?></td>
                                                <td>
                                                    <span class="status-badge status-<?php echo esc_attr($installment->status); ?> <?php echo $is_overdue ? 'overdue' : ''; ?>">
                                                        <?php echo esc_html(ucfirst($installment->status)); ?> <?php echo $status_icon; ?>
                                                        <?php if ($is_overdue) : ?>
                                                            <i class="bi bi-exclamation-triangle-fill"></i> <?php _e('Overdue', 'somity-manager'); ?>
                                                        <?php endif; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <?php if ($installment->status == 'pending') : ?>
                                                            <button type="button" class="btn btn-sm btn-outline-success mark-as-paid" data-id="<?php echo esc_attr($installment->id); ?>" title="<?php _e('Mark as Paid', 'somity-manager'); ?>">
                                                                <i class="bi bi-check-lg"></i>
                                                            </button>
                                                        <?php endif; ?>
                                                        <a href="<?php echo esc_url(home_url('/installment-details/?installment_id=' . $installment->id)); ?>" class="btn btn-sm btn-outline-primary" title="<?php _e('View Details', 'somity-manager'); ?>">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                    } else {
                                        echo '<tr><td colspan="5" class="text-center">' . __('No installments found.', 'somity-manager') . '</td></tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-between align-items-center">
                        <div>
                            <?php 
                            $start_record = ($installments_data['current_page'] - 1) * $per_page + 1;
                            $end_record = min($start_record + $per_page - 1, $installments_data['total']);
                            echo sprintf(
                                __('Showing %d-%d of %d records', 'somity-manager'),
                                $start_record,
                                $end_record,
                                $installments_data['total']
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
                                    'month' => $month,
                                    'per_page' => $per_page
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
                                if ($installments_data['current_page'] > 1) {
                                    $prev_page = $installments_data['current_page'] - 1;
                                    $prev_link = add_query_arg(array_merge($query_params, array('paged' => $prev_page)), $current_url);
                                    echo '<li class="page-item"><a class="page-link" href="' . esc_url($prev_link) . '">' . __('Previous', 'somity-manager') . '</a></li>';
                                } else {
                                    echo '<li class="page-item disabled"><a class="page-link" href="#" tabindex="-1">' . __('Previous', 'somity-manager') . '</a></li>';
                                }
                                
                                // Page numbers - only show a limited range
                                $start_page = max(1, $installments_data['current_page'] - 2);
                                $end_page = min($installments_data['pages'], $installments_data['current_page'] + 2);
                                
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
                                    if ($i == $installments_data['current_page']) {
                                        echo '<li class="page-item active"><a class="page-link" href="#">' . $i . '</a></li>';
                                    } else {
                                        $page_link = add_query_arg(array_merge($query_params, array('paged' => $i)), $current_url);
                                        echo '<li class="page-item"><a class="page-link" href="' . esc_url($page_link) . '">' . $i . '</a></li>';
                                    }
                                }
                                
                                // Last page
                                if ($end_page < $installments_data['pages']) {
                                    if ($end_page < $installments_data['pages'] - 1) {
                                        echo '<li class="page-item disabled"><a class="page-link" href="#">...</a></li>';
                                    }
                                    $last_link = add_query_arg(array_merge($query_params, array('paged' => $installments_data['pages'])), $current_url);
                                    echo '<li class="page-item"><a class="page-link" href="' . esc_url($last_link) . '">' . $installments_data['pages'] . '</a></li>';
                                }
                                
                                // Next page
                                if ($installments_data['current_page'] < $installments_data['pages']) {
                                    $next_page = $installments_data['current_page'] + 1;
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

<!-- Generate Installments Modal -->
<div class="modal fade" id="generateInstallmentsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php _e('Generate Installments', 'somity-manager'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="generate-installments-form">
                    <div class="mb-3">
                        <label for="member-select" class="form-label"><?php _e('Select Member', 'somity-manager'); ?></label>
                        <select class="form-select" id="member-select" required>
                            <option value=""><?php _e('Select a member', 'somity-manager'); ?></option>
                            <?php
                            $members = get_users(array('role' => 'subscriber'));
                            foreach ($members as $member) {
                                echo '<option value="' . esc_attr($member->ID) . '">' . esc_html($member->display_name) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="installment-amount" class="form-label"><?php _e('Installment Amount', 'somity-manager'); ?></label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control" id="installment-amount" min="0" step="0.01" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="installment-year" class="form-label"><?php _e('Year', 'somity-manager'); ?></label>
                        <select class="form-select" id="installment-year" required>
                            <?php
                            $current_year = date('Y');
                            for ($year = $current_year; $year <= $current_year + 1; $year++) {
                                echo '<option value="' . $year . '" ' . selected($year, $current_year, false) . '>' . $year . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="generate-for-all">
                            <label class="form-check-label" for="generate-for-all">
                                <?php _e('Generate for all approved members', 'somity-manager'); ?>
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php _e('Cancel', 'somity-manager'); ?></button>
                <button type="button" class="btn btn-primary" id="confirm-generate"><?php _e('Generate', 'somity-manager'); ?></button>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>