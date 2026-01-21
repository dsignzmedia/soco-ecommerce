<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Master Admin Login | The Skool Store</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #7c3aed;
            --primary-dark: #6d28d9;
            --primary-light: #8b5cf6;
            --accent: #ff6b35;
            --heading: #1a202c;
            --text: #4a5568;
            --text-light: #718096;
            --border: #e2e8f0;
            --card-bg: #ffffff;
            --surface: #f7fafc;
            --success: #10b981;
            --error: #ef4444;
            --warning: #f59e0b;
            --info: #3b82f6;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #f6f4ef;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            position: relative;
            overflow-x: hidden;
        }


        .login-container {
            width: 100%;
            max-width: 900px;
            position: relative;
            z-index: 1;
        }

        .login-card {
            display: grid;
            grid-template-columns: 1fr 1fr;
            background: var(--card-bg);
            border-radius: 32px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-card__brand {
            background: linear-gradient(135deg, #f3e8ff 0%, #e9d5ff57 50%, #ddd6fe 100%);
            padding: 48px 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            position: relative;
            overflow: hidden;
        }


        .brand-content {
            position: relative;
            z-index: 2;
        }

        .brand-logo {
            width: 180px;
            height: auto;
            margin-bottom: 32px;
        }

        .brand-name {
            font-size: 36px;
            font-weight: 700;
            color: #7c3aed;
            margin-bottom: 16px;
            letter-spacing: -0.5px;
        }

        .brand-tagline {
            font-size: 16px;
            color: #6b21a8;
            font-weight: 400;
            line-height: 1.6;
            max-width: 300px;
        }

        .brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 32px;
            padding: 12px 24px;
            background: rgba(124, 58, 237, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 50px;
            border: 1px solid rgba(124, 58, 237, 0.2);
            color: #7c3aed;
            font-size: 14px;
            font-weight: 600;
        }

        .brand-badge i {
            font-size: 18px;
        }

        .login-card__form {
            padding: 48px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: #ffffff;
        }

        .form-header {
            margin-bottom: 40px;
        }

        .form-heading {
            font-size: 32px;
            font-weight: 700;
            color: var(--heading);
            margin-bottom: 12px;
            letter-spacing: -0.5px;
            line-height: 1.2;
        }

        .form-heading strong {
            color: var(--primary);
        }

        .form-subtitle {
            font-size: 16px;
            color: var(--text-light);
            font-weight: 400;
            line-height: 1.6;
        }

        .alert {
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 24px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            font-size: 14px;
            line-height: 1.5;
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-10px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .alert-success {
            background: #ecfdf5;
            border: 1px solid #86efac;
            color: #065f46;
        }

        .alert-info {
            background: #eff6ff;
            border: 1px solid #93c5fd;
            color: #1e40af;
        }

        .alert-error {
            background: #fef2f2;
            border: 1px solid #fca5a5;
            color: #991b1b;
        }

        .alert i {
            font-size: 18px;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .alert ul {
            margin: 0;
            padding-left: 0;
            list-style: none;
        }

        .alert li {
            margin-top: 4px;
        }

        .alert li:first-child {
            margin-top: 0;
        }

        .form-group {
            margin-bottom: 24px;
        }

        label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: var(--heading);
            margin-bottom: 8px;
            letter-spacing: -0.2px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-light);
            font-size: 18px;
            pointer-events: none;
            transition: color 0.2s ease;
        }

        input {
            width: 100%;
            padding: 16px 16px 16px 48px;
            border-radius: 12px;
            border: 2px solid var(--border);
            font-size: 15px;
            font-family: inherit;
            color: var(--heading);
            background: var(--surface);
            transition: all 0.2s ease;
        }

        input[type="password"],
        input[type="text"][name="password"] {
            padding-right: 48px;
        }

        input::placeholder {
            color: var(--text-light);
        }

        input:focus {
            outline: none;
            border-color: var(--primary);
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(124, 58, 237, 0.1);
        }

        input.error {
            border-color: var(--error);
            background: #fff5f5;
        }

        input.error:focus {
            border-color: var(--error);
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1);
        }

        .input-wrapper.error i {
            color: var(--error);
        }

        .input-wrapper.focused i {
            color: var(--primary);
        }

        .field-error {
            color: var(--error);
            font-size: 13px;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .field-error i {
            font-size: 14px;
        }

        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-light);
            cursor: pointer;
            padding: 8px;
            font-size: 18px;
            transition: color 0.2s ease;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
        }

        .password-toggle:hover {
            color: var(--primary);
        }

        button[type="submit"] {
            width: 100%;
            padding: 16px 24px;
            border-radius: 12px;
            border: none;
            font-size: 16px;
            font-weight: 600;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: #ffffff;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(124, 58, 237, 0.25);
            position: relative;
            overflow: hidden;
            margin-top: 8px;
        }

        button[type="submit"]::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        button[type="submit"]:hover::before {
            left: 100%;
        }

        button[type="submit"]:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(124, 58, 237, 0.3);
        }

        button[type="submit"]:active {
            transform: translateY(0);
        }

        .login-note {
            margin-top: 32px;
            text-align: center;
            font-size: 14px;
            color: var(--text-light);
        }

        .login-note a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .login-note a:hover {
            color: var(--primary-light);
            gap: 10px;
        }

        .login-note a i {
            font-size: 12px;
            transition: transform 0.2s ease;
        }

        .login-note a:hover i {
            transform: translateX(4px);
        }

        @media (max-width: 968px) {
            .login-card {
                grid-template-columns: 1fr;
            }

            .login-card__brand {
                padding: 48px 32px;
            }

            .login-card__form {
                padding: 48px 32px;
            }

            .brand-name {
                font-size: 28px;
            }

            .form-heading {
                font-size: 28px;
            }
        }

        @media (max-width: 640px) {
            body {
                padding: 16px;
            }

            .login-card {
                border-radius: 24px;
            }

            .login-card__brand {
                padding: 40px 24px;
            }

            .login-card__form {
                padding: 40px 24px;
            }

            .brand-logo {
                width: 140px;
            }

            .form-heading {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-card__brand">
                <div class="brand-content">
                    <img class="brand-logo" src="{{ asset('assets/img/new logo/new_logo-master.png') }}" alt="The Skool Store logo">
                    <h1 class="brand-name">The Skool Store</h1>
                    <p class="brand-tagline">Empowering educational institutions with seamless e-commerce solutions</p>
                    <div class="brand-badge">
                        <i class="fas fa-shield-alt"></i>
                        <span>Master Admin Portal</span>
                    </div>
                </div>
            </div>
            <div class="login-card__form">
                <div class="form-header">
                    <h2 class="form-heading">Sign in to <strong>Master Admin</strong></h2>
                    <p class="form-subtitle">Full access for school, catalog & system management</p>
                </div>
                
                @if (session('status'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <div>{{ session('status') }}</div>
                    </div>
                @endif
                
                @if (session('info'))
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <div>{{ session('info') }}</div>
                    </div>
                @endif



                <form action="{{ route('master.admin.login.submit') }}" method="POST" autocomplete="off" id="loginForm">
                    @csrf
                    <div class="form-group">
                        <label for="email">Email or Phone</label>
                        <div class="input-wrapper {{ $errors->has('email') ? 'error' : '' }}">
                            <i class="fas fa-user"></i>
                            <input 
                                type="text" 
                                id="email" 
                                name="email" 
                                class="{{ $errors->has('email') ? 'error' : '' }}"
                                placeholder="admin@example.com" 
                                value="{{ old('email') }}"
                                autocomplete="off" 
                                autofocus
                            >
                        </div>
                        @if ($errors->has('email'))
                            <div class="field-error">
                                <i class="fas fa-exclamation-circle"></i>
                                <span>{{ $errors->first('email') }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-wrapper {{ $errors->has('password') ? 'error' : '' }}">
                            <i class="fas fa-lock"></i>
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                class="{{ $errors->has('password') ? 'error' : '' }}"
                                placeholder="Enter your password" 
                                autocomplete="new-password"
                            >
                            <button type="button" class="password-toggle" onclick="togglePassword()">
                                <i class="fas fa-eye" id="passwordToggleIcon"></i>
                            </button>
                        </div>
                        @if ($errors->has('password'))
                            <div class="field-error">
                                <i class="fas fa-exclamation-circle"></i>
                                <span>{{ $errors->first('password') }}</span>
                            </div>
                        @endif
                    </div>
                    <button type="submit">
                        <span>Sign In</span>
                    </button>
                </form>
                
                <p class="login-note">
                    <a href="{{ route('inventory.admin.login') }}">
                        Inventory Admin Login
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </p>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('passwordToggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        // Handle input focus for icon color change and clear errors
        document.querySelectorAll('.input-wrapper input').forEach(input => {
            const wrapper = input.closest('.input-wrapper');
            const formGroup = wrapper.closest('.form-group');
            
            input.addEventListener('focus', () => {
                wrapper.classList.add('focused');
                // Remove error state when user starts typing
                input.classList.remove('error');
                wrapper.classList.remove('error');
            });
            
            input.addEventListener('blur', () => {
                wrapper.classList.remove('focused');
            });

            // Clear error on input
            input.addEventListener('input', () => {
                if (input.classList.contains('error')) {
                    input.classList.remove('error');
                    wrapper.classList.remove('error');
                    const fieldError = formGroup.querySelector('.field-error');
                    if (fieldError) {
                        fieldError.style.display = 'none';
                    }
                }
            });
        });

        // Form submission loading state
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const submitButton = this.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Signing In...';
        });
    </script>
</body>
</html>
