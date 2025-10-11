<?php
/**
 * Somity Manager Theme Functions
 */

// Define constants
define('SOMITY_VERSION', '1.0.0');
define('SOMITY_DIR', get_template_directory());
define('SOMITY_URL', get_template_directory_uri());

// Theme Setup
function somity_theme_setup() {
    // Add theme support
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption'));
    add_theme_support('automatic-feed-links');
    
    // Register navigation menus
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'somity-manager'),
        'footer' => __('Footer Menu', 'somity-manager'),
    ));
    
    // Add custom image sizes
    add_image_size('member-avatar', 150, 150, true);
}
add_action('after_setup_theme', 'somity_theme_setup');



// Enqueue scripts and styles
function somity_enqueue_scripts() {
    // Enqueue styles
    wp_enqueue_style('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css', array(), '5.1.3');
    wp_enqueue_style('bootstrap-icons', 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css', array(), '1.8.1');
    wp_enqueue_style('somity-style', get_stylesheet_uri(), array(), SOMITY_VERSION);
    wp_enqueue_style('somity-main-style', SOMITY_URL . '/assets/css/main.css', array(), SOMITY_VERSION);
    wp_enqueue_style('somity-dashboard-style', SOMITY_URL . '/assets/css/dashboard.css', array(), SOMITY_VERSION);
    
    // Enqueue scripts
    wp_enqueue_script('jquery');
    wp_enqueue_script('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js', array('jquery'), '5.1.3', true);
    wp_enqueue_script('somity-main', get_template_directory_uri() . '/assets/js/main.js', array('jquery'), SOMITY_VERSION, true);
    wp_enqueue_script('somity-dashboard', get_template_directory_uri() . '/assets/js/dashboard.js', array('jquery', 'bootstrap'), SOMITY_VERSION, true);
    
    // Localize script
    wp_localize_script('somity-dashboard', 'somityAjax', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('somity-nonce'),
        'memberDashboardUrl' => home_url('/member-dashboard/'),
        'adminDashboardUrl' => home_url('/admin-dashboard/'),
        'texts' => array(
            'approveConfirm' => __('Are you sure you want to approve this member?', 'somity-manager'),
            'rejectReason' => __('Please enter a reason for rejection:', 'somity-manager'),
            'errorPrefix' => __('Error:', 'somity-manager'),
            'errorMessage' => __('An error occurred. Please try again.', 'somity-manager'),
            'logoutConfirm' => __('Are you sure you want to logout?', 'somity-manager'),
        )
    ));
}
add_action('wp_enqueue_scripts', 'somity_enqueue_scripts');


// Register sidebars
function somity_widgets_init() {
    register_sidebar(array(
        'name' => __('Sidebar', 'somity-manager'),
        'id' => 'sidebar-1',
        'description' => __('Add widgets here to appear in your sidebar.', 'somity-manager'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget' => '</section>',
        'before_title' => '<h2 class="widget-title">',
        'after_title' => '</h2>',
    ));
    
    register_sidebar(array(
        'name' => __('Footer Widget Area', 'somity-manager'),
        'id' => 'footer-1',
        'description' => __('Add widgets here to appear in your footer.', 'somity-manager'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget' => '</section>',
        'before_title' => '<h2 class="widget-title">',
        'after_title' => '</h2>',
    ));
}
add_action('widgets_init', 'somity_widgets_init');

// Include custom functions
require_once SOMITY_DIR . '/inc/custom-post-types.php';
require_once SOMITY_DIR . '/inc/shortcodes.php';
require_once SOMITY_DIR . '/inc/api-functions.php';

// Custom login redirect
function somity_custom_login_redirect($redirect_to, $request, $user) {
    if (isset($user->roles) && is_array($user->roles)) {
        if (in_array('administrator', $user->roles)) {
            return home_url('/admin-dashboard/');
        } else if (in_array('member', $user->roles)) {
            return home_url('/member-dashboard/');
        }
    }
    return $redirect_to;
}
add_filter('login_redirect', 'somity_custom_login_redirect', 10, 3);

// Create user roles on theme activation
function somity_add_roles_on_activation() {
    add_role('member', __('Member', 'somity-manager'), array(
        'read' => true,
        'edit_posts' => false,
        'delete_posts' => false,
    ));
}
register_activation_hook(__FILE__, 'somity_add_roles_on_activation');

// Remove user roles on theme deactivation
function somity_remove_roles_on_deactivation() {
    remove_role('member');
}
register_deactivation_hook(__FILE__, 'somity_remove_roles_on_deactivation');


/**
 * Bootstrap Nav Walker
 */
class Bootstrap_Nav_Walker extends Walker_Nav_Menu {
    /**
     * Starts the element output.
     *
     * @since 3.0.0
     * @since 4.4.0 The {@see 'nav_menu_item_args'} filter was added.
     *
     * @see Walker::start_el()
     *
     * @param string   $output Used to append additional content (passed by reference).
     * @param WP_Post  $item   Menu item data object.
     * @param int      $depth  Depth of menu item. Used for padding.
     * @param stdClass $args   An object of wp_nav_menu() arguments.
     * @param int      $id     Current item ID.
     */
    public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
        if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
            $t = '';
            $n = '';
        } else {
            $t = "\t";
            $n = "\n";
        }
        $indent = ( $depth ) ? str_repeat( $t, $depth ) : '';

        $classes   = empty( $item->classes ) ? array() : (array) $item->classes;
        $classes[] = 'menu-item-' . $item->ID;
        
        // Add nav-item class
        $classes[] = 'nav-item';

        /**
         * Filters the CSS class(es) applied to a menu item's list item element.
         *
         * @since 3.0.0
         * @since 4.1.0 The `$depth` parameter was added.
         *
         * @param string[] $classes Array of the CSS classes that are applied to the menu item's `<li>` element.
         * @param WP_Post  $item    The current menu item.
         * @param stdClass $args    An object of wp_nav_menu() arguments.
         * @param int      $depth   Depth of menu item. Used for padding.
         */
        $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
        $class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

        /**
         * Filters the ID applied to a menu item's list item element.
         *
         * @since 3.0.1
         * @since 4.1.0 The `$depth` parameter was added.
         *
         * @param string   $menu_id The ID that is applied to the menu item's `<li>` element.
         * @param WP_Post  $item    The current menu item.
         * @param stdClass $args    An object of wp_nav_menu() arguments.
         * @param int      $depth   Depth of menu item. Used for padding.
         */
        $id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args, $depth );
        $id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

        $output .= $indent . '<li' . $id . $class_names . '>';

        $atts           = array();
        $atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
        $atts['target'] = ! empty( $item->target ) ? $item->target : '';
        $atts['rel']    = ! empty( $item->xfn ) ? $item->xfn : '';
        $atts['href']   = ! empty( $item->url ) ? $item->url : '';
        
        // Add nav-link class
        $atts['class'] = 'nav-link';

        /**
         * Filters the HTML attributes applied to a menu item's anchor element.
         *
         * @since 3.6.0
         * @since 4.1.0 The `$depth` parameter was added.
         *
         * @param array $atts {
         *     The HTML attributes applied to the menu item's `<a>` element, empty strings are ignored.
         *
         *     @type string $title  Title attribute.
         *     @type string $target Target attribute.
         *     @type string $rel    The rel attribute.
         *     @type string $href   The href attribute.
         * }
         * @param WP_Post  $item  The current menu item.
         * @param stdClass $args An object of wp_nav_menu() arguments.
         * @param int      $depth Depth of menu item. Used for padding.
         */
        $atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

        $attributes = '';
        foreach ( $atts as $attr => $value ) {
            if ( ! empty( $value ) ) {
                $value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
                $attributes .= ' ' . $attr . '="' . $value . '"';
            }
        }

        /** This filter is documented in wp-includes/post-template.php */
        $title = apply_filters( 'the_title', $item->title, $item->ID );

        /**
         * Filters a menu item's title.
         *
         * @since 4.4.0
         *
         * @param string   $title The menu item's title.
         * @param WP_Post  $item  The current menu item.
         * @param stdClass $args  An object of wp_nav_menu() arguments.
         * @param int      $depth Depth of menu item. Used for padding.
         */
        $title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );

        // Fix for the warnings - check if $args is an object and has the required properties
        $before = '';
        $link_before = '';
        $link_after = '';
        $after = '';
        
        if (is_object($args)) {
            $before = isset($args->before) ? $args->before : '';
            $link_before = isset($args->link_before) ? $args->link_before : '';
            $link_after = isset($args->link_after) ? $args->link_after : '';
            $after = isset($args->after) ? $args->after : '';
        }

        $item_output = $before;
        $item_output .= '<a'. $attributes .'>';
        $item_output .= $link_before . $title . $link_after;
        $item_output .= '</a>';
        $item_output .= $after;

        /**
         * Filters a menu item's starting output.
         *
         * The menu item's starting output only includes `$args->before`, the opening `<a>`,
         * the menu item's title, the closing `</a>`, and `$args->after`. Currently, there is
         * no filter for modifying the opening and closing `<li>` for a menu item.
         *
         * @since 3.0.0
         *
         * @param string   $item_output The menu item's starting HTML output.
         * @param WP_Post  $item        Menu item data object.
         * @param int      $depth       Depth of menu item. Used for padding.
         * @param stdClass $args        An object of wp_nav_menu() arguments.
         */
        $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
    }
}



/**
 * Add custom fields to registration form
 */
function somity_add_registration_fields() {
    ?>
    <p>
        <label for="first_name"><?php _e('First Name', 'somity-manager'); ?><br />
        <input type="text" name="first_name" id="first_name" class="input" value="<?php echo (isset($_POST['first_name'])) ? esc_attr($_POST['first_name']) : ''; ?>" size="25" /></label>
    </p>
    <p>
        <label for="last_name"><?php _e('Last Name', 'somity-manager'); ?><br />
        <input type="text" name="last_name" id="last_name" class="input" value="<?php echo (isset($_POST['last_name'])) ? esc_attr($_POST['last_name']) : ''; ?>" size="25" /></label>
    </p>
    <p>
        <label for="phone"><?php _e('Phone', 'somity-manager'); ?><br />
        <input type="text" name="phone" id="phone" class="input" value="<?php echo (isset($_POST['phone'])) ? esc_attr($_POST['phone']) : ''; ?>" size="25" /></label>
    </p>
    <p>
        <label for="address"><?php _e('Address', 'somity-manager'); ?><br />
        <textarea name="address" id="address" class="input" rows="3"><?php echo (isset($_POST['address'])) ? esc_textarea($_POST['address']) : ''; ?></textarea></label>
    </p>
    <?php
}
add_action('register_form', 'somity_add_registration_fields');

/**
 * Validate registration fields
 */
function somity_validate_registration_fields($errors, $sanitized_user_login, $user_email) {
    if (empty($_POST['first_name'])) {
        $errors->add('first_name_error', __('<strong>Error</strong>: Please enter your first name.', 'somity-manager'));
    }
    
    if (empty($_POST['last_name'])) {
        $errors->add('last_name_error', __('<strong>Error</strong>: Please enter your last name.', 'somity-manager'));
    }
    
    if (empty($_POST['phone'])) {
        $errors->add('phone_error', __('<strong>Error</strong>: Please enter your phone number.', 'somity-manager'));
    }
    
    if (empty($_POST['address'])) {
        $errors->add('address_error', __('<strong>Error</strong>: Please enter your address.', 'somity-manager'));
    }
    
    return $errors;
}
add_filter('registration_errors', 'somity_validate_registration_fields', 10, 3);

/**
 * Save registration fields
 */
function somity_save_registration_fields($user_id) {
    if (isset($_POST['first_name'])) {
        update_user_meta($user_id, 'first_name', sanitize_text_field($_POST['first_name']));
    }
    
    if (isset($_POST['last_name'])) {
        update_user_meta($user_id, 'last_name', sanitize_text_field($_POST['last_name']));
    }
    
    if (isset($_POST['phone'])) {
        update_user_meta($user_id, '_phone', sanitize_text_field($_POST['phone']));
    }
    
    if (isset($_POST['address'])) {
        update_user_meta($user_id, '_address', sanitize_textarea_field($_POST['address']));
    }
    
    // Set default member status to pending
    update_user_meta($user_id, '_member_status', 'pending');
    
    // Set user role to member
    $user = new WP_User($user_id);
    $user->set_role('member');
    
    // Create activity record
    $activity_data = array(
        'post_title' => 'New Member Registration',
        'post_content' => 'New member ' . $_POST['first_name'] . ' ' . $_POST['last_name'] . ' registered and is pending approval',
        'post_status' => 'publish',
        'post_author' => 1, // Admin user
        'post_type' => 'activity',
    );
    
    $activity_id = wp_insert_post($activity_data);
    
    if (!is_wp_error($activity_id)) {
        wp_set_post_terms($activity_id, 'member', 'activity_type');
    }
}
add_action('user_register', 'somity_save_registration_fields');


/**
 * Custom registration handler
 */
function somity_custom_registration_handler() {
    if (isset($_POST['wp-submit']) && $_POST['wp-submit'] == __('Register', 'somity-manager')) {
        $errors = new WP_Error();
        
        // Validate username
        $username = sanitize_user($_POST['user_login']);
        if (empty($username)) {
            $errors->add('empty_username', __('<strong>Error</strong>: Please enter a username.', 'somity-manager'));
        } elseif (!validate_username($username)) {
            $errors->add('invalid_username', __('<strong>Error</strong>: This username is invalid because it uses illegal characters. Please enter a valid username.', 'somity-manager'));
        } elseif (username_exists($username)) {
            $errors->add('username_exists', __('<strong>Error</strong>: This username is already registered. Please choose another one.', 'somity-manager'));
        }
        
        // Validate email
        $email = sanitize_email($_POST['user_email']);
        if (empty($email)) {
            $errors->add('empty_email', __('<strong>Error</strong>: Please type your email address.', 'somity-manager'));
        } elseif (!is_email($email)) {
            $errors->add('invalid_email', __('<strong>Error</strong>: The email address isn&#8217;t correct.', 'somity-manager'));
        } elseif (email_exists($email)) {
            $errors->add('email_exists', __('<strong>Error</strong>: This email is already registered. Please choose another one.', 'somity-manager'));
        }
        
        // Validate password
        $password = $_POST['user_pass'];
        $confirm_password = $_POST['confirm_password'];
        
        if (empty($password)) {
            $errors->add('empty_password', __('<strong>Error</strong>: Please enter a password.', 'somity-manager'));
        } elseif (strlen($password) < 8) {
            $errors->add('password_length', __('<strong>Error</strong>: Password must be at least 8 characters long.', 'somity-manager'));
        } elseif ($password !== $confirm_password) {
            $errors->add('password_mismatch', __('<strong>Error</strong>: Passwords do not match.', 'somity-manager'));
        }
        
        // Validate custom fields
        if (empty($_POST['first_name'])) {
            $errors->add('empty_first_name', __('<strong>Error</strong>: Please enter your first name.', 'somity-manager'));
        }
        
        if (empty($_POST['last_name'])) {
            $errors->add('empty_last_name', __('<strong>Error</strong>: Please enter your last name.', 'somity-manager'));
        }
        
        if (empty($_POST['phone'])) {
            $errors->add('empty_phone', __('<strong>Error</strong>: Please enter your phone number.', 'somity-manager'));
        }
        
        if (empty($_POST['address'])) {
            $errors->add('empty_address', __('<strong>Error</strong>: Please enter your address.', 'somity-manager'));
        }
        
        // If there are errors, redirect back with error messages
        if (count($errors->get_error_messages()) > 0) {
            // Use WordPress transients to store error data temporarily
            $error_key = 'registration_errors_' . md5(time());
            set_transient($error_key, $errors, 300); // Store for 5 minutes
            
            $redirect_url = add_query_arg(array(
                'registration_errors' => $error_key
            ), home_url('/signup/'));
            
            wp_redirect($redirect_url);
            exit;
        }
        
        // If no errors, create the user
        $user_id = wp_create_user($username, $password, $email);
        
        if (is_wp_error($user_id)) {
            $errors->add('registerfail', sprintf(__('<strong>Error</strong>: Couldn&#8217;t register you&hellip; please contact the <a href="mailto:%s">site admin</a>!', 'somity-manager'), get_option('admin_email')));
            
            // Use WordPress transients to store error data temporarily
            $error_key = 'registration_errors_' . md5(time());
            set_transient($error_key, $errors, 300); // Store for 5 minutes
            
            $redirect_url = add_query_arg(array(
                'registration_errors' => $error_key
            ), home_url('/signup/'));
            
            wp_redirect($redirect_url);
            exit;
        }
        
        // Update user meta
        update_user_meta($user_id, 'first_name', sanitize_text_field($_POST['first_name']));
        update_user_meta($user_id, 'last_name', sanitize_text_field($_POST['last_name']));
        update_user_meta($user_id, '_phone', sanitize_text_field($_POST['phone']));
        update_user_meta($user_id, '_address', sanitize_textarea_field($_POST['address']));
        update_user_meta($user_id, '_member_status', 'pending');
        
        // Set user role to member
        $user = new WP_User($user_id);
        $user->set_role('member');
        
        // Create activity record
        $activity_data = array(
            'post_title' => 'New Member Registration',
            'post_content' => 'New member ' . sanitize_text_field($_POST['first_name']) . ' ' . sanitize_text_field($_POST['last_name']) . ' registered and is pending approval',
            'post_status' => 'publish',
            'post_author' => 1, // Admin user
            'post_type' => 'activity',
        );
        
        $activity_id = wp_insert_post($activity_data);
        
        if (!is_wp_error($activity_id)) {
            wp_set_post_terms($activity_id, 'member', 'activity_type');
        }
        
        // Send notification email to admin
        $admin_email = get_option('admin_email');
        $subject = __('New Member Registration', 'somity-manager');
        
        // Build the email message
        $message = __('A new member has registered on', 'somity-manager') . ' ' . get_bloginfo('name') . " " . __('and is pending approval.', 'somity-manager') . "\n\n";
        $message .= "----------------------\n";
        $message .= __('Name:', 'somity-manager') . ' ' . sanitize_text_field($_POST['first_name']) . ' ' . sanitize_text_field($_POST['last_name']) . "\n";
        $message .= __('Email:', 'somity-manager') . ' ' . $email . "\n";
        $message .= __('Phone:', 'somity-manager') . ' ' . sanitize_text_field($_POST['phone']) . "\n";
        $message .= __('Address:', 'somity-manager') . ' ' . sanitize_textarea_field($_POST['address']) . "\n";
        $message .= "----------------------\n";
        $message .= __('You can approve or reject this member here:', 'somity-manager') . ' ' . admin_url('admin.php?page=manage-members');
        
        wp_mail($admin_email, $subject, $message);
        
        // Redirect to success page
        $redirect_to = $_POST['redirect_to'];
        wp_redirect($redirect_to);
        exit;
    }
    
    // Check for error key in URL and display errors
    if (isset($_GET['registration_errors'])) {
        $error_key = sanitize_text_field($_GET['registration_errors']);
        if (strpos($error_key, 'registration_errors_') === 0) {
            $errors = get_transient($error_key);
            if ($errors && is_wp_error($errors)) {
                // Store errors in a global variable for display in the template
                global $registration_errors;
                $registration_errors = $errors;
                // Delete the transient after retrieving it
                delete_transient($error_key);
            }
        }
    }
}
add_action('init', 'somity_custom_registration_handler');


/**
 * Create custom database table for installments
 */
function somity_create_installments_table() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'somity_installments';
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        member_id mediumint(9) NOT NULL,
        amount decimal(10,2) NOT NULL,
        due_date date NOT NULL,
        status varchar(20) NOT NULL DEFAULT 'pending',
        created_at datetime NOT NULL,
        updated_at datetime NOT NULL,
        PRIMARY KEY  (id),
        KEY member_id (member_id),
        KEY status (status),
        KEY due_date (due_date)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
add_action('after_switch_theme', 'somity_create_installments_table');