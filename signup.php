<?php
/**
 * Template Name: Signup
 */

// Redirect if user is already logged in
if (is_user_logged_in()) {
    wp_redirect(home_url('/member-dashboard/'));
    exit;
}

get_header();
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header text-center">
                    <h3><?php _e('Create an Account', 'somity-manager'); ?></h3>
                    <p class="mb-0"><?php _e('Join our savings program today', 'somity-manager'); ?></p>
                </div>
                <div class="card-body">
                    <?php
                    // Show success/error messages
                    if (isset($_GET['registered']) && $_GET['registered'] == 'success') {
                        echo '<div class="alert alert-success">' . __('Registration successful! Your account is pending approval. You will be notified once your account is approved.', 'somity-manager') . '</div>';
                    }
                    
                    // Show registration errors from global variable
                    global $registration_errors;
                    if (isset($registration_errors) && is_wp_error($registration_errors)) {
                        echo '<div class="alert alert-danger">';
                        foreach ($registration_errors->get_error_messages() as $error) {
                            echo '<p>' . $error . '</p>';
                        }
                        echo '</div>';
                    }
                    ?>
                    
                    <form name="registerform" id="registerform" action="<?php echo esc_url(site_url('wp-login.php?action=register', 'login_post')); ?>" method="post">
                        <div class="mb-3">
                            <label for="first_name"><?php _e('First Name', 'somity-manager'); ?></label>
                            <input type="text" name="first_name" id="first_name" class="form-control" value="<?php echo (isset($_POST['first_name'])) ? esc_attr($_POST['first_name']) : ''; ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="last_name"><?php _e('Last Name', 'somity-manager'); ?></label>
                            <input type="text" name="last_name" id="last_name" class="form-control" value="<?php echo (isset($_POST['last_name'])) ? esc_attr($_POST['last_name']) : ''; ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="username"><?php _e('Username', 'somity-manager'); ?></label>
                            <input type="text" name="user_login" id="username" class="form-control" value="<?php echo (isset($_POST['user_login'])) ? esc_attr($_POST['user_login']) : ''; ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email"><?php _e('Email', 'somity-manager'); ?></label>
                            <input type="email" name="user_email" id="email" class="form-control" value="<?php echo (isset($_POST['user_email'])) ? esc_attr($_POST['user_email']) : ''; ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="phone"><?php _e('Phone', 'somity-manager'); ?></label>
                            <input type="text" name="phone" id="phone" class="form-control" value="<?php echo (isset($_POST['phone'])) ? esc_attr($_POST['phone']) : ''; ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="address"><?php _e('Address', 'somity-manager'); ?></label>
                            <textarea name="address" id="address" class="form-control" rows="3" required><?php echo (isset($_POST['address'])) ? esc_textarea($_POST['address']) : ''; ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password"><?php _e('Password', 'somity-manager'); ?></label>
                            <input type="password" name="user_pass" id="password" class="form-control" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="confirm_password"><?php _e('Confirm Password', 'somity-manager'); ?></label>
                            <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                        </div>
                        
                        <div class="d-grid">
                            <input type="submit" name="wp-submit" id="wp-submit" class="btn btn-primary" value="<?php _e('Register', 'somity-manager'); ?>">
                        </div>
                        
                        <input type="hidden" name="redirect_to" value="<?php echo esc_url(home_url('/signup/?registered=success')); ?>">
                    </form>
                    
                    <div class="text-center mt-3">
                        <p><?php _e('Already have an account?', 'somity-manager'); ?> <a href="<?php echo esc_url(home_url('/login/')); ?>"><?php _e('Login', 'somity-manager'); ?></a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>