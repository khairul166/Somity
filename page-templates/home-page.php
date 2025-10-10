<?php
/**
 * Template Name: Home Page
 */

get_header();
?>

<!-- Home Page Content -->
<div id="homePage">
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 hero-content fade-in">
                    <h1 class="hero-title"><?php bloginfo('name'); ?></h1>
                    <p class="hero-subtitle"><?php _e('Together We Grow Stronger', 'somity-manager'); ?></p>
                    <p class="mb-4"><?php _e('Join our community savings group and build a secure financial future together. Save regularly, access funds when needed, and grow stronger as a community.', 'somity-manager'); ?></p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="<?php echo wp_login_url(); ?>" class="btn btn-light btn-lg"><?php _e('Login', 'somity-manager'); ?></a>
                        <a href="<?php echo esc_url(home_url('/join-somity/')); ?>" class="btn btn-outline-light btn-lg"><?php _e('Join Somity', 'somity-manager'); ?></a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <img src="https://picsum.photos/seed/savings/600/400.jpg" alt="<?php _e('Community Savings', 'somity-manager'); ?>" class="img-fluid rounded-4 shadow-lg">
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="stats-card fade-in">
                        <div class="stats-icon">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <div class="stats-number" id="totalMembers">0</div>
                        <div class="stats-label"><?php _e('Total Members', 'somity-manager'); ?></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card fade-in">
                        <div class="stats-icon">
                            <i class="bi bi-cash-stack"></i>
                        </div>
                        <div class="stats-number" id="totalSavings">$0</div>
                        <div class="stats-label"><?php _e('Total Savings', 'somity-manager'); ?></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card fade-in">
                        <div class="stats-icon">
                            <i class="bi bi-calendar-check"></i>
                        </div>
                        <div class="stats-number" id="totalInstallments">0</div>
                        <div class="stats-label"><?php _e('Total Installments', 'somity-manager'); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5"><?php _e('Why Join Our Somity?', 'somity-manager'); ?></h2>
                <p class="lead"><?php _e('We provide a secure and transparent platform for community savings', 'somity-manager'); ?></p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="bi bi-shield-check text-success" style="font-size: 3rem;"></i>
                            </div>
                            <h4><?php _e('Secure & Transparent', 'somity-manager'); ?></h4>
                            <p><?php _e('All transactions are recorded and visible to members. Your savings are safe with us.', 'somity-manager'); ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="bi bi-graph-up-arrow text-success" style="font-size: 3rem;"></i>
                            </div>
                            <h4><?php _e('Regular Growth', 'somity-manager'); ?></h4>
                            <p><?php _e('Watch your savings grow steadily with regular contributions and compound benefits.', 'somity-manager'); ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="bi bi-people text-success" style="font-size: 3rem;"></i>
                            </div>
                            <h4><?php _e('Community Support', 'somity-manager'); ?></h4>
                            <p><?php _e('Access to funds when you need them most. We\'re here to support each other.', 'somity-manager'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5"><?php _e('What Our Members Say', 'somity-manager'); ?></h2>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex mb-3">
                                <div class="user-avatar me-3">JD</div>
                                <div>
                                    <h5 class="mb-0"><?php _e('John Doe', 'somity-manager'); ?></h5>
                                    <p class="text-muted small mb-0"><?php _e('Member since 2020', 'somity-manager'); ?></p>
                                </div>
                            </div>
                            <p><?php _e('Joining this somity has been one of the best financial decisions I\'ve made. The transparency and community support are unmatched.', 'somity-manager'); ?></p>
                            <div class="text-warning">
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex mb-3">
                                <div class="user-avatar me-3">SM</div>
                                <div>
                                    <h5 class="mb-0"><?php _e('Sarah Miller', 'somity-manager'); ?></h5>
                                    <p class="text-muted small mb-0"><?php _e('Member since 2019', 'somity-manager'); ?></p>
                                </div>
                            </div>
                            <p><?php _e('I was able to start my small business with a loan from our somity. The process was simple and the interest rate was fair.', 'somity-manager'); ?></p>
                            <div class="text-warning">
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex mb-3">
                                <div class="user-avatar me-3">RJ</div>
                                <div>
                                    <h5 class="mb-0"><?php _e('Robert Johnson', 'somity-manager'); ?></h5>
                                    <p class="text-muted small mb-0"><?php _e('Member since 2021', 'somity-manager'); ?></p>
                                </div>
                            </div>
                            <p><?php _e('The online platform makes it easy to track my savings and payments. I feel more in control of my finances now.', 'somity-manager'); ?></p>
                            <div class="text-warning">
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-half"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-5 bg-success text-white">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h2 class="mb-3"><?php _e('Ready to Join Our Community?', 'somity-manager'); ?></h2>
                    <p><?php _e('Take the first step towards financial security and community support. Join our somity today!', 'somity-manager'); ?></p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a href="<?php echo esc_url(home_url('/join-somity/')); ?>" class="btn btn-light btn-lg"><?php _e('Join Now', 'somity-manager'); ?></a>
                </div>
            </div>
        </div>
    </section>
</div>

<?php get_footer(); ?>