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
    wp_enqueue_style('somity-google-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap', array(), null);
    wp_enqueue_style('somity-main-style', SOMITY_URL . '/assets/css/main.css', array(), SOMITY_VERSION);
    wp_enqueue_style('somity-dashboard-style', SOMITY_URL . '/assets/css/dashboard.css', array(), SOMITY_VERSION);
    
    // Enqueue scripts
    wp_enqueue_script('jquery');
    wp_enqueue_script('somity-main-script', SOMITY_URL . '/assets/js/main.js', array('jquery'), SOMITY_VERSION, true);
    wp_enqueue_script('somity-dashboard-script', SOMITY_URL . '/assets/js/dashboard.js', array('jquery'), SOMITY_VERSION, true);
    
// Localize script - THIS IS IMPORTANT
    wp_localize_script('somity-dashboard-script', 'somityAjax', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('somity-nonce'),
        'memberDashboardUrl' => home_url('/member-dashboard/'),
        'texts' => array(
            'approveConfirm' => __('Are you sure you want to approve this payment?', 'somity-manager'),
            'rejectReason' => __('Please enter a reason for rejection:', 'somity-manager'),
            'errorPrefix' => __('Error:', 'somity-manager'),
            'errorMessage' => __('An error occurred. Please try again.', 'somity-manager'),
            'logoutConfirm' => __('Are you sure you want to logout?', 'somity-manager'),
            'cancelConfirm' => __('Are you sure you want to cancel? Any unsaved changes will be lost.', 'somity-manager'),
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



