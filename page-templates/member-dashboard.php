<?php
/**
 * Template Name: Member Dashboard
 */

if (!is_user_logged_in()) {
    wp_redirect(wp_login_url());
    exit;
}

get_header();
?>

<div class="dashboard-layout">
    <?php get_sidebar(); ?>
    
    <div class="dashboard-content">
        <div class="dashboard-header">
            <h1 class="dashboard-title"><?php _e('Member Dashboard', 'somity-manager'); ?></h1>
            <div class="dashboard-actions">
                <a href="<?php echo esc_url(home_url('/submit-payment/')); ?>" class="btn btn-primary"><?php _e('Submit New Payment', 'somity-manager'); ?></a>
            </div>
        </div>
        
        <div class="dashboard-body">
            <div class="member-info-card">
                <div class="member-avatar">
                    <?php 
                    $current_user = wp_get_current_user();
                    if ($current_user->user_avatar) {
                        echo '<img src="' . esc_url($current_user->user_avatar) . '" alt="' . esc_attr($current_user->display_name) . '">';
                    } else {
                        echo '<div class="avatar-initials">' . substr($current_user->first_name, 0, 1) . substr($current_user->last_name, 0, 1) . '</div>';
                    }
                    ?>
                </div>
                <div class="member-details">
                    <h2><?php echo esc_html($current_user->display_name); ?></h2>
                    <p><?php echo esc_html($current_user->user_email); ?></p>
                    <p><?php _e('Member ID:', 'somity-manager'); ?> <?php echo esc_html($current_user->ID); ?></p>
                    <p><?php _e('Joined:', 'somity-manager'); ?> <?php echo esc_html(date('F j, Y', strtotime($current_user->user_registered))); ?></p>
                </div>
            </div>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">💰</div>
                    <div class="stat-number"><?php echo esc_html(somity_get_member_balance($current_user->ID)); ?></div>
                    <div class="stat-label"><?php _e('Current Balance', 'somity-manager'); ?></div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">✅</div>
                    <div class="stat-number"><?php echo esc_html(somity_get_member_total_paid($current_user->ID)); ?></div>
                    <div class="stat-label"><?php _e('Total Paid', 'somity-manager'); ?></div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">⏳</div>
                    <div class="stat-number"><?php echo esc_html(somity_get_member_pending_payments($current_user->ID)); ?></div>
                    <div class="stat-label"><?php _e('Pending Installments', 'somity-manager'); ?></div>
                </div>
            </div>
            
            <div class="recent-payments">
                <h3><?php _e('Recent Payments', 'somity-manager'); ?></h3>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th><?php _e('Date', 'somity-manager'); ?></th>
                                <th><?php _e('Amount', 'somity-manager'); ?></th>
                                <th><?php _e('Transaction ID', 'somity-manager'); ?></th>
                                <th><?php _e('Status', 'somity-manager'); ?></th>
                                <th><?php _e('Actions', 'somity-manager'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $payments = somity_get_member_payments($current_user->ID, 5);
                            if ($payments) {
                                foreach ($payments as $payment) {
                                    $status_class = '';
                                    $status_icon = '';
                                    
                                    switch ($payment->status) {
                                        case 'approved':
                                            $status_class = 'status-approved';
                                            $status_icon = '✅';
                                            break;
                                        case 'pending':
                                            $status_class = 'status-pending';
                                            $status_icon = '⏳';
                                            break;
                                        case 'rejected':
                                            $status_class = 'status-rejected';
                                            $status_icon = '❌';
                                            break;
                                    }
                                    
                                    echo '<tr>';
                                    echo '<td>' . esc_html(date('F j, Y', strtotime($payment->date))) . '</td>';
                                    echo '<td>' . esc_html($payment->amount) . '</td>';
                                    echo '<td>' . esc_html($payment->transaction_id) . '</td>';
                                    echo '<td><span class="status-badge ' . esc_attr($status_class) . '">' . esc_html($status_icon) . ' ' . esc_html(ucfirst($payment->status)) . '</span></td>';
                                    echo '<td><a href="' . esc_url(home_url('/payment-details/?payment_id=' . $payment->id)) . '" class="btn btn-sm btn-outline">' . __('View', 'somity-manager') . '</a></td>';
                                    echo '</tr>';
                                }
                            } else {
                                echo '<tr><td colspan="5">' . __('No payments found.', 'somity-manager') . '</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <div class="view-all">
                    <a href="<?php echo esc_url(home_url('/my-payments/')); ?>" class="btn btn-outline"><?php _e('View All Payments', 'somity-manager'); ?></a>
                </div>
            </div>
            
            <div class="upcoming-installments">
                <h3><?php _e('Upcoming Installments', 'somity-manager'); ?></h3>
                <div class="installment-list">
                    <?php
                    $installments = somity_get_member_upcoming_installments($current_user->ID, 3);
                    if ($installments) {
                        foreach ($installments as $installment) {
                            echo '<div class="installment-card">';
                            echo '<div class="installment-date">' . esc_html(date('F j, Y', strtotime($installment->due_date))) . '</div>';
                            echo '<div class="installment-amount">' . esc_html($installment->amount) . '</div>';
                            echo '<div class="installment-actions">';
                            echo '<a href="' . esc_url(home_url('/submit-payment/?installment_id=' . $installment->id)) . '" class="btn btn-sm btn-primary">' . __('Pay Now', 'somity-manager') . '</a>';
                            echo '</div>';
                            echo '</div>';
                        }
                    } else {
                        echo '<p>' . __('No upcoming installments.', 'somity-manager') . '</p>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>