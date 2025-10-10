<?php
/**
 * Template Name: Login
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
                    <h3><?php _e('Login', 'somity-manager'); ?></h3>
                    <p class="mb-0"><?php _e('Welcome back! Please login to your account.', 'somity-manager'); ?></p>
                </div>
                <div class="card-body">
                    <?php
                    // Show login errors
                    if (isset($_GET['login']) && $_GET['login'] == 'failed') {
                        echo '<div class="alert alert-danger">' . __('Invalid username or password.', 'somity-manager') . '</div>';
                    }
                    
                    // Show logout message
                    if (isset($_GET['loggedout']) && $_GET['loggedout'] == 'true') {
                        echo '<div class="alert alert-success">' . __('You have been logged out successfully.', 'somity-manager') . '</div>';
                    }
                    
                    // Show account pending message
                    if (isset($_GET['account']) && $_GET['account'] == 'pending') {
                        echo '<div class="alert alert-warning">' . __('Your account is pending approval. Please wait for administrator approval.', 'somity-manager') . '</div>';
                    }
                    
                    // Show account rejected message
                    if (isset($_GET['account']) && $_GET['account'] == 'rejected') {
                        echo '<div class="alert alert-danger">' . __('Your account has been rejected. Please contact administrator for more information.', 'somity-manager') . '</div>';
                    }
                    ?>
                    
                    <form name="loginform" id="loginform" action="<?php echo esc_url(site_url('wp-login.php', 'login_post')); ?>" method="post">
                        <div class="mb-3">
                            <label for="user_login"><?php _e('Username or Email', 'somity-manager'); ?></label>
                            <input type="text" name="log" id="user_login" class="form-control" value="<?php echo (isset($_POST['log'])) ? esc_attr($_POST['log']) : ''; ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="user_pass"><?php _e('Password', 'somity-manager'); ?></label>
                            <div class="input-group">
                                <input type="password" name="pwd" id="user_pass" class="form-control" required>
                                <button class="btn btn-outline-secondary toggle-password" type="button">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" name="rememberme" id="rememberme" class="form-check-input" value="forever">
                            <label for="rememberme" class="form-check-label"><?php _e('Remember Me', 'somity-manager'); ?></label>
                        </div>
                        
                        <div class="d-grid">
                            <input type="submit" name="wp-submit" id="wp-submit" class="btn btn-primary" value="<?php _e('Login', 'somity-manager'); ?>">
                        </div>
                        
                        <input type="hidden" name="redirect_to" value="<?php echo esc_url(home_url('/login-check/')); ?>">
                    </form>
                    
                    <div class="text-center mt-3">
                        <p><a href="<?php echo esc_url(wp_lostpassword_url()); ?>"><?php _e('Forgot Password?', 'somity-manager'); ?></a></p>
                        <p><?php _e('Don\'t have an account?', 'somity-manager'); ?> <a href="<?php echo esc_url(home_url('/signup/')); ?>"><?php _e('Sign Up', 'somity-manager'); ?></a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>

<script>
(function($) {
    'use strict';
    
    $(document).ready(function() {
        // Toggle password visibility
        $('.toggle-password').on('click', function() {
            const passwordField = $('#user_pass');
            const icon = $(this).find('i');
            
            if (passwordField.attr('type') === 'password') {
                passwordField.attr('type', 'text');
                icon.removeClass('bi-eye').addClass('bi-eye-slash');
            } else {
                passwordField.attr('type', 'password');
                icon.removeClass('bi-eye-slash').addClass('bi-eye');
            }
        });
    });
})(jQuery);
</script>