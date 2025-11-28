<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Master Admin Login | The Skool Store</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #490d59;
            --primary-dark: #2f073a;
            --heading: #1e293b;
            --text: #475467;
            --muted: #98a2b3;
            --border: #e4e7ec;
            --card-bg: #ffffff;
            --surface: #f9fafb;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
            background: #f6f4ef;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            color: var(--text);
        }

        .login-card {
            width: min(960px, 100%);
            border-radius: 24px;
            background: var(--card-bg);
            box-shadow: 0 25px 60px rgba(15, 23, 42, 0.08);
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            overflow: hidden;
        }

        .login-card__brand {
            background: #f6f8ff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 48px 32px;
            text-align: center;
        }

        .brand-logo {
            width: 150px;
            margin-bottom: 16px;
        }

        .brand-name {
            font-size: 28px;
            font-weight: 600;
            color: var(--primary);
            margin: 0;
        }

        .login-card__form {
            padding: 48px 40px;
            background: #fff;
        }

        .form-heading {
            font-size: 26px;
            font-weight: 600;
            color: var(--heading);
            margin: 0 0 8px;
        }

        .form-heading strong {
            color: var(--primary);
        }

        .form-subtitle {
            margin: 0 0 32px;
            color: var(--muted);
            font-size: 15px;
        }

        label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 6px;
            color: var(--heading);
        }

        input {
            width: 100%;
            padding: 14px 16px;
            border-radius: 12px;
            border: 1px solid var(--border);
            font-size: 15px;
            font-family: inherit;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        input:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(11, 97, 201, 0.15);
        }

        .form-group {
            margin-bottom: 18px;
        }

        button {
            width: 100%;
            padding: 14px 16px;
            border-radius: 12px;
            border: none;
            font-size: 16px;
            font-weight: 600;
            background: #f0f4ff;
            color: #030712;
            cursor: pointer;
            transition: background 0.2s ease, transform 0.2s ease;
        }

        button:hover {
            background: #e1eaff;
            transform: translateY(-1px);
        }

        .login-note {
            font-size: 13px;
            color: var(--muted);
            margin-top: 16px;
            text-align: center;
        }

        @media (max-width: 720px) {
            .login-card {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-card__brand">
            <img class="brand-logo" src="{{ asset('assets/img/logo.svg') }}" alt="The Skool Store logo">
            <h1 class="brand-name">The Skool Store</h1>
        </div>
        <div class="login-card__form">
            <h2 class="form-heading">Sign in to Your <strong>Master Admin</strong> Account</h2>
            <p class="form-subtitle">Full access for school + catalog + system management</p>
            <form action="{{ route('master.admin.dashboard') }}" method="GET">
                <div class="form-group">
                    <label for="email">Email or Phone</label>
                    <input type="text" id="email" name="email" placeholder="admin@example.com">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="••••••••••">
                </div>
                <button type="submit">Login</button>
            </form>
            <p class="login-note">Demo only. Hook into Laravel auth once credentials are ready.</p>
            <p class="login-note" style="margin-top:8px;">
                <a href="{{ route('inventory.admin.login') }}" style="color:#490d59;text-decoration:none;">Inventory Admin Login →</a>
            </p>
        </div>
    </div>
</body>
</html>

