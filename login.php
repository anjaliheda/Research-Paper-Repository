<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Research Paper Repository</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="js/login.js" defer></script>
    <style>
        /* Global Variables - Color Scheme */
        :root {
            --primary-color: #1a78c2;
            --secondary-color: #f0f9ff;
            --accent-color: #0ea5e9;
            --text-color: #1f2937;
            --background-color: #ffffff;
            --error-color: #ef4444;
            --success-color: #10b981;
        }

        /* Reset Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* Main Body Layout */
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, var(--secondary-color) 0%, #ffffff 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        /* Main Container Wrapper */
        .wrapper {
            width: 100%;
            max-width: 1200px;
            min-height: 600px;
            background: var(--background-color);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            display: flex;
            position: relative;
        }

        /* Forms Container Styles */
        .forms-container {
            flex: 1;
            padding: 3rem;
            position: relative;
            min-height: 600px;
        }

        /* Individual Form Section Styles */
        .section {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            padding: 3rem;
            transition: transform 0.6s ease-in-out, opacity 0.5s ease-in-out;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
        }

        /* Form Element Styles */
        form {
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
        }

        /* Input Group Container */
        .input-group {
            position: relative;
            margin-bottom: 1.25rem;
        }

        /* Form Transition Animations */
        .sign-up {
            transform: translateX(100%);
            opacity: 0;
            z-index: 1;
        }

        .wrapper.show-signup .sign-up {
            transform: translateX(0);
            opacity: 1;
        }

        .wrapper.show-signup .sign-in {
            transform: translateX(-100%);
            opacity: 0;
        }

        /* Left Panel Styles - Information Section */
        .left-panel {
            flex: 1;
            padding: 4rem;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--accent-color) 100%);
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        /* Left Panel Pattern Overlay */
        .left-panel::before {
            content: '';
            position: absolute;
            width: 200%;
            height: 200%;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            opacity: 0.1;
            animation: patternMove 30s linear infinite;
        }

        /* Left Panel Typography */
        .left-panel h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            line-height: 1.2;
        }

        .left-panel p {
            font-size: 1.1rem;
            opacity: 0.9;
            line-height: 1.6;
            margin-bottom: 0;
        }

        /* Form Title Styles */
        .form-title {
            font-size: 1.8rem;
            color: var(--text-color);
            margin-bottom: 2rem;
            text-align: center;
            font-weight: 600;
        }

        /* Input Field Styles */
        input {
            width: 100%;
            padding: 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #f9fafb;
        }

        input:focus {
            border-color: var(--primary-color);
            background: white;
            outline: none;
            box-shadow: 0 0 0 4px rgba(26, 120, 194, 0.1);
        }

        /* Button Styles */
        button {
            width: 100%;
            padding: 1rem;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        button:hover {
            background: var(--accent-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(26, 120, 194, 0.2);
        }

        /* Toggle Text Styles */
        .toggle-text {
            text-align: center;
            margin-top: 1.5rem;
            color: var(--text-color);
        }

        .toggle-btn {
            color: var(--primary-color);
            font-weight: 600;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .toggle-btn:hover {
            color: var(--accent-color);
            text-decoration: underline;
        }

        /* Animation Keyframes */
        @keyframes patternMove {
            0% { transform: translate(0, 0) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }

        /* Responsive Design - Mobile Styles */
        @media (max-width: 768px) {
            /* Container Adjustments */
            .wrapper {
                flex-direction: column;
                margin: 1rem;
                min-height: auto;
            }

            /* Left Panel Adjustments */
            .left-panel {
                padding: 2rem;
                text-align: center;
            }

            .left-panel h1 {
                font-size: 2rem;
            }

            .left-panel p {
                font-size: 1rem;
            }

            /* Form Container Adjustments */
            .forms-container {
                padding: 2rem 1.5rem;
                min-height: 500px;
            }

            /* Form Elements Adjustments */
            .section {
                padding: 2rem;
            }

            form {
                padding: 0;
            }

            .form-title {
                font-size: 1.5rem;
            }

            input, button {
                font-size: 0.95rem;
            }
        }

        /* Font Awesome Icon Adjustments */
        .fa-sign-in-alt, .fa-user-plus {
            font-size: 1.1rem;
        }

        button i {
            margin-right: 0.5rem;
        }

        /* Form Accessibility Styles */
        .sign-in, .sign-up {
            position: absolute;
            width: 100%;
            height: 100%;
            backface-visibility: hidden;
        }
    </style>
</head>

<body>
    <!--<?php session_start(); ?>-->
    
    <div class="wrapper">
        <div class="left-panel">
            <h1>Centralized Research Paper Repository</h1>
            <p>Sign in or Create a new account.</p>
        </div>

        <div class="forms-container">
            <!-- Sign In Form -->
            <div class="section sign-in">
                <form action="" method="POST" id="sign-in">
                    <h2 class="form-title">Welcome Back</h2>
                    <div class="input-group">
                        <input type="email" name="email" id="email" placeholder="Email Address (@rvce.edu.in)" required>
                    </div>
                    <div class="input-group">
                        <input type="password" name="password" id="password" placeholder="Password" required>
                    </div>
                    <button type="submit">
                        <i class="fas fa-sign-in-alt me-2"></i> Sign In
                    </button>
                    <p class="toggle-text">
                        Don't have an account? <span class="toggle-btn">Create Account</span>
                    </p>
                </form>
            </div>

            <!-- Sign Up Form -->
            <div class="section sign-up">
                <form action="" method="POST" id="sign-up">
                    <h2 class="form-title">Create Account</h2>
                    <div class="input-group">
                        <input type="text" name="name" id="rname" placeholder="Full Name" required>
                    </div>
                    <div class="input-group">
                        <input type="email" name="email" id="remail" placeholder="Email Address(@rvce.edu.in)" required>
                    </div>
                    <div class="input-group">
                        <input type="password" name="password" id="rpassword" placeholder="Password" required>
                    </div>
                    <input type="hidden" name="role_id" id="rrole_id" value="3">
                    <button type="submit">
                        <i class="fas fa-user-plus me-2"></i> Sign Up
                    </button>
                    <p class="toggle-text">
                        Already have an account? <span class="toggle-btn">Sign In</span>
                    </p>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.toggle-btn').forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                const wrapper = document.querySelector('.wrapper');
                wrapper.classList.toggle('show-signup');
                
                // Reset form fields when switching
                document.querySelectorAll('form').forEach(form => {
                    form.reset();
                });

                // Adjust container scroll
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        });

        // Ensure proper height on mobile
        function adjustFormHeight() {
            const forms = document.querySelectorAll('form');
            const container = document.querySelector('.forms-container');
            forms.forEach(form => {
                const height = form.offsetHeight;
                container.style.minHeight = `${height + 80}px`; // Add padding
            });
        }

        window.addEventListener('load', adjustFormHeight);
        window.addEventListener('resize', adjustFormHeight);
    </script>
</body>
</html>