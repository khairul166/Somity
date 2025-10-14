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
            var $errorMessage = $('#errorMessage');
            var $errortext = $errorMessage.find('.errortext');
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
            submitButton.html('<i class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></i> Submitting...');
            console.log(formData);
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
                        $errorMessage.hide();
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
                        $message.hide();
                        $errortext.text(response.data.message || 'An error occurred. Please try again.');
                        $errorMessage.show();

                        // Scroll to error message
                        $('html, body').animate({
                            scrollTop: $errorMessage.offset().top - 100
                        }, 500);
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
        // Show loading state
        $('#paymentAmount').val('Loading...');
        
        $.ajax({
            url: somity_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'get_installment_amount',
                installment_id: installmentId,
                nonce: somity_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    $('#paymentAmount').val(response.data.amount);
                } else {
                    $('#paymentAmount').val('0.00');
                    alert(response.data.message || 'Error loading installment amount');
                }
            },
            error: function() {
                $('#paymentAmount').val('0.00');
                alert('Error communicating with server');
            }
        });
    } else {
        $('#paymentAmount').val('0.00');
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

        // Filter functionality
        $('#filter-btn').on('click', function() {
            var status = $('#installment-filter').val();
            var search = $('#installment-search').val();
            var month = $('#month-filter').val();
            
            var url = new URL(window.location.href);
            
            // Clear existing search parameters
            url.searchParams.delete('status');
            url.searchParams.delete('search');
            url.searchParams.delete('month');
            url.searchParams.delete('paged');
            
            // Add new search parameters
            url.searchParams.set('status', status);
            url.searchParams.set('search', search);
            url.searchParams.set('month', month);
            url.searchParams.set('paged', 1);
            
            window.location.href = url.toString();
        });

        // Export members
        $('#export-members').on('click', function() {
            var status = $('#member-filter').val();
            var search = $('#member-search').val();
            
            window.location.href = somityAjax.ajaxurl + '?action=export_members&status=' + status + '&search=' + search + '&nonce=' + somityAjax.nonce;
        });

        // Export payments
        $('#export-payments').on('click', function () {
            var filterValue = $('#payment-filter').val();
            var searchTerm = $('#payment-search').val();
            var monthFilter = $('#month-filter').val();

            window.location.href = somityAjax.ajaxurl + '?action=export_payments&filter=' + filterValue + '&search=' + searchTerm + '&month=' + monthFilter + '&nonce=' + somityAjax.nonce;
        });

        // Helper function to show success messages at bottom-right
function showSuccessMessage(message) {
    const successMessage = document.createElement('div');
    successMessage.className = 'alert alert-success alert-dismissible fade show position-fixed';
    successMessage.style.bottom = '-100px'; // Start off-screen
    successMessage.style.right = '20px'; // Position at right side
    successMessage.style.transition = 'bottom 0.3s ease-in-out';
    successMessage.style.zIndex = '9999';
    successMessage.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    document.body.appendChild(successMessage);

    // Animate in - slide up from bottom
    setTimeout(() => {
        successMessage.style.bottom = '20px';
    }, 10); // Small delay to ensure the transition takes effect

    // Animate out and remove after 3 seconds
    setTimeout(() => {
        successMessage.style.bottom = '-100px'; // Slide down
        
        // Remove the element after the animation completes
        setTimeout(() => {
            successMessage.remove();
        }, 300); // Wait for the slide-down animation to complete
    }, 3000);
}

// Approve payment on payment details page
 $('.approve-payment').on('click', function () {
    var paymentId = $(this).data('id');
    var $btn = $(this);
    var $row = $(this).closest('tr');
    var originalButtonText = $btn.html();

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
                $btn.html('<i class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></i>');
            },
            success: function (response) {
                // Add this debugging line
                console.log('AJAX Response:', response);
                
                if (response.success) {
                    // Prepare the message
                    var alertMessage = response.data.message;
                    
                    // Add overpayment info if applicable
                    if (response.data.overpayment) {
                        alertMessage += '<br><small class="overpayment-info">' + response.data.overpayment_info + '</small>';
                    }
                    
                    // Show the success message at the bottom-right
                    showSuccessMessage(alertMessage);
                    
                    //Reload the page after the message disappears (3.3 seconds to account for animation)
                    setTimeout(() => {
                        window.location.reload();
                    }, 3300);
                    
                } else {
                    // Create an error alert with debug info if available
                    var errorMessage = response.data.message;
                    if (response.data.debug) {
                        errorMessage += '<br><small class="text-muted">Debug: ' + response.data.debug + '</small>';
                    }
                    
                    var $alert = $('<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                        errorMessage +
                        '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                        '</div>');
                    
                    // Insert the alert before the payment row
                    $row.before($alert);
                    
                    $btn.prop('disabled', false);
                    $btn.html(originalButtonText);
                }
            },
            error: function (xhr, status, error) {
                // Create an error alert with detailed info
                var errorMessage = somityAjax.texts.errorMessage;
                errorMessage += '<br><small>Status: ' + status + '</small>';
                errorMessage += '<br><small>Error: ' + error + '</small>';
                if (xhr.responseText) {
                    errorMessage += '<br><small>Response: ' + xhr.responseText.substring(0, 200) + '...</small>';
                }
                
                var $alert = $('<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                    errorMessage +
                    '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                    '</div>');
                
                // Insert the alert before the payment row
                $row.before($alert);
                
                $btn.prop('disabled', false);
                $btn.html(originalButtonText);
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

        // Approve member
        $('.approve-member').on('click', function() {
            var memberId = $(this).data('id');
            var $btn = $(this);
            
            if (confirm(somityAjax.texts.approveConfirm)) {
                $.ajax({
                    type: 'POST',
                    url: somityAjax.ajaxurl,
                    data: {
                        action: 'approve_member',
                        member_id: memberId,
                        nonce: somityAjax.nonce
                    },
                    beforeSend: function() {
                        $btn.prop('disabled', true);
                        $btn.html('<i class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></i>');
                    },
                    success: function(response) {
                        if (response.success) {
                            // Show success message
                            alert(response.data.message);
                            // Reload the page
                            location.reload();
                        } else {
                            // Show error message
                            alert(response.data.message);
                            $btn.prop('disabled', false);
                            $btn.html('<i class="bi bi-check-lg"></i>');
                        }
                    },
                    error: function(xhr, status, error) {
                        // Show error message
                        alert(somityAjax.texts.errorMessage);
                        console.log(xhr.responseText);
                        $btn.prop('disabled', false);
                        $btn.html('<i class="bi bi-check-lg"></i>');
                    }
                });
            }
        });

        // Reject member
        $('.reject-member').on('click', function() {
            var memberId = $(this).data('id');
            $('#rejection-member-id').val(memberId);
            $('#rejectionModal').modal('show');
        });

        // Confirm rejection
        $('#confirm-rejection').on('click', function() {
            var memberId = $('#rejection-member-id').val();
            var reason = $('#rejection-reason').val();
            
            if (!reason) {
                alert(somityAjax.texts.rejectReason);
                return;
            }
            
            $.ajax({
                type: 'POST',
                url: somityAjax.ajaxurl,
                data: {
                    action: 'reject_member',
                    member_id: memberId,
                    reason: reason,
                    nonce: somityAjax.nonce
                },
                beforeSend: function() {
                    $('#confirm-rejection').prop('disabled', true);
                    $('#confirm-rejection').html('<i class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></i> Processing...');
                },
                success: function(response) {
                    if (response.success) {
                        $('#rejectionModal').modal('hide');
                        alert(response.data.message);
                        location.reload();
                    } else {
                        alert(response.data.message);
                        $('#confirm-rejection').prop('disabled', false);
                        $('#confirm-rejection').html('Reject Member');
                    }
                },
                error: function(xhr, status, error) {
                    alert(somityAjax.texts.errorMessage);
                    console.log(xhr.responseText);
                    $('#confirm-rejection').prop('disabled', false);
                    $('#confirm-rejection').html('Reject Member');
                }
            });
        });
        // Export installments
        $('#export-installments').on('click', function() {
            var status = $('#installment-filter').val();
            var search = $('#installment-search').val();
            var month = $('#month-filter').val();
            
            window.location.href = somityAjax.ajaxurl + '?action=export_installments&status=' + status + '&search=' + search + '&month=' + month + '&nonce=' + somityAjax.nonce;
        });

        // Generate installments button
        $('#generate-installments').on('click', function() {
            $('#generateInstallmentsModal').modal('show');
        });
        
        
        // Toggle member select based on checkbox
        $('#generate-for-all').on('change', function() {
            if ($(this).is(':checked')) {
                $('#member-select').prop('disabled', true);
            } else {
                $('#member-select').prop('disabled', false);
            }
        });
        
        // Confirm generate installments
        $('#confirm-generate').on('click', function() {
            var generateForAll = $('#generate-for-all').is(':checked');
            var memberId = $('#member-select').val();
            var amount = $('#installment-amount').val();
            var year = $('#installment-year').val();
            
            if (!generateForAll && !memberId) {
                alert('Please select a member.');
                return;
            }
            
            if (!amount || amount <= 0) {
                alert('Please enter a valid amount.');
                return;
            }
            
            $.ajax({
                type: 'POST',
                url: somityAjax.ajaxurl,
                data: {
                    action: 'generate_installments',
                    generate_for_all: generateForAll ? 1 : 0,
                    member_id: memberId,
                    amount: amount,
                    year: year,
                    nonce: somityAjax.nonce
                },
                beforeSend: function() {
                    $('#confirm-generate').prop('disabled', true);
                    $('#confirm-generate').html('<i class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></i> Generating...');
                },
                success: function(response) {
                    if (response.success) {
                        $('#generateInstallmentsModal').modal('hide');
                        alert(response.data.message);
                        location.reload();
                    } else {
                        alert(response.data.message);
                        $('#confirm-generate').prop('disabled', false);
                        $('#confirm-generate').html('Generate');
                    }
                },
                error: function(xhr, status, error) {
                    alert(somityAjax.texts.errorMessage);
                    console.log(xhr.responseText);
                    $('#confirm-generate').prop('disabled', false);
                    $('#confirm-generate').html('Generate');
                }
            });
        });
        
        // Mark as paid button
        $('.mark-as-paid').on('click', function() {
            var installmentId = $(this).data('id');
            var $btn = $(this);
            
            if (confirm('Are you sure you want to mark this installment as paid?')) {
                $.ajax({
                    type: 'POST',
                    url: somityAjax.ajaxurl,
                    data: {
                        action: 'mark_installment_paid',
                        installment_id: installmentId,
                        nonce: somityAjax.nonce
                    },
                    beforeSend: function() {
                        $btn.prop('disabled', true);
                        $btn.html('<i class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></i>');
                    },
                    success: function(response) {
                        if (response.success) {
                            // Show success message
                            alert(response.data.message);
                            // Reload the page
                            location.reload();
                        } else {
                            // Show error message
                            alert(response.data.message);
                            $btn.prop('disabled', false);
                            $btn.html('<i class="bi bi-check-lg"></i>');
                        }
                    },
                    error: function(xhr, status, error) {
                        // Show error message
                        alert(somityAjax.texts.errorMessage);
                        console.log(xhr.responseText);
                        $btn.prop('disabled', false);
                        $btn.html('<i class="bi bi-check-lg"></i>');
                    }
                });
            }
        });
    });

})(jQuery);


// // Approve payment button
//  $(document).on('click', '.approve-payment', function() {
//     var paymentId = $(this).data('id');
//     var $btn = $(this);
    
//     if (confirm('Are you sure you want to approve this payment?')) {
//         $.ajax({
//             type: 'POST',
//             url: somityAjax.ajaxurl,
//             data: {
//                 action: 'approve_payment',
//                 payment_id: paymentId,
//                 nonce: somityAjax.nonce
//             },
//             beforeSend: function() {
//                 $btn.prop('disabled', true);
//                 $btn.html('<i class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></i>');
//             },
//             success: function(response) {
//                 if (response.success) {
//                     // Show success message
//                     alert(response.data.message);
//                     // Reload the page
//                     location.reload();
//                 } else {
//                     // Show error message
//                     alert(response.data.message);
//                     $btn.prop('disabled', false);
//                     $btn.html('<i class="bi bi-check-lg"></i>');
//                 }
//             },
//             error: function(xhr, status, error) {
//                 // Show error message
//                 alert(somityAjax.texts.errorMessage);
//                 console.log(xhr.responseText);
//                 $btn.prop('disabled', false);
//                 $btn.html('<i class="bi bi-check-lg"></i>');
//             }
//         });
//     }
// });

// // Reject payment button
//  $(document).on('click', '.reject-payment', function() {
//     var paymentId = $(this).data('id');
//     var $btn = $(this);
    
//     if (confirm('Are you sure you want to reject this payment?')) {
//         $.ajax({
//             type: 'POST',
//             url: somityAjax.ajaxurl,
//             data: {
//                 action: 'reject_payment',
//                 payment_id: paymentId,
//                 nonce: somityAjax.nonce
//             },
//             beforeSend: function() {
//                 $btn.prop('disabled', true);
//                 $btn.html('<i class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></i>');
//             },
//             success: function(response) {
//                 if (response.success) {
//                     // Show success message
//                     alert(response.data.message);
//                     // Reload the page
//                     location.reload();
//                 } else {
//                     // Show error message
//                     alert(response.data.message);
//                     $btn.prop('disabled', false);
//                     $btn.html('<i class="bi bi-x-lg"></i>');
//                 }
//             },
//             error: function(xhr, status, error) {
//                 // Show error message
//                 alert(somityAjax.texts.errorMessage);
//                 console.log(xhr.responseText);
//                 $btn.prop('disabled', false);
//                 $btn.html('<i class="bi bi-x-lg"></i>');
//             }
//         });
//     }
// });

// // Export payments
//  $(document).on('click', '#export-payments', function() {
//     var status = $('select[name="status"]').val();
//     var search = $('input[name="search"]').val();
//     var month = $('select[name="month"]').val();
    
//     window.location.href = somityAjax.ajaxurl + '?action=export_payments&status=' + status + '&search=' + search + '&month=' + month + '&nonce=' + somityAjax.nonce;
// });