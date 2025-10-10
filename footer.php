<?php
/**
 * Footer template for the theme
 */

?>
    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><?php bloginfo('name'); ?></h5>
                    <p><?php _e('Together We Grow Stronger', 'somity-manager'); ?></p>
                </div>
                <div class="col-md-3">
                    <h5><?php _e('Quick Links', 'somity-manager'); ?></h5>
                    <ul class="list-unstyled">
                        <li><a href="<?php echo esc_url(home_url('/')); ?>" class="text-white text-decoration-none"><?php _e('Home', 'somity-manager'); ?></a></li>
                        <li><a href="<?php echo esc_url(home_url('/member-dashboard/')); ?>" class="text-white text-decoration-none"><?php _e('Member Dashboard', 'somity-manager'); ?></a></li>
                        <li><a href="<?php echo esc_url(home_url('/admin-dashboard/')); ?>" class="text-white text-decoration-none"><?php _e('Admin Dashboard', 'somity-manager'); ?></a></li>
                        <li><a href="<?php echo esc_url(home_url('/contact/')); ?>" class="text-white text-decoration-none"><?php _e('Contact', 'somity-manager'); ?></a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5><?php _e('Contact Us', 'somity-manager'); ?></h5>
                    <ul class="list-unstyled">
                        <li><i class="bi bi-envelope me-2"></i> <?php echo antispambot(get_option('admin_email')); ?></li>
                        <li><i class="bi bi-telephone me-2"></i> <?php echo esc_html(get_option('somity_phone', '+1 (555) 123-4567')); ?></li>
                        <li><i class="bi bi-geo-alt me-2"></i> <?php echo esc_html(get_option('somity_address', '123 Savings St, Finance City')); ?></li>
                    </ul>
                </div>
            </div>
            <hr class="my-4 bg-white">
            <div class="text-center">
                <p class="mb-0">&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. <?php _e('All Rights Reserved.', 'somity-manager'); ?></p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script>
        // Logout functionality
        document.getElementById('logoutLink')?.addEventListener('click', function(e) {
            e.preventDefault();
            if (confirm('<?php _e('Are you sure you want to logout?', 'somity-manager'); ?>')) {
                window.location.href = this.href;
            }
        });
        
        document.getElementById('sidebarLogout')?.addEventListener('click', function(e) {
            e.preventDefault();
            if (confirm('<?php _e('Are you sure you want to logout?', 'somity-manager'); ?>')) {
                window.location.href = this.href;
            }
        });
    </script>
    
    <?php wp_footer(); ?>
</body>
</html>