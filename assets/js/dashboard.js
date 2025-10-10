/**
 * Somity Manager Dashboard JavaScript
 */

(function ($) {
    'use strict';

    // Document ready
    $(document).ready(function () {
        // Counter animation for stats
        function animateCounter(id, start, end, duration, prefix = '') {
            let startTimestamp = null;
            const step = (timestamp) => {
                if (!startTimestamp) startTimestamp = timestamp;
                const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                const currentValue = Math.floor(progress * (end - start) + start);
                $(id).text(prefix + currentValue.toLocaleString());
                if (progress < 1) {
                    window.requestAnimationFrame(step);
                }
            };
            window.requestAnimationFrame(step);
        }

        // Initialize counters when page loads
        if ($('.stat-number').length) {
            $('.stat-number').each(function () {
                var $this = $(this);
                var countTo = $this.attr('data-count');
                var prefix = $this.attr('data-prefix') || '';

                animateCounter($this, 0, countTo, 2000, prefix);
            });
        }

        // Dark mode toggle
        $('#darkModeToggle').on('click', function () {
            $('body').toggleClass('dark-mode');
            var isDarkMode = $('body').hasClass('dark-mode');
            localStorage.setItem('somity_dark_mode', isDarkMode);
        });

        // Check for saved dark mode preference
        if (localStorage.getItem('somity_dark_mode') === 'true') {
            $('body').addClass('dark-mode');
        }

        // Sidebar toggle
        $('.sidebar-toggle').on('click', function () {
            $('.dashboard-sidebar').toggleClass('active');
        });

        // Close sidebar when clicking outside
        $(document).on('click', function (e) {
            if (!$(e.target).closest('.dashboard-sidebar, .sidebar-toggle').length) {
                $('.dashboard-sidebar').removeClass('active');
            }
        });

        // File upload label update and preview
        $('#paymentScreenshot').on('change', function (e) {
            const file = e.target.files[0];
            const fileName = file?.name || 'Click to upload or drag and drop';
            $('#fileLabel').text(fileName);

            // Show a preview of the image if selected
            if (file) {
                // Check file size (5MB limit)
                if (file.size > 5 * 1024 * 1024) {
                    alert('File is too large. Please select a file smaller than 5MB.');
                    $(this).val('');
                    $('#fileLabel').text('Click to upload or drag and drop');
                    $('#imagePreview').hide();
                    return;
                }

                // Check file type
                if (!file.type.match('image.*')) {
                    alert('Please select an image file (PNG, JPG, etc.).');
                    $(this).val('');
                    $('#fileLabel').text('Click to upload or drag and drop');
                    $('#imagePreview').hide();
                    return;
                }

                const reader = new FileReader();

                reader.onload = function (e) {
                    $('#imagePreview img').attr('src', e.target.result);
                    $('#imagePreview').show();
                };

                reader.onerror = function () {
                    alert('Error reading file. Please try again.');
                };

                reader.readAsDataURL(file);
            } else {
                $('#imagePreview').hide();
            }
        });

        // Payment form submission
        $('#paymentForm').on('submit', function (e) {
            e.preventDefault();

            var $form = $(this);
            var $message = $('#successMessage');
            var formData = new FormData($form[0]);
            const submitButton = $form.find('button[type="submit"]');

            // Add nonce to form data
            formData.append('nonce', somityAjax.nonce);

            // Check if file is selected
            var fileInput = document.getElementById('paymentScreenshot');
            if (fileInput.files.length === 0) {
                if (!confirm('No file selected. Do you want to continue without uploading a screenshot?')) {
                    return;
                }
            }

            // Show loading state
            submitButton.prop('disabled', true);
            submitButton.html('<i class="bi bi-spinner-border spin"></i> Submitting...');

            $.ajax({
                type: 'POST',
                url: somityAjax.ajaxurl,
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function () {
                    $message.hide();
                },
                success: function (response) {
                    // Re-enable and restore button text
                    submitButton.prop('disabled', false);
                    submitButton.html('Submit Payment');

                    if (response.success) {
                        $message.show();

                        // Scroll to success message
                        $('html, body').animate({
                            scrollTop: $message.offset().top - 100
                        }, 500);

                        // Reset form and preview
                        $form[0].reset();
                        $('#fileLabel').text('Click to upload or drag and drop');
                        $('#imagePreview').hide();
                    } else {
                        alert(response.data.message);
                    }
                },
                error: function (xhr, status, error) {
                    var errorMessage = somityAjax.texts.errorMessage;
                    
                    if (xhr.responseText) {
                        try {
                            var response = JSON.parse(xhr.responseText);
                            if (response.data && response.data.message) {
                                errorMessage = response.data.message;
                            }
                        } catch (e) {
                            console.error('Error parsing response: ' + e.message);
                        }
                    }
                    
                    alert(errorMessage);

                    // Re-enable button after error
                    submitButton.prop('disabled', false);
                    submitButton.html('Submit Payment');
                }
            });
        });

        // Cancel button
        $('#cancelBtn').on('click', function () {
            if (confirm(somityAjax.texts.cancelConfirm)) {
                window.location.href = somityAjax.memberDashboardUrl;
            }
        });

        // Set today's date as default
        $('#paymentDate').val(new Date().toISOString().split('T')[0]);

        // Update amount when installment is selected
        $('#installmentMonth').on('change', function () {
            var installmentId = $(this).val();
            if (installmentId) {
                // In a real application, you would fetch the installment amount from the server
                // For now, we'll just set a default value
                $('#paymentAmount').val('300.00');
            }
        });

        // Payment search with delay for better performance
        var searchTimer;
        $('#payment-search').on('keyup', function () {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(function () {
                var searchTerm = $('#payment-search').val().toLowerCase();
                
                if (searchTerm.length > 2 || searchTerm.length === 0) {
                    var currentUrl = new URL(window.location.href);
                    if (searchTerm.length > 0) {
                        currentUrl.searchParams.set('search', searchTerm);
                    } else {
                        currentUrl.searchParams.delete('search');
                    }
                    currentUrl.searchParams.set('paged', '1');
                    window.location.href = currentUrl.toString();
                }
            }, 500);
        });

        // Payment filter
        $('#payment-filter').on('change', function () {
            var filterValue = $(this).val();
            var currentUrl = new URL(window.location.href);
            
            if (filterValue === 'all') {
                currentUrl.searchParams.delete('status');
            } else {
                currentUrl.searchParams.set('status', filterValue);
            }
            currentUrl.searchParams.set('paged', '1');
            window.location.href = currentUrl.toString();
        });

        // Month filter
        $('#month-filter').on('change', function () {
            var filterValue = $(this).val();
            var currentUrl = new URL(window.location.href);
            
            if (filterValue === 'all') {
                currentUrl.searchParams.delete('month');
            } else {
                currentUrl.searchParams.set('month', filterValue);
            }
            currentUrl.searchParams.set('paged', '1');
            window.location.href = currentUrl.toString();
        });

        // Filter button
        $('#filter-btn').on('click', function () {
            // Reset to first page when filtering
            var currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('paged', '1');
            window.location.href = currentUrl.toString();
        });

        // Export payments
        $('#export-payments').on('click', function () {
            var filterValue = $('#payment-filter').val();
            var searchTerm = $('#payment-search').val();
            var monthFilter = $('#month-filter').val();
            
            window.location.href = somityAjax.ajaxurl + '?action=export_payments&filter=' + filterValue + '&search=' + searchTerm + '&month=' + monthFilter + '&nonce=' + somityAjax.nonce;
        });

        // Approve payment on payment details page
        $('.approve-payment').on('click', function () {
            var paymentId = $(this).data('id');
            var $btn = $(this);
            
            if (confirm(somityAjax.texts.approveConfirm)) {
                $.ajax({
                    type: 'POST',
                    url: somityAjax.ajaxurl,
                    data: {
                        action: 'approve_payment',
                        payment_id: paymentId,
                        nonce: somityAjax.nonce
                    },
                    beforeSend: function () {
                        $btn.prop('disabled', true);
                    },
                    success: function (response) {
                        if (response.success) {
                            location.reload();
                        } else {
                            alert(somityAjax.texts.errorPrefix + ' ' + response.data.message);
                            $btn.prop('disabled', false);
                        }
                    },
                    error: function () {
                        alert(somityAjax.texts.errorMessage);
                        $btn.prop('disabled', false);
                    }
                });
            }
        });

        // Reject payment on payment details page
        $('.reject-payment').on('click', function () {
            var paymentId = $(this).data('id');
            var $btn = $(this);
            var reason = prompt(somityAjax.texts.rejectReason);
            
            if (reason !== null) {
                $.ajax({
                    type: 'POST',
                    url: somityAjax.ajaxurl,
                    data: {
                        action: 'reject_payment',
                        payment_id: paymentId,
                        reason: reason,
                        nonce: somityAjax.nonce
                    },
                    beforeSend: function () {
                        $btn.prop('disabled', true);
                    },
                    success: function (response) {
                        if (response.success) {
                            location.reload();
                        } else {
                            alert(somityAjax.texts.errorPrefix + ' ' + response.data.message);
                            $btn.prop('disabled', false);
                        }
                    },
                    error: function () {
                        alert(somityAjax.texts.errorMessage);
                        $btn.prop('disabled', false);
                    }
                });
            }
        });

        // Contact form submission
        $('#contact-form').on('submit', function (e) {
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
                beforeSend: function () {
                    $form.find('button[type="submit"]').prop('disabled', true);
                    $message.hide();
                },
                success: function (response) {
                    if (response.success) {
                        $message.removeClass('error').addClass('success').text(response.data.message).show();
                        $form[0].reset();
                    } else {
                        $message.removeClass('success').addClass('error').text(response.data.message).show();
                    }
                    $form.find('button[type="submit"]').prop('disabled', false);
                },
                error: function () {
                    $message.removeClass('success').addClass('error').text(somityAjax.texts.errorMessage).show();
                    $form.find('button[type="submit"]').prop('disabled', false);
                }
            });
        });

        // Smooth scroll for anchor links
        $('a[href^="#"]').on('click', function (e) {
            var target = $(this.getAttribute('href'));
            if (target.length) {
                e.preventDefault();
                $('html, body').animate({
                    scrollTop: target.offset().top - 70
                }, 500);
            }
        });
    });

})(jQuery);