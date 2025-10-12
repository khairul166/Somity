<?php
/**
 * Template Name: Installment Status
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

<!-- Installment Status Content -->
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
                    <li><a href="<?php echo esc_url(home_url('/payment-history/')); ?>"><i class="bi bi-clock-history"></i> <?php _e('Payment History', 'somity-manager'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/installment-status/')); ?>" class="active"><i class="bi bi-calendar-check"></i> <?php _e('Installment Status', 'somity-manager'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/profile/')); ?>"><i class="bi bi-person-circle"></i> <?php _e('My Profile', 'somity-manager'); ?></a></li>
                    <li><a href="<?php echo wp_logout_url(); ?>" id="sidebarLogout"><i class="bi bi-box-arrow-right"></i> <?php _e('Logout', 'somity-manager'); ?></a></li>
                </ul>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-lg-9">
            <div class="dashboard-content">
                <h2 class="mb-4"><?php _e('Installment Status', 'somity-manager'); ?></h2>
                
                <!-- Installment Summary -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="stats-card">
                            <div class="stats-icon">
                                <i class="bi bi-calendar-check"></i>
                            </div>
                            <div class="stats-number"><?php echo somity_get_member_total_installments($member_id); ?></div>
                            <div class="stats-label"><?php _e('Total Installments', 'somity-manager'); ?></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card">
                            <div class="stats-icon">
                                <i class="bi bi-check-circle-fill"></i>
                            </div>
                            <div class="stats-number"><?php echo somity_get_member_paid_installments($member_id); ?></div>
                            <div class="stats-label"><?php _e('Paid Installments', 'somity-manager'); ?></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card">
                            <div class="stats-icon">
                                <i class="bi bi-clock-fill"></i>
                            </div>
                            <div class="stats-number"><?php echo somity_get_member_pending_installments($member_id); ?></div>
                            <div class="stats-label"><?php _e('Pending Installments', 'somity-manager'); ?></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card">
                            <div class="stats-icon">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                            </div>
                            <div class="stats-number"><?php echo somity_get_member_overdue_installments($member_id); ?></div>
                            <div class="stats-label"><?php _e('Overdue Installments', 'somity-manager'); ?></div>
                        </div>
                    </div>
                </div>
                
                <!-- Outstanding Balance -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><?php _e('Outstanding Balance', 'somity-manager'); ?></h5>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h3 class="mb-0">$<?php echo number_format(somity_get_member_outstanding_balance($member_id), 2); ?></h3>
                                <p class="text-muted mb-0"><?php _e('Total amount due for pending installments', 'somity-manager'); ?></p>
                            </div>
                            <div class="col-md-6 text-end">
                                <a href="<?php echo esc_url(home_url('/submit-payment/')); ?>" class="btn btn-primary">
                                    <i class="bi bi-cash-stack"></i> <?php _e('Pay Now', 'somity-manager'); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Search and Filter -->
                <div class="search-filter mb-4">
                    <form id="installments-filter-form" method="get" action="<?php echo esc_url(get_permalink()); ?>">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                                    <input type="text" name="search" class="form-control" placeholder="<?php _e('Search by month...', 'somity-manager'); ?>" value="<?php echo isset($_GET['search']) ? esc_attr($_GET['search']) : ''; ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select name="status" class="form-select">
                                    <option value="all" <?php selected(isset($_GET['status']) ? $_GET['status'] : 'all', 'all'); ?>><?php _e('All Statuses', 'somity-manager'); ?></option>
                                    <option value="paid" <?php selected(isset($_GET['status']) ? $_GET['status'] : 'all', 'paid'); ?>><?php _e('Paid', 'somity-manager'); ?></option>
                                    <option value="pending" <?php selected(isset($_GET['status']) ? $_GET['status'] : 'all', 'pending'); ?>><?php _e('Pending', 'somity-manager'); ?></option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="year" class="form-select">
                                    <option value="all" <?php selected(isset($_GET['year']) ? $_GET['year'] : 'all', 'all'); ?>><?php _e('All Years', 'somity-manager'); ?></option>
                                    <?php
                                    // Generate year options
                                    $current_year = date('Y');
                                    for ($year = $current_year; $year >= $current_year - 5; $year--) {
                                        echo '<option value="' . $year . '" ' . selected(isset($_GET['year']) ? $_GET['year'] : 'all', $year, false) . '>' . $year . '</option>';
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
                        <h5 class="mb-0"><?php _e('Installments', 'somity-manager'); ?></h5>
                        <div>
                            <button id="export-installments" class="btn btn-sm btn-outline-primary"><i class="bi bi-download"></i> <?php _e('Export', 'somity-manager'); ?></button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="installments-table">
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
                                    // Get current page number
                                    $paged = max(1, get_query_var('paged') ? get_query_var('paged') : (isset($_GET['paged']) ? intval($_GET['paged']) : 1));
                                    
                                    // Get filter values
                                    $status = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : 'all';
                                    $search = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';
                                    $year = isset($_GET['year']) ? sanitize_text_field($_GET['year']) : 'all';
                                    
                                    // Get paginated installments
                                    $installments_data = somity_get_member_installments_paginated($member_id, 10, $paged, $status, $search, $year);
                                    
                                    if ($installments_data['items']) {
                                        foreach ($installments_data['items'] as $installment) {
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
                                        echo '<tr><td colspan="4" class="text-center">' . __('No installments found.', 'somity-manager') . '</td></tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-between align-items-center">
                        <div>
                            <?php 
                            $start_record = ($installments_data['current_page'] - 1) * 10 + 1;
                            $end_record = min($start_record + 9, $installments_data['total']);
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
                                    'year' => $year
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

<script>
jQuery(document).ready(function($) {
    // Export installments
    $('#export-installments').on('click', function() {
        var status = $('select[name="status"]').val();
        var search = $('input[name="search"]').val();
        var year = $('select[name="year"]').val();
        
        window.location.href = somityAjax.ajaxurl + '?action=export_member_installments&status=' + status + '&search=' + search + '&year=' + year + '&nonce=' + somityAjax.nonce;
    });
});
</script>

<?php get_footer(); ?>