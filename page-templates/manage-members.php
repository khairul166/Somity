<?php
/**
 * Template Name: Manage Members
 */

if (!is_user_logged_in() || !current_user_can('administrator')) {
    wp_redirect(home_url());
    exit;
}

get_header();
?>

<!-- Manage Members Content -->
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
                <h2 class="mb-4"><?php _e('Manage Members', 'somity-manager'); ?></h2>
                
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
                            <div class="stats-number"><?php 
                                $pending_members = somity_get_members_paginated(999, 1, 'pending');
                                echo esc_html(count($pending_members['items']));
                            ?></div>
                            <div class="stats-label"><?php _e('Pending Approval', 'somity-manager'); ?></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card">
                            <div class="stats-icon">
                                <i class="bi bi-check-circle-fill"></i>
                            </div>
                            <div class="stats-number"><?php 
                                $approved_members = somity_get_members_paginated(999, 1, 'approved');
                                echo esc_html(count($approved_members['items']));
                            ?></div>
                            <div class="stats-label"><?php _e('Approved Members', 'somity-manager'); ?></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card">
                            <div class="stats-icon">
                                <i class="bi bi-x-circle-fill"></i>
                            </div>
                            <div class="stats-number"><?php 
                                $rejected_members = somity_get_members_paginated(999, 1, 'rejected');
                                echo esc_html(count($rejected_members['items']));
                            ?></div>
                            <div class="stats-label"><?php _e('Rejected Members', 'somity-manager'); ?></div>
                        </div>
                    </div>
                </div>
                
                <!-- Search and Filter -->
                <div class="search-filter">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" id="member-search" class="form-control" placeholder="<?php _e('Search by name or email...', 'somity-manager'); ?>" value="<?php echo isset($_GET['search']) ? esc_attr($_GET['search']) : ''; ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <select id="member-filter" class="form-select">
                                <option value="all" <?php selected(isset($_GET['status']) ? $_GET['status'] : 'all', 'all'); ?>><?php _e('All Statuses', 'somity-manager'); ?></option>
                                <option value="pending" <?php selected(isset($_GET['status']) ? $_GET['status'] : 'all', 'pending'); ?>><?php _e('Pending', 'somity-manager'); ?></option>
                                <option value="approved" <?php selected(isset($_GET['status']) ? $_GET['status'] : 'all', 'approved'); ?>><?php _e('Approved', 'somity-manager'); ?></option>
                                <option value="rejected" <?php selected(isset($_GET['status']) ? $_GET['status'] : 'all', 'rejected'); ?>><?php _e('Rejected', 'somity-manager'); ?></option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button id="filter-btn" class="btn btn-primary w-100"><?php _e('Filter', 'somity-manager'); ?></button>
                        </div>
                    </div>
                </div>
                
                <!-- Members Table -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><?php _e('Members List', 'somity-manager'); ?></h5>
                        <div>
                            <button id="export-members" class="btn btn-sm btn-outline-primary me-2"><i class="bi bi-download"></i> <?php _e('Export', 'somity-manager'); ?></button>
                            <a href="<?php echo esc_url(wp_registration_url()); ?>" class="btn btn-sm btn-primary"><i class="bi bi-plus-circle"></i> <?php _e('Add Member', 'somity-manager'); ?></a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="members-table">
                                <thead>
                                    <tr>
                                        <th><?php _e('Member', 'somity-manager'); ?></th>
                                        <th><?php _e('Email', 'somity-manager'); ?></th>
                                        <th><?php _e('Phone', 'somity-manager'); ?></th>
                                        <th><?php _e('Join Date', 'somity-manager'); ?></th>
                                        <th><?php _e('Status', 'somity-manager'); ?></th>
                                        <th><?php _e('Actions', 'somity-manager'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Get current page number
                                    $paged = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
                                    
                                    // Get filter values
                                    $status = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : 'all';
                                    $search = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';
                                    
                                    // Get paginated members
                                    $members_data = somity_get_members_paginated(10, $paged, $status, $search);
                                    
                                    if ($members_data['items']) {
                                        foreach ($members_data['items'] as $member) {
                                            $member_initials = substr($member->name, 0, 1);
                                            
                                            // Generate a random color for the avatar
                                            $avatar_colors = array('#6c5ce7', '#fd79a8', '#fdcb6e', '#00b894', '#0984e3', '#a29bfe');
                                            $avatar_color = $avatar_colors[array_rand($avatar_colors)];
                                            
                                            // Get join date
                                            $join_date = date('M d, Y', strtotime($member->join_date));
                                            
                                            // Get status icon
                                            $status_icon = '';
                                            switch ($member->status) {
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
                                                            <div class="fw-bold"><?php echo esc_html($member->name); ?></div>
                                                            <div class="small text-muted">CSM-<?php echo date('Y'); ?>-<?php echo str_pad($member->id, 3, '0', STR_PAD_LEFT); ?></div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><?php echo esc_html($member->email); ?></td>
                                                <td><?php echo esc_html($member->phone); ?></td>
                                                <td><?php echo esc_html($join_date); ?></td>
                                                <td><span class="status-badge status-<?php echo esc_attr($member->status); ?>"><?php echo esc_html(ucfirst($member->status)); ?> <?php echo $status_icon; ?></span></td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <?php if ($member->status == 'pending') : ?>
                                                            <button type="button" class="btn btn-sm btn-outline-success approve-member" data-id="<?php echo esc_attr($member->id); ?>" title="<?php _e('Approve', 'somity-manager'); ?>"><i class="bi bi-check-lg"></i></button>
                                                            <button type="button" class="btn btn-sm btn-outline-danger reject-member" data-id="<?php echo esc_attr($member->id); ?>" title="<?php _e('Reject', 'somity-manager'); ?>"><i class="bi bi-x-lg"></i></button>
                                                        <?php endif; ?>
                                                        <a href="<?php echo esc_url(home_url('/member-details/?member_id=' . $member->id)); ?>" class="btn btn-sm btn-outline-primary" title="<?php _e('View Details', 'somity-manager'); ?>"><i class="bi bi-eye"></i></a>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                    } else {
                                        echo '<tr><td colspan="6" class="text-center">' . __('No members found.', 'somity-manager') . '</td></tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-between align-items-center">
                        <div>
                            <?php 
                            $start_record = ($members_data['current_page'] - 1) * 10 + 1;
                            $end_record = min($start_record + 9, $members_data['total']);
                            echo sprintf(
                                __('Showing %d-%d of %d records', 'somity-manager'),
                                $start_record,
                                $end_record,
                                $members_data['total']
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
                                    'per_page' => 1,
                                );
                                
                                // Remove empty parameters
                                foreach ($query_params as $key => $value) {
                                    if ($value === 'all' || $value === '') {
                                        unset($query_params[$key]);
                                    }
                                }
                                
                                // Previous page
                                if ($members_data['current_page'] > 1) {
                                    $prev_page = $members_data['current_page'] - 1;
                                    $prev_link = add_query_arg(array_merge($query_params, array('paged' => $prev_page)));
                                    echo '<li class="page-item"><a class="page-link" href="' . esc_url($prev_link) . '">' . __('Previous', 'somity-manager') . '</a></li>';
                                } else {
                                    echo '<li class="page-item disabled"><a class="page-link" href="#" tabindex="-1">' . __('Previous', 'somity-manager') . '</a></li>';
                                }
                                
                                // Page numbers - only show a limited range
                                $start_page = max(1, $members_data['current_page'] - 2);
                                $end_page = min($members_data['pages'], $members_data['current_page'] + 2);
                                
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
                                    if ($i == $members_data['current_page']) {
                                        echo '<li class="page-item active"><a class="page-link" href="#">' . $i . '</a></li>';
                                    } else {
                                        $page_link = add_query_arg(array_merge($query_params, array('paged' => $i)));
                                        echo '<li class="page-item"><a class="page-link" href="' . esc_url($page_link) . '">' . $i . '</a></li>';
                                    }
                                }
                                
                                // Last page
                                if ($end_page < $members_data['pages']) {
                                    if ($end_page < $members_data['pages'] - 1) {
                                        echo '<li class="page-item disabled"><a class="page-link" href="#">...</a></li>';
                                    }
                                    $last_link = add_query_arg(array_merge($query_params, array('paged' => $members_data['pages'])));
                                    echo '<li class="page-item"><a class="page-link" href="' . esc_url($last_link) . '">' . $members_data['pages'] . '</a></li>';
                                }
                                
                                // Next page
                                if ($members_data['current_page'] < $members_data['pages']) {
                                    $next_page = $members_data['current_page'] + 1;
                                    $next_link = add_query_arg(array_merge($query_params, array('paged' => $next_page)));
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

<!-- <script>
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
        
        // Filter functionality
        $('#filter-btn').on('click', function() {
            var status = $('#member-filter').val();
            var search = $('#member-search').val();
            
            var url = new URL(window.location.href);
            url.searchParams.set('status', status);
            url.searchParams.set('search', search);
            url.searchParams.set('paged', '1');
            
            window.location.href = url.toString();
        });
        
        // Export members
        $('#export-members').on('click', function() {
            var status = $('#member-filter').val();
            var search = $('#member-search').val();
            
            window.location.href = somityAjax.ajaxurl + '?action=export_members&status=' + status + '&search=' + search + '&nonce=' + somityAjax.nonce;
        });
    });
})(jQuery);
</script> -->
