<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Community Savings Somity</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #006a4e;
            --secondary-color: #f8f9fa;
            --light-gray: #e9ecef;
            --dark-gray: #495057;
            --white: #ffffff;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --shadow-hover: 0 8px 15px rgba(0, 0, 0, 0.1);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            color: var(--dark-gray);
            background-color: var(--secondary-color);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        /* Typography */
        h1, h2, h3, h4, h5, h6 {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
        
        p {
            margin-bottom: 1rem;
        }
        
        /* Buttons */
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: var(--white);
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background-color: #00563d;
            border-color: #00563d;
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
        }
        
        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            color: var(--white);
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
        }
        
        /* Forms */
        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid var(--light-gray);
            padding: 12px 15px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(0, 106, 78, 0.25);
        }
        
        .form-label {
            font-weight: 500;
            margin-bottom: 8px;
            color: var(--dark-gray);
        }
        
        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        /* Signup Container */
        .signup-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 0;
        }
        
        .signup-card {
            background-color: var(--white);
            border-radius: 12px;
            box-shadow: var(--shadow);
            overflow: hidden;
            max-width: 900px;
            width: 100%;
            margin: 0 auto;
        }
        
        .signup-image {
            background: linear-gradient(135deg, var(--primary-color), #00895c);
            color: var(--white);
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }
        
        .signup-image img {
            max-width: 200px;
            margin-bottom: 20px;
            border-radius: 50%;
            background-color: var(--white);
            padding: 10px;
        }
        
        .signup-form {
            padding: 40px;
        }
        
        .signup-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .signup-header h2 {
            font-size: 1.8rem;
            margin-bottom: 10px;
        }
        
        .signup-header p {
            color: var(--dark-gray);
        }
        
        .form-steps {
            display: flex;
            margin-bottom: 30px;
            position: relative;
        }
        
        .form-steps::before {
            content: "";
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            height: 2px;
            background-color: var(--light-gray);
            z-index: 1;
        }
        
        .step {
            position: relative;
            z-index: 2;
            flex: 1;
            text-align: center;
        }
        
        .step-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--light-gray);
            color: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            font-weight: 600;
        }
        
        .step.active .step-circle {
            background-color: var(--primary-color);
        }
        
        .step.completed .step-circle {
            background-color: #28a745;
        }
        
        .step-label {
            font-size: 0.9rem;
            color: var(--dark-gray);
        }
        
        .step.active .step-label {
            color: var(--primary-color);
            font-weight: 600;
        }
        
        .form-section {
            display: none;
        }
        
        .form-section.active {
            display: block;
        }
        
        .social-login {
            margin-top: 25px;
        }
        
        .social-login .divider {
            text-align: center;
            margin: 20px 0;
            position: relative;
        }
        
        .social-login .divider::before {
            content: "";
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background-color: var(--light-gray);
        }
        
        .social-login .divider span {
            background-color: var(--white);
            padding: 0 15px;
            position: relative;
            color: var(--dark-gray);
        }
        
        .social-buttons {
            display: flex;
            gap: 10px;
        }
        
        .social-buttons .btn {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        /* Footer */
        footer {
            background-color: var(--primary-color);
            color: var(--white);
            padding: 30px 0;
            margin-top: auto;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .signup-image {
                padding: 30px 20px;
            }
            
            .signup-form {
                padding: 30px 20px;
            }
            
            .social-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <!-- Signup Container -->
    <div class="signup-container">
        <div class="signup-card">
            <div class="row g-0">
                <!-- Left Side - Image and Info -->
                <div class="col-lg-5 d-none d-lg-block">
                    <div class="signup-image">
                        <img src="https://picsum.photos/seed/logo/200/200.jpg" alt="Community Savings Logo">
                        <h2>Join Our Community</h2>
                        <p>Together We Grow Stronger</p>
                        <p class="mt-3">Become a member of our savings group and start building a secure financial future with community support.</p>
                    </div>
                </div>
                
                <!-- Right Side - Signup Form -->
                <div class="col-lg-7">
                    <div class="signup-form">
                        <div class="signup-header">
                            <h2>Create Your Account</h2>
                            <p>Fill in the details to join our community</p>
                        </div>
                        
                        <!-- Form Steps -->
                        <div class="form-steps">
                            <div class="step active">
                                <div class="step-circle">1</div>
                                <div class="step-label">Personal Info</div>
                            </div>
                            <div class="step">
                                <div class="step-circle">2</div>
                                <div class="step-label">Account Details</div>
                            </div>
                            <div class="step">
                                <div class="step-circle">3</div>
                                <div class="step-label">Membership</div>
                            </div>
                        </div>
                        
                        <form id="signupForm">
                            <!-- Step 1: Personal Information -->
                            <div class="form-section active" id="step1">
                                <h4 class="mb-3">Personal Information</h4>
                                
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="firstName" class="form-label">First Name</label>
                                        <input type="text" class="form-control" id="firstName" placeholder="First Name" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="lastName" class="form-label">Last Name</label>
                                        <input type="text" class="form-control" id="lastName" placeholder="Last Name" required>
                                    </div>
                                    <div class="col-12">
                                        <label for="email" class="form-label">Email Address</label>
                                        <input type="email" class="form-control" id="email" placeholder="your.email@example.com" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="phone" class="form-label">Phone Number</label>
                                        <input type="tel" class="form-control" id="phone" placeholder="+1 (555) 123-4567" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="dateOfBirth" class="form-label">Date of Birth</label>
                                        <input type="date" class="form-control" id="dateOfBirth" required>
                                    </div>
                                    <div class="col-12">
                                        <label for="address" class="form-label">Address</label>
                                        <input type="text" class="form-control" id="address" placeholder="Your Address" required>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-end mt-4">
                                    <button type="button" class="btn btn-primary" onclick="nextStep(1)">Next</button>
                                </div>
                            </div>
                            
                            <!-- Step 2: Account Details -->
                            <div class="form-section" id="step2">
                                <h4 class="mb-3">Account Details</h4>
                                
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label for="username" class="form-label">Username</label>
                                        <input type="text" class="form-control" id="username" placeholder="Choose a username" required>
                                    </div>
                                    <div class="col-12">
                                        <label for="password" class="form-label">Password</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="password" placeholder="Create a password" required>
                                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                        <div class="form-text">Password must be at least 8 characters with a number and a special character.</div>
                                    </div>
                                    <div class="col-12">
                                        <label for="confirmPassword" class="form-label">Confirm Password</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="confirmPassword" placeholder="Confirm your password" required>
                                            <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="agreeTerms" required>
                                            <label class="form-check-label" for="agreeTerms">
                                                I agree to the <a href="#" class="text-decoration-none">Terms and Conditions</a>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-between mt-4">
                                    <button type="button" class="btn btn-outline-primary" onclick="prevStep(2)">Previous</button>
                                    <button type="button" class="btn btn-primary" onclick="nextStep(2)">Next</button>
                                </div>
                            </div>
                            
                            <!-- Step 3: Membership -->
                            <div class="form-section" id="step3">
                                <h4 class="mb-3">Membership Information</h4>
                                
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label for="membershipType" class="form-label">Membership Type</label>
                                        <select class="form-select" id="membershipType" required>
                                            <option value="" selected disabled>Select membership type</option>
                                            <option value="basic">Basic - $100/month</option>
                                            <option value="standard">Standard - $300/month</option>
                                            <option value="premium">Premium - $500/month</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label for="occupation" class="form-label">Occupation</label>
                                        <input type="text" class="form-control" id="occupation" placeholder="Your occupation" required>
                                    </div>
                                    <div class="col-12">
                                        <label for="monthlyIncome" class="form-label">Monthly Income</label>
                                        <select class="form-select" id="monthlyIncome" required>
                                            <option value="" selected disabled>Select income range</option>
                                            <option value="below1000">Below $1,000</option>
                                            <option value="1000-3000">$1,000 - $3,000</option>
                                            <option value="3000-5000">$3,000 - $5,000</option>
                                            <option value="5000-10000">$5,000 - $10,000</option>
                                            <option value="above10000">Above $10,000</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label for="referral" class="form-label">Referral Code (Optional)</label>
                                        <input type="text" class="form-control" id="referral" placeholder="Enter referral code if you have one">
                                    </div>
                                    <div class="col-12">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="newsletter">
                                            <label class="form-check-label" for="newsletter">
                                                I would like to receive updates and news from Community Savings Somity
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-between mt-4">
                                    <button type="button" class="btn btn-outline-primary" onclick="prevStep(3)">Previous</button>
                                    <button type="submit" class="btn btn-primary">Create Account</button>
                                </div>
                            </div>
                        </form>
                        
                        <div class="social-login">
                            <div class="divider">
                                <span>Or sign up with</span>
                            </div>
                            
                            <div class="social-buttons">
                                <button type="button" class="btn btn-outline-primary">
                                    <i class="bi bi-google"></i> Google
                                </button>
                                <button type="button" class="btn btn-outline-primary">
                                    <i class="bi bi-facebook"></i> Facebook
                                </button>
                            </div>
                        </div>
                        
                        <div class="text-center mt-4">
                            <p>Already have an account? <a href="login.html" class="text-decoration-none">Login</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>Community Savings Somity</h5>
                    <p>Together We Grow Stronger</p>
                </div>
                <div class="col-md-3">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="index.html" class="text-white text-decoration-none">Home</a></li>
                        <li><a href="member-dashboard.html" class="text-white text-decoration-none">Member Dashboard</a></li>
                        <li><a href="admin-dashboard.html" class="text-white text-decoration-none">Admin Dashboard</a></li>
                        <li><a href="contact.html" class="text-white text-decoration-none">Contact</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5>Contact Us</h5>
                    <ul class="list-unstyled">
                        <li><i class="bi bi-envelope me-2"></i> info@communitysavings.com</li>
                        <li><i class="bi bi-telephone me-2"></i> +1 (555) 123-4567</li>
                        <li><i class="bi bi-geo-alt me-2"></i> 123 Savings St, Finance City</li>
                    </ul>
                </div>
            </div>
            <hr class="my-4 bg-white">
            <div class="text-center">
                <p class="mb-0">© 2023 Community Savings Somity. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const passwordIcon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.classList.remove('bi-eye');
                passwordIcon.classList.add('bi-eye-slash');
            } else {
                passwordInput.type = 'password';
                passwordIcon.classList.remove('bi-eye-slash');
                passwordIcon.classList.add('bi-eye');
            }
        });
        
        document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
            const confirmPasswordInput = document.getElementById('confirmPassword');
            const confirmPasswordIcon = this.querySelector('i');
            
            if (confirmPasswordInput.type === 'password') {
                confirmPasswordInput.type = 'text';
                confirmPasswordIcon.classList.remove('bi-eye');
                confirmPasswordIcon.classList.add('bi-eye-slash');
            } else {
                confirmPasswordInput.type = 'password';
                confirmPasswordIcon.classList.remove('bi-eye-slash');
                confirmPasswordIcon.classList.add('bi-eye');
            }
        });
        
        // Form step navigation
        function nextStep(currentStep) {
            // Validate current step before proceeding
            if (!validateStep(currentStep)) {
                return;
            }
            
            // Hide current step
            document.getElementById(`step${currentStep}`).classList.remove('active');
            
            // Update step indicator
            const currentStepElement = document.querySelectorAll('.step')[currentStep - 1];
            currentStepElement.classList.remove('active');
            currentStepElement.classList.add('completed');
            
            // Show next step
            document.getElementById(`step${currentStep + 1}`).classList.add('active');
            
            // Update next step indicator
            const nextStepElement = document.querySelectorAll('.step')[currentStep];
            nextStepElement.classList.add('active');
        }
        
        function prevStep(currentStep) {
            // Hide current step
            document.getElementById(`step${currentStep}`).classList.remove('active');
            
            // Update step indicator
            const currentStepElement = document.querySelectorAll('.step')[currentStep - 1];
            currentStepElement.classList.remove('active');
            
            // Show previous step
            document.getElementById(`step${currentStep - 1}`).classList.add('active');
            
            // Update previous step indicator
            const prevStepElement = document.querySelectorAll('.step')[currentStep - 2];
            prevStepElement.classList.remove('completed');
            prevStepElement.classList.add('active');
        }
        
        function validateStep(step) {
            let isValid = true;
            
            if (step === 1) {
                // Validate personal information
                const firstName = document.getElementById('firstName').value;
                const lastName = document.getElementById('lastName').value;
                const email = document.getElementById('email').value;
                const phone = document.getElementById('phone').value;
                const dateOfBirth = document.getElementById('dateOfBirth').value;
                const address = document.getElementById('address').value;
                
                if (!firstName || !lastName || !email || !phone || !dateOfBirth || !address) {
                    isValid = false;
                    alert('Please fill in all required fields in the Personal Information section.');
                }
            } else if (step === 2) {
                // Validate account details
                const username = document.getElementById('username').value;
                const password = document.getElementById('password').value;
                const confirmPassword = document.getElementById('confirmPassword').value;
                const agreeTerms = document.getElementById('agreeTerms').checked;
                
                if (!username || !password || !confirmPassword || !agreeTerms) {
                    isValid = false;
                    alert('Please fill in all required fields in the Account Details section.');
                } else if (password !== confirmPassword) {
                    isValid = false;
                    alert('Passwords do not match. Please try again.');
                } else if (password.length < 8 || !/\d/.test(password) || !/[!@#$%^&*(),.?":{}|<>]/.test(password)) {
                    isValid = false;
                    alert('Password must be at least 8 characters with a number and a special character.');
                }
            }
            
            return isValid;
        }
        
        // Signup form submission
        document.getElementById('signupForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validate all steps
            if (!validateStep(3)) {
                return;
            }
            
            // Collect form data
            const formData = {
                firstName: document.getElementById('firstName').value,
                lastName: document.getElementById('lastName').value,
                email: document.getElementById('email').value,
                phone: document.getElementById('phone').value,
                dateOfBirth: document.getElementById('dateOfBirth').value,
                address: document.getElementById('address').value,
                username: document.getElementById('username').value,
                password: document.getElementById('password').value,
                membershipType: document.getElementById('membershipType').value,
                occupation: document.getElementById('occupation').value,
                monthlyIncome: document.getElementById('monthlyIncome').value,
                referral: document.getElementById('referral').value,
                newsletter: document.getElementById('newsletter').checked
            };
            
            // In a real application, this would send the signup data to the server
            console.log('Signup data:', formData);
            
            // For demo purposes, redirect to login page with a success message
            alert('Account created successfully! Please login with your new credentials.');
            window.location.href = 'login.html';
        });
    </script>
</body>
</html>