<?php
/**
 * Template Name: Contact Page
 */

get_header();
?>

<div class="contact-page">
    <div class="container">
        <div class="page-header">
            <h1 class="page-title"><?php the_title(); ?></h1>
            <div class="page-description">
                <?php _e('Get in touch with our team for any questions or support.', 'somity-manager'); ?>
            </div>
        </div>
        
        <div class="contact-content">
            <div class="contact-info">
                <div class="contact-card">
                    <h3><?php _e('Contact Information', 'somity-manager'); ?></h3>
                    
                    <div class="contact-item">
                        <div class="contact-icon">📞</div>
                        <div class="contact-details">
                            <h4><?php _e('Phone', 'somity-manager'); ?></h4>
                            <p><a href="tel:<?php echo esc_attr(get_option('somity_phone')); ?>"><?php echo esc_html(get_option('somity_phone')); ?></a></p>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-icon">✉️</div>
                        <div class="contact-details">
                            <h4><?php _e('Email', 'somity-manager'); ?></h4>
                            <p><a href="mailto:<?php echo antispambot(get_option('somity_email')); ?>"><?php echo antispambot(get_option('somity_email')); ?></a></p>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-icon">💬</div>
                        <div class="contact-details">
                            <h4><?php _e('WhatsApp', 'somity-manager'); ?></h4>
                            <p><a href="https://wa.me/<?php echo esc_attr(preg_replace('/[^0-9]/', '', get_option('somity_whatsapp'))); ?>" target="_blank"><?php echo esc_html(get_option('somity_whatsapp')); ?></a></p>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-icon">📍</div>
                        <div class="contact-details">
                            <h4><?php _e('Address', 'somity-manager'); ?></h4>
                            <p><?php echo esc_html(get_option('somity_address')); ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="contact-card">
                    <h3><?php _e('Office Hours', 'somity-manager'); ?></h3>
                    <div class="office-hours">
                        <div class="day-time">
                            <span class="day"><?php _e('Monday - Friday', 'somity-manager'); ?></span>
                            <span class="time"><?php echo esc_html(get_option('somity_weekday_hours')); ?></span>
                        </div>
                        <div class="day-time">
                            <span class="day"><?php _e('Saturday', 'somity-manager'); ?></span>
                            <span class="time"><?php echo esc_html(get_option('somity_saturday_hours')); ?></span>
                        </div>
                        <div class="day-time">
                            <span class="day"><?php _e('Sunday', 'somity-manager'); ?></span>
                            <span class="time"><?php echo esc_html(get_option('somity_sunday_hours')); ?></span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="contact-form-container">
                <div class="contact-form-card">
                    <h3><?php _e('Send us a Message', 'somity-manager'); ?></h3>
                    
                    <form id="contact-form" class="contact-form" method="post">
                        <?php wp_nonce_field('contact_form', 'contact_nonce'); ?>
                        
                        <div class="form-group">
                            <label for="name"><?php _e('Name', 'somity-manager'); ?></label>
                            <input type="text" id="name" name="name" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email"><?php _e('Email', 'somity-manager'); ?></label>
                            <input type="email" id="email" name="email" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="phone"><?php _e('Phone', 'somity-manager'); ?></label>
                            <input type="tel" id="phone" name="phone" class="form-control">
                        </div>
                        
                        <div class="form-group">
                            <label for="subject"><?php _e('Subject', 'somity-manager'); ?></label>
                            <input type="text" id="subject" name="subject" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="message"><?php _e('Message', 'somity-manager'); ?></label>
                            <textarea id="message" name="message" class="form-control" rows="5" required></textarea>
                        </div>
                        
                        <div class="form-group">
                            <input type="hidden" name="action" value="submit_contact_form">
                            <button type="submit" class="btn btn-primary"><?php _e('Send Message', 'somity-manager'); ?></button>
                        </div>
                    </form>
                    
                    <div id="contact-message" class="contact-message" style="display: none;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>