<?php
/**
 * Sidebar template for the dashboard
 */

?>
<div class="dashboard-sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <?php if (has_custom_logo()) : ?>
                <?php the_custom_logo(); ?>
            <?php else : ?>
                <a href="<?php echo esc_url(home_url('/')); ?>" class="site-title">
                    <?php bloginfo('name'); ?>
                </a>
            <?php endif; ?>
        </div>
        <div class="sidebar-toggle">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
    
    <nav class="sidebar-nav">
        <ul>
            <?php if (current_user_can('administrator')) : ?>
                <li class="<?php echo is_page('admin-dashboard') ? 'active' : ''; ?>">
                    <a href="<?php echo esc_url(home_url('/admin-dashboard/')); ?>">
                        <span class="nav-icon">📊</span>
                        <span class="nav-text"><?php _e('Dashboard', 'somity-manager'); ?></span>
                    </a>
                </li>
                <li class="<?php echo is_page('members') ? 'active' : ''; ?>">
                    <a href="<?php echo esc_url(home_url('/members/')); ?>">
                        <span class="nav-icon">👥</span>
                        <span class="nav-text"><?php _e('Members', 'somity-manager'); ?></span>
                    </a>
                </li>
                <li class="<?php echo is_page('payments') ? 'active' : ''; ?>">
                    <a href="<?php echo esc_url(home_url('/payments/')); ?>">
                        <span class="nav-icon">💰</span>
                        <span class="nav-text"><?php _e('Payments', 'somity-manager'); ?></span>
                    </a>
                </li>
                <li class="<?php echo is_page('reports') ? 'active' : ''; ?>">
                    <a href="<?php echo esc_url(home_url('/reports/')); ?>">
                        <span class="nav-icon">📈</span>
                        <span class="nav-text"><?php _e('Reports', 'somity-manager'); ?></span>
                    </a>
                </li>
            <?php else : ?>
                <li class="<?php echo is_page('member-dashboard') ? 'active' : ''; ?>">
                    <a href="<?php echo esc_url(home_url('/member-dashboard/')); ?>">
                        <span class="nav-icon">🏠</span>
                        <span class="nav-text"><?php _e('Dashboard', 'somity-manager'); ?></span>
                    </a>
                </li>
                <li class="<?php echo is_page('my-payments') ? 'active' : ''; ?>">
                    <a href="<?php echo esc_url(home_url('/my-payments/')); ?>">
                        <span class="nav-icon">💰</span>
                        <span class="nav-text"><?php _e('My Payments', 'somity-manager'); ?></span>
                    </a>
                </li>
                <li class="<?php echo is_page('submit-payment') ? 'active' : ''; ?>">
                    <a href="<?php echo esc_url(home_url('/submit-payment/')); ?>">
                        <span class="nav-icon">📤</span>
                        <span class="nav-text"><?php _e('Submit Payment', 'somity-manager'); ?></span>
                    </a>
                </li>
            <?php endif; ?>
            <li class="<?php echo is_page('contact') ? 'active' : ''; ?>">
                <a href="<?php echo esc_url(home_url('/contact/')); ?>">
                    <span class="nav-icon">📞</span>
                    <span class="nav-text"><?php _e('Contact', 'somity-manager'); ?></span>
                </a>
            </li>
        </ul>
    </nav>
    
    <div class="sidebar-footer">
        <a href="<?php echo wp_logout_url(home_url()); ?>" class="logout-btn">
            <span class="nav-icon">🚪</span>
            <span class="nav-text"><?php _e('Logout', 'somity-manager'); ?></span>
        </a>
    </div>
</div>