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
                
                <!-- Add your member management content here -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><?php _e('Members List', 'somity-manager'); ?></h5>
                        <a href="<?php echo esc_url(home_url('/add-member/')); ?>" class="btn btn-sm btn-primary"><i class="bi bi-plus-circle"></i> <?php _e('Add Member', 'somity-manager'); ?></a>
                    </div>
                    <div class="card-body">
                        <p><?php _e('Member management functionality will be implemented here.', 'somity-manager'); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>