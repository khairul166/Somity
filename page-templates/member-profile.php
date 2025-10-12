<?php
/**
 * Template Name: Member Profile
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

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    // Verify nonce
    if (!isset($_POST['profile_nonce']) || !wp_verify_nonce($_POST['profile_nonce'], 'somity_profile_nonce')) {
        wp_die(__('Security check failed.', 'somity-manager'));
    }
    
    // Update user data
    $updated_data = array(
        'ID' => $member_id,
        'first_name' => sanitize_text_field($_POST['first_name']),
        'last_name' => sanitize_text_field($_POST['last_name']),
        'display_name' => sanitize_text_field($_POST['first_name']) . ' ' . sanitize_text_field($_POST['last_name']),
        'user_email' => sanitize_email($_POST['user_email']),
    );
    
    $result = wp_update_user($updated_data);
    
    if (!is_wp_error($result)) {
        // Handle profile picture upload
        if (!empty($_FILES['profile_picture']['name'])) {
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/media.php');
            
            $attachment_id = media_handle_upload('profile_picture', 0);
            
            if (!is_wp_error($attachment_id)) {
                // Delete old profile picture if exists
                $old_profile_picture = get_user_meta($member_id, 'profile_picture', true);
                if ($old_profile_picture) {
                    wp_delete_attachment($old_profile_picture);
                }
                
                // Save new profile picture
                update_user_meta($member_id, 'profile_picture', $attachment_id);
            }
        }
        
        // Success message
        $success_message = __('Profile updated successfully.', 'somity-manager');
    } else {
        // Error message
        $error_message = __('Error updating profile. Please try again.', 'somity-manager');
    }
}

// Get user meta
 $profile_picture_id = get_user_meta($member_id, 'profile_picture', true);
 $profile_picture_url = $profile_picture_id ? wp_get_attachment_url($profile_picture_id) : '';

get_header();
?>

<!-- Member Profile Content -->
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
                    <li><a href="<?php echo esc_url(home_url('/installment-status/')); ?>"><i class="bi bi-calendar-check"></i> <?php _e('Installment Status', 'somity-manager'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/profile/')); ?>" class="active"><i class="bi bi-person-circle"></i> <?php _e('My Profile', 'somity-manager'); ?></a></li>
                    <li><a href="<?php echo wp_logout_url(); ?>" id="sidebarLogout"><i class="bi bi-box-arrow-right"></i> <?php _e('Logout', 'somity-manager'); ?></a></li>
                </ul>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-lg-9">
            <div class="dashboard-content">
                <h2 class="mb-4"><?php _e('My Profile', 'somity-manager'); ?></h2>
                
                <?php if (isset($success_message)) : ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo esc_html($success_message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
                
                <?php if (isset($error_message)) : ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo esc_html($error_message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
                
                <!-- Profile Form -->
                <div class="card">
                    <div class="card-body">
                        <form id="profile-form" method="post" enctype="multipart/form-data">
                            <?php wp_nonce_field('somity_profile_nonce', 'profile_nonce'); ?>
                            
                            <div class="row mb-4">
                                <div class="col-md-4 text-center">
                                    <div class="profile-picture-container mb-3">
                                        <?php if ($profile_picture_url) : ?>
                                            <img src="<?php echo esc_url($profile_picture_url); ?>" alt="<?php esc_attr($current_user->display_name); ?>" class="profile-picture img-thumbnail rounded-circle">
                                        <?php else : ?>
                                            <div class="profile-picture-placeholder img-thumbnail rounded-circle d-flex align-items-center justify-content-center">
                                                <span class="initials"><?php echo substr($current_user->first_name, 0, 1) . substr($current_user->last_name, 0, 1); ?></span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="mb-3">
                                        <label for="profile_picture" class="form-label"><?php _e('Change Profile Picture', 'somity-manager'); ?></label>
                                        <input type="file" class="form-control" id="profile_picture" name="profile_picture" accept="image/*">
                                        <div class="form-text"><?php _e('JPG, GIF or PNG. Max size of 5MB.', 'somity-manager'); ?></div>
                                    </div>
                                </div>
                                
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="first_name" class="form-label"><?php _e('First Name', 'somity-manager'); ?></label>
                                            <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo esc_attr($current_user->first_name); ?>" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="last_name" class="form-label"><?php _e('Last Name', 'somity-manager'); ?></label>
                                            <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo esc_attr($current_user->last_name); ?>" required>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="display_name" class="form-label"><?php _e('Display Name', 'somity-manager'); ?></label>
                                        <input type="text" class="form-control" id="display_name" name="display_name" value="<?php echo esc_attr($current_user->display_name); ?>" readonly>
                                        <div class="form-text"><?php _e('This is automatically generated from your first and last name.', 'somity-manager'); ?></div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="user_email" class="form-label"><?php _e('Email Address', 'somity-manager'); ?></label>
                                        <input type="email" class="form-control" id="user_email" name="user_email" value="<?php echo esc_attr($current_user->user_email); ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="member_id" class="form-label"><?php _e('Member ID', 'somity-manager'); ?></label>
                                        <input type="text" class="form-control" id="member_id" value="CSM-<?php echo date('Y'); ?>-<?php echo str_pad($member_id, 3, '0', STR_PAD_LEFT); ?>" readonly>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="member_since" class="form-label"><?php _e('Member Since', 'somity-manager'); ?></label>
                                        <input type="text" class="form-control" id="member_since" value="<?php echo date_i18n(get_option('date_format'), strtotime($current_user->user_registered)); ?>" readonly>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" name="update_profile" class="btn btn-primary">
                                    <i class="bi bi-save"></i> <?php _e('Update Profile', 'somity-manager'); ?>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Account Security -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0"><?php _e('Account Security', 'somity-manager'); ?></h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6><?php _e('Password', 'somity-manager'); ?></h6>
                                <p class="text-muted"><?php _e('Last changed: Never', 'somity-manager'); ?></p>
                                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                                    <i class="bi bi-key"></i> <?php _e('Change Password', 'somity-manager'); ?>
                                </button>
                            </div>
                            <div class="col-md-6">
                                <h6><?php _e('Two-Factor Authentication', 'somity-manager'); ?></h6>
                                <p class="text-muted"><?php _e('Add an extra layer of security to your account.', 'somity-manager'); ?></p>
                                <button type="button" class="btn btn-outline-primary">
                                    <i class="bi bi-shield-lock"></i> <?php _e('Enable 2FA', 'somity-manager'); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php _e('Change Password', 'somity-manager'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="change-password-form">
                    <div class="mb-3">
                        <label for="current_password" class="form-label"><?php _e('Current Password', 'somity-manager'); ?></label>
                        <input type="password" class="form-control" id="current_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label"><?php _e('New Password', 'somity-manager'); ?></label>
                        <input type="password" class="form-control" id="new_password" required>
                        <div class="form-text"><?php _e('Use at least 8 characters with a mix of letters, numbers & symbols.', 'somity-manager'); ?></div>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label"><?php _e('Confirm New Password', 'somity-manager'); ?></label>
                        <input type="password" class="form-control" id="confirm_password" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php _e('Cancel', 'somity-manager'); ?></button>
                <button type="button" class="btn btn-primary" id="save-password"><?php _e('Save Changes', 'somity-manager'); ?></button>
            </div>
        </div>
    </div>
</div>

<style>
.profile-picture-container {
    position: relative;
    width: 150px;
    height: 150px;
    margin: 0 auto;
}

.profile-picture,
.profile-picture-placeholder {
    width: 150px;
    height: 150px;
    object-fit: cover;
}

.profile-picture-placeholder {
    background-color: #6c5ce7;
    color: white;
    font-size: 3rem;
    font-weight: bold;
}

.initials {
    text-transform: uppercase;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Update first and last name fields to update display name
    $('#first_name, #last_name').on('input', function() {
        var firstName = $('#first_name').val();
        var lastName = $('#last_name').val();
        $('#display_name').val(firstName + ' ' + lastName);
    });
    
    // Change password form submission
    $('#save-password').on('click', function() {
        var currentPassword = $('#current_password').val();
        var newPassword = $('#new_password').val();
        var confirmPassword = $('#confirm_password').val();
        
        // Validate passwords
        if (!currentPassword || !newPassword || !confirmPassword) {
            alert('<?php _e('Please fill in all password fields.', 'somity-manager'); ?>');
            return;
        }
        
        if (newPassword !== confirmPassword) {
            alert('<?php _e('New password and confirm password do not match.', 'somity-manager'); ?>');
            return;
        }
        
        if (newPassword.length < 8) {
            alert('<?php _e('Password must be at least 8 characters long.', 'somity-manager'); ?>');
            return;
        }
        
        // Disable button and show loading state
        $(this).prop('disabled', true);
        $(this).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> <?php _e('Updating...', 'somity-manager'); ?>');
        
        // Make AJAX request
        $.ajax({
            type: 'POST',
            url: somityAjax.ajaxurl,
            data: {
                action: 'change_member_password',
                current_password: currentPassword,
                new_password: newPassword,
                nonce: somityAjax.nonce
            },
            success: function(response) {
                if (response.success) {
                    alert(response.data.message);
                    $('#changePasswordModal').modal('hide');
                    $('#change-password-form')[0].reset();
                } else {
                    alert(response.data.message);
                }
                
                // Reset button
                $('#save-password').prop('disabled', false);
                $('#save-password').html('<?php _e('Save Changes', 'somity-manager'); ?>');
            },
            error: function(xhr, status, error) {
                alert('<?php _e('Error changing password. Please try again.', 'somity-manager'); ?>');
                console.log(xhr.responseText);
                
                // Reset button
                $('#save-password').prop('disabled', false);
                $('#save-password').html('<?php _e('Save Changes', 'somity-manager'); ?>');
            }
        });
    });
});
</script>

<?php get_footer(); ?>