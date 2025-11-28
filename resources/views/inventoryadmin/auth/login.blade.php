<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Admin Login | The Skool Store</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4f46e5;
            --surface: #f5f7fb;
            --border: rgba(15, 23, 42, 0.08);
            --text: #475467;
            --heading: #0f172a;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f4f0ff, #f0f5ff);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .login-shell {
            width: min(960px, 100%);
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            background: #fff;
            border-radius: 28px;
            overflow: hidden;
            box-shadow: 0 30px 80px rgba(15, 23, 42, 0.08);
        }

        .login-shell__hero {
            padding: 48px;
            background: #111827;
            color: #e2e8f0;
            display: flex;
            flex-direction: column;
            gap: 20px;
            justify-content: center;
        }

        .login-shell__hero h1 {
            margin: 0;
            color: #fff;
            font-size: 34px;
        }

        .login-shell__hero p {
            margin: 0;
            opacity: 0.8;
        }

        .login-shell__form {
            padding: 48px 40px;
        }

        label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: var(--heading);
            margin-bottom: 6px;
        }

        input {
            width: 100%;
            border-radius: 14px;
            border: 1px solid var(--border);
            padding: 14px 16px;
            font-size: 15px;
            font-family: inherit;
            margin-bottom: 18px;
        }

        input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
        }

        button {
            width: 100%;
            padding: 14px 16px;
            border-radius: 14px;
            border: none;
            font-size: 16px;
            font-weight: 600;
            background: var(--primary);
            color: #fff;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        button:hover {
            transform: translateY(-1px);
            box-shadow: 0 18px 30px rgba(79, 70, 229, 0.25);
        }

        .switch-link {
            display: block;
            text-align: center;
            margin-top: 24px;
            color: var(--primary);
            font-weight: 600;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <section class="login-shell">
        <div class="login-shell__hero">
            <img src="{{ asset('assets/img/logo.svg') }}" alt="The Skool Store" style="width:140px;">
            <h1>Inventory Admin</h1>
            <p>Focused tooling for stock managers. Track low stock, log adjustments and stay in sync with Master Admin directives.</p>
        </div>
        <div class="login-shell__form">
            <h2 style="margin:0 0 8px;font-size:28px;">Sign in</h2>
            <p style="margin:0 0 24px;color:var(--text);">Enter credentials shared by the Master Admin team.</p>
            <form method="GET" action="{{ route('inventory.admin.dashboard') }}">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="inventory.manager@school.com">

                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="••••••••••">

                <button type="submit">Login</button>
            </form>
            <a class="switch-link" href="{{ route('master.admin.login') }}">Master Admin Login →</a>
        </div>
    </section>
</body>
</html>

