<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Community Savings Somity</title>
    
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
        
        /* Login Container */
        .login-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 0;
        }
        
        .login-card {
            background-color: var(--white);
            border-radius: 12px;
            box-shadow: var(--shadow);
            overflow: hidden;
            max-width: 900px;
            width: 100%;
            margin: 0 auto;
        }
        
        .login-image {
            background: linear-gradient(135deg, var(--primary-color), #00895c);
            color: var(--white);
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }
        
        .login-image img {
            max-width: 200px;
            margin-bottom: 20px;
            border-radius: 50%;
            background-color: var(--white);
            padding: 10px;
        }
        
        .login-form {
            padding: 40px;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .login-header h2 {
            font-size: 1.8rem;
            margin-bottom: 10px;
        }
        
        .login-header p {
            color: var(--dark-gray);
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
            .login-image {
                padding: 30px 20px;
            }
            
            .login-form {
                padding: 30px 20px;
            }
            
            .social-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <!-- Login Container -->
    <div class="login-container">
        <div class="login-card">
            <div class="row g-0">
                <!-- Left Side - Image and Info -->
                <div class="col-lg-5 d-none d-lg-block">
                    <div class="login-image">
                        <img src="https://picsum.photos/seed/logo/200/200.jpg" alt="Community Savings Logo">
                        <h2>Community Savings Somity</h2>
                        <p>Together We Grow Stronger</p>
                        <p class="mt-3">Access your account to manage your savings, view payment history, and stay connected with our community.</p>
                    </div>
                </div>
                
                <!-- Right Side - Login Form -->
                <div class="col-lg-7">
                    <div class="login-form">
                        <div class="login-header">
                            <h2>Welcome Back</h2>
                            <p>Please login to your account</p>
                        </div>
                        
                        <form id="loginForm">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                    <input type="email" class="form-control" id="email" placeholder="Enter your email" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                    <input type="password" class="form-control" id="password" placeholder="Enter your password" required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="rememberMe">
                                <label class="form-check-label" for="rememberMe">Remember me</label>
                                <div class="float-end">
                                    <a href="forget-password.html" class="text-decoration-none">Forgot password?</a>
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Login</button>
                            </div>
                        </form>
                        
                        <div class="social-login">
                            <div class="divider">
                                <span>Or login with</span>
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
                            <p>Don't have an account? <a href="signup.html" class="text-decoration-none">Sign up</a></p>
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
        
        // Login form submission
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const rememberMe = document.getElementById('rememberMe').checked;
            
            // In a real application, this would send the login data to the server
            console.log('Login attempt:', { email, password, rememberMe });
            
            // For demo purposes, redirect to member dashboard
            window.location.href = 'member-dashboard.html';
        });
    </script>
</body>
</html>