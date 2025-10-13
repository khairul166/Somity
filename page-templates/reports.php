<?php
/**
 * Template Name: Reports
 */

if (!is_user_logged_in() || !current_user_can('administrator')) {
    wp_redirect(home_url());
    exit;
}

get_header();
?>

<!-- Reports Content -->
<div class="container my-5">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="dashboard-sidebar">
                <div class="user-profile">
                    <div class="user-avatar">
                        <?php 
                        $current_user = wp_get_current_user();
                        $initials = substr($current_user->first_name, 0, 1) . substr($current_user->last_name, 0, 1);
                        echo esc_html($initials);
                        ?>
                    </div>
                    <div class="user-info">
                        <h4><?php echo esc_html($current_user->display_name); ?></h4>
                        <p><?php _e('Administrator', 'somity-manager'); ?></p>
                    </div>
                </div>
                
                <ul class="sidebar-menu">
                    <li><a href="<?php echo esc_url(home_url('/admin-dashboard/')); ?>"><i class="bi bi-speedometer2"></i> <?php _e('Dashboard', 'somity-manager'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/manage-members/')); ?>"><i class="bi bi-people"></i> <?php _e('Manage Members', 'somity-manager'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/approve-payments/')); ?>"><i class="bi bi-cash-stack"></i> <?php _e('Approve Payments', 'somity-manager'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/installments/')); ?>"><i class="bi bi-calendar-check"></i> <?php _e('Installments', 'somity-manager'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/reports/')); ?>" class="active"><i class="bi bi-graph-up"></i> <?php _e('Reports', 'somity-manager'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/settings/')); ?>"><i class="bi bi-gear"></i> <?php _e('Settings', 'somity-manager'); ?></a></li>
                    <li><a href="<?php echo wp_logout_url(); ?>" id="sidebarLogout"><i class="bi bi-box-arrow-right"></i> <?php _e('Logout', 'somity-manager'); ?></a></li>
                </ul>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-lg-9">
            <div class="dashboard-content">
                <h2 class="mb-4"><?php _e('Reports', 'somity-manager'); ?></h2>
                
                <!-- Report Type Selection -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title"><?php _e('Select Report Type', 'somity-manager'); ?></h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="report_type" id="payment_summary" value="payment_summary" checked>
                                    <label class="form-check-label" for="payment_summary">
                                        <?php _e('Payment Summary', 'somity-manager'); ?>
                                    </label>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="report_type" id="member_payments" value="member_payments">
                                    <label class="form-check-label" for="member_payments">
                                        <?php _e('Member Payments', 'somity-manager'); ?>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="report_type" id="installment_summary" value="installment_summary">
                                    <label class="form-check-label" for="installment_summary">
                                        <?php _e('Installment Summary', 'somity-manager'); ?>
                                    </label>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="report_type" id="overdue_installments" value="overdue_installments">
                                    <label class="form-check-label" for="overdue_installments">
                                        <?php _e('Overdue Installments', 'somity-manager'); ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Date Range Selection -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title"><?php _e('Select Date Range', 'somity-manager'); ?></h5>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="date_range" id="current_month" value="current_month" checked>
                                    <label class="form-check-label" for="current_month">
                                        <?php _e('Current Month', 'somity-manager'); ?>
                                    </label>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="date_range" id="last_month" value="last_month">
                                    <label class="form-check-label" for="last_month">
                                        <?php _e('Last Month', 'somity-manager'); ?>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="date_range" id="current_quarter" value="current_quarter">
                                    <label class="form-check-label" for="current_quarter">
                                        <?php _e('Current Quarter', 'somity-manager'); ?>
                                    </label>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="date_range" id="last_quarter" value="last_quarter">
                                    <label class="form-check-label" for="last_quarter">
                                        <?php _e('Last Quarter', 'somity-manager'); ?>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="date_range" id="current_year" value="current_year">
                                    <label class="form-check-label" for="current_year">
                                        <?php _e('Current Year', 'somity-manager'); ?>
                                    </label>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="date_range" id="custom_range" value="custom_range">
                                    <label class="form-check-label" for="custom_range">
                                        <?php _e('Custom Range', 'somity-manager'); ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Custom Date Range (hidden by default) -->
                        <div id="custom-date-range" class="row mt-3" style="display: none;">
                            <div class="col-md-5">
                                <label for="start_date" class="form-label"><?php _e('Start Date', 'somity-manager'); ?></label>
                                <input type="date" class="form-control" id="start_date">
                            </div>
                            <div class="col-md-5">
                                <label for="end_date" class="form-label"><?php _e('End Date', 'somity-manager'); ?></label>
                                <input type="date" class="form-control" id="end_date">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button id="apply-custom-range" class="btn btn-primary w-100"><?php _e('Apply', 'somity-manager'); ?></button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Generate Report Button -->
                <div class="text-center mb-4">
                    <button id="generate-report" class="btn btn-primary btn-lg">
                        <i class="bi bi-file-earmark-text"></i> <?php _e('Generate Report', 'somity-manager'); ?>
                    </button>
                </div>
                
                <!-- Report Preview (hidden by default) -->
                <div id="report-preview" class="card" style="display: none;">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0" id="report-title"><?php _e('Report Preview', 'somity-manager'); ?></h5>
                        <div>
                            <button id="print-report" class="btn btn-sm btn-outline-secondary me-2">
                                <i class="bi bi-printer"></i> <?php _e('Print', 'somity-manager'); ?>
                            </button>
                            <button id="download-report" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-download"></i> <?php _e('Download CSV', 'somity-manager'); ?>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="report-table">
                                <thead>
                                    <tr id="report-table-header">
                                        <!-- Table headers will be dynamically generated -->
                                    </tr>
                                </thead>
                                <tbody id="report-table-body">
                                    <!-- Table rows will be dynamically generated -->
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Report Summary -->
                        <div class="row mt-4" id="report-summary">
                            <!-- Summary will be dynamically generated -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Show/hide custom date range
    $('input[name="date_range"]').on('change', function() {
        if ($(this).val() === 'custom_range') {
            $('#custom-date-range').slideDown();
        } else {
            $('#custom-date-range').slideUp();
        }
    });
    
    // Generate report button
    $('#generate-report').on('click', function() {
        var reportType = $('input[name="report_type"]:checked').val();
        var dateRange = $('input[name="date_range"]:checked').val();
        var startDate = $('#start_date').val();
        var endDate = $('#end_date').val();
        
        // Validate custom date range
        if (dateRange === 'custom_range' && (!startDate || !endDate)) {
            alert('<?php _e('Please select both start and end dates for custom range.', 'somity-manager'); ?>');
            return;
        }
        
        // Show loading indicator
        $(this).prop('disabled', true);
        $(this).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> <?php _e('Generating...', 'somity-manager'); ?>');
        
        // Make AJAX request to generate report
        $.ajax({
            type: 'POST',
            url: somityAjax.ajaxurl,
            data: {
                action: 'generate_report',
                report_type: reportType,
                date_range: dateRange,
                start_date: startDate,
                end_date: endDate,
                nonce: somityAjax.nonce
            },
            success: function(response) {
                if (response.success) {
                    // Display report
                    $('#report-title').text(response.data.title);
                    $('#report-table-header').html(response.data.header);
                    $('#report-table-body').html(response.data.body);
                    $('#report-summary').html(response.data.summary);
                    
                    // Show report preview
                    $('#report-preview').slideDown();
                    
                    // Store report data for download
                    $('#download-report').data('report_data', response.data.csv_data);
                    $('#download-report').data('report_name', response.data.filename);
                } else {
                    alert(response.data.message);
                }
                
                // Reset button
                $('#generate-report').prop('disabled', false);
                $('#generate-report').html('<i class="bi bi-file-earmark-text"></i> <?php _e('Generate Report', 'somity-manager'); ?>');
            },
            error: function(xhr, status, error) {
                alert('<?php _e('Error generating report. Please try again.', 'somity-manager'); ?>');
                console.log(xhr.responseText);
                
                // Reset button
                $('#generate-report').prop('disabled', false);
                $('#generate-report').html('<i class="bi bi-file-earmark-text"></i> <?php _e('Generate Report', 'somity-manager'); ?>');
            }
        });
    });
    
    // Print report
    $('#print-report').on('click', function() {
        window.print();
    });
    
    // Download report as CSV
    $('#download-report').on('click', function() {
        var reportData = $(this).data('report_data');
        var reportName = $(this).data('report_name');
        
        if (!reportData) {
            alert('<?php _e('No report data available for download.', 'somity-manager'); ?>');
            return;
        }
        
        // Create a blob with the CSV data
        var blob = new Blob([reportData], { type: 'text/csv;charset=utf-8;' });
        
        // Create a link to download the file
        var link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = reportName;
        link.style.display = 'none';
        
        // Add the link to the document and click it
        document.body.appendChild(link);
        link.click();
        
        // Remove the link
        document.body.removeChild(link);
    });
});
</script>

<?php get_footer(); ?>