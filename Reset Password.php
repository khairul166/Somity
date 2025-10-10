<?php
/**
 * Template Name: Reset Password
 */

// Redirect if user is already logged in
if (is_user_logged_in()) {
    $user = wp_get_current_user();
    if (in_array('administrator', $user->roles)) {
        wp_redirect(home_url('/admin-dashboard/'));
    } else {
        wp_redirect(home_url('/member-dashboard/'));
    }
    exit;
}

get_header();
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card">
                <div class="card-header text-center">
                    <h3><?php _e('Reset Password', 'somity-manager'); ?></h3>
                    <p class="mb-0"><?php _e('Enter your username or email to reset your password.', 'somity-manager'); ?></p>
                </div>
                <div class="card-body">
                    <?php
                    // Show messages
                    if (isset($_GET['checkemail']) && $_GET['checkemail'] == 'confirm') {
                        echo '<div class="alert alert-success">' . __('Password reset email has been sent. Please check your email.', 'somity-manager') . '</div>';
                    }
                    
                    if (isset($_GET['error']) && $_GET['error'] == 'invalidkey') {
                        echo '<div class="alert alert-danger">' . __('Invalid password reset key.', 'somity-manager') . '</div>';
                    }
                    
                    if (isset($_GET['error']) && $_GET['error'] == 'expiredkey') {
                        echo '<div class="alert alert-danger">' . __('Password reset key has expired.', 'somity-manager') . '</div>';
                    }
                    
                    // Check if we're resetting the password
                    $rp_key = isset($_REQUEST['key']) ? sanitize_text_field($_REQUEST['key']) : '';
                    $rp_login = isset($_REQUEST['login']) ? sanitize_text_field($_REQUEST['login']) : '';
                    
                    if ($rp_key && $rp_login) {
                        // Verify key
                        $user = check_password_reset_key($rp_key, $rp_login);
                        
                        if (is_wp_error($user)) {
                            if ($user->get_error_code() === 'invalid_key') {
                                wp_redirect(home_url('/reset-password/?error=invalidkey'));
                                exit;
                            } elseif ($user->get_error_code() === 'expired_key') {
                                wp_redirect(home_url('/reset-password/?error=expiredkey'));
                                exit;
                            }
                        }
                        
                        // Show password reset form
                        ?>
                        <form name="resetpassform" id="resetpassform" action="<?php echo esc_url(site_url('wp-login.php?action=resetpass', 'login_post')); ?>" method="post" autocomplete="off">
                            <input type="hidden" id="user_login" name="rp_login" value="<?php echo esc_attr($rp_login); ?>" autocomplete="off" />
                            <input type="hidden" name="rp_key" value="<?php echo esc_attr($rp_key); ?>" />
                            
                            <div class="mb-3">
                                <label for="pass1"><?php _e('New Password', 'somity-manager'); ?></label>
                                <input type="password" name="pass1" id="pass1" class="form-control" size="20" required />
                                <div class="form-text"><?php _e('Hint: The password should be at least 8 characters long.', 'somity-manager'); ?></div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="pass2"><?php _e('Confirm New Password', 'somity-manager'); ?></label>
                                <input type="password" name="pass2" id="pass2" class="form-control" size="20" required />
                            </div>
                            
                            <div class="d-grid">
                                <input type="submit" name="wp-submit" id="wp-submit" class="btn btn-primary" value="<?php _e('Reset Password', 'somity-manager'); ?>" />
                            </div>
                        </form>
                        <?php
                    } else {
                        // Show request reset form
                        ?>
                        <form name="lostpasswordform" id="lostpasswordform" action="<?php echo esc_url(site_url('wp-login.php?action=lostpassword', 'login_post')); ?>" method="post">
                            <div class="mb-3">
                                <label for="user_login"><?php _e('Username or Email', 'somity-manager'); ?></label>
                                <input type="text" name="user_login" id="user_login" class="form-control" required>
                            </div>
                            
                            <div class="d-grid">
                                <input type="submit" name="wp-submit" id="wp-submit" class="btn btn-primary" value="<?php _e('Get New Password', 'somity-manager'); ?>">
                            </div>
                        </form>
                        
                        <div class="text-center mt-3">
                            <p><?php _e('Remember your password?', 'somity-manager'); ?> <a href="<?php echo esc_url(home_url('/login/')); ?>"><?php _e('Login', 'somity-manager'); ?></a></p>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>