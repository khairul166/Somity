/**
 * Somity Manager Main JavaScript
 */

(function($) {
    'use strict';

    // Document ready
    $(document).ready(function() {
        // Counter animation
        $('.stat-number').each(function() {
            var $this = $(this);
            var countTo = $this.attr('data-count');
            
            $({ countNum: $this.text() }).animate({
                countNum: countTo
            }, {
                duration: 2000,
                easing: 'linear',
                step: function() {
                    $this.text(Math.floor(this.countNum));
                },
                complete: function() {
                    $this.text(this.countNum);
                }
            });
        });
        
        // Contact form submission
        $('#contact-form').on('submit', function(e) {
            e.preventDefault();
            
            var $form = $(this);
            var $message = $('#contact-message');
            var formData = new FormData($form[0]);
            
            $.ajax({
                type: 'POST',
                url: somityAjax.ajaxurl,
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $form.find('button[type="submit"]').prop('disabled', true);
                    $message.hide();
                },
                success: function(response) {
                    if (response.success) {
                        $message.removeClass('error').addClass('success').text(response.data.message).show();
                        $form[0].reset();
                    } else {
                        $message.removeClass('success').addClass('error').text(response.data.message).show();
                    }
                    $form.find('button[type="submit"]').prop('disabled', false);
                },
                error: function() {
                    $message.removeClass('success').addClass('error').text('An error occurred. Please try again.').show();
                    $form.find('button[type="submit"]').prop('disabled', false);
                }
            });
        });
        
        // Dark mode toggle
        $('.dark-mode-toggle').on('click', function() {
            $('body').toggleClass('dark-mode');
            var isDarkMode = $('body').hasClass('dark-mode');
            localStorage.setItem('somity_dark_mode', isDarkMode);
            
            if (isDarkMode) {
                $('.toggle-icon').text('☀️');
            } else {
                $('.toggle-icon').text('🌙');
            }
        });
        
        // Check for saved dark mode preference
        if (localStorage.getItem('somity_dark_mode') === 'true') {
            $('body').addClass('dark-mode');
            $('.toggle-icon').text('☀️');
        }
        
        // Smooth scroll
        $('a[href^="#"]').on('click', function(e) {
            e.preventDefault();
            
            var target = $(this.getAttribute('href'));
            if (target.length) {
                $('html, body').animate({
                    scrollTop: target.offset().top - 70
                }, 500);
            }
        });
        
        // File upload preview
        $('.file-input').on('change', function() {
            var file = this.files[0];
            var reader = new FileReader();
            var preview = $(this).siblings('.file-preview');
            
            reader.onload = function(e) {
                preview.html('<img src="' + e.target.result + '" alt="Preview">');
                preview.show();
            };
            
            if (file) {
                reader.readAsDataURL(file);
            } else {
                preview.hide();
            }
        });
        
        // Sidebar toggle
        $('.sidebar-toggle').on('click', function() {
            $('.dashboard-sidebar').toggleClass('active');
        });
        
        // Close sidebar when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.dashboard-sidebar, .sidebar-toggle').length) {
                $('.dashboard-sidebar').removeClass('active');
            }
        });
    });

})(jQuery);