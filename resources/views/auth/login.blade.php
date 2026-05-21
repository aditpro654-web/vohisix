<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login PKL SIJA | Sistem Informasi PKL</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
            min-height: 100vh;
            /* Prefer a fast local gradient background; optionally place a local image at public/images/school-bg.jpg to override */
            background: linear-gradient(135deg, #0b3b57 0%, #123f5a 100%);
            background-image: url("{{ asset('images/school-bg.jpg') }}");
            background-position: center;
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
        }

        /* Overlay gelap agar teks kontras */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(0, 48, 86, 0.85) 0%, rgba(0, 48, 86, 0.75) 100%);
            z-index: 0;
        }

        /* Card Login */
        .login-card {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 460px;
            background: #ffffff;
            border-radius: 32px;
            box-shadow: 0 30px 50px rgba(0, 0, 0, 0.2), 0 0 0 1px rgba(255, 255, 255, 0.1);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .login-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 40px 60px rgba(0, 0, 0, 0.25);
        }

        /* Header card */
        .card-header {
            background: linear-gradient(135deg, #003056 0%, #00457d 100%);
            padding: 34px 32px 28px;
            text-align: center;
            color: white;
        }

        .card-header h1 {
            font-size: 28px;
            font-weight: 700;
            letter-spacing: -0.3px;
            margin-bottom: 10px;
        }

        .card-header p {
            font-size: 14px;
            opacity: 0.85;
            font-weight: 500;
            line-height: 1.4;
        }

        /* Body form */
        .card-body {
            padding: 36px 32px 40px;
            background: white;
        }

        /* Form group */
        .form-group {
            margin-bottom: 24px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 600;
            color: #1e293b;
            letter-spacing: -0.2px;
        }

        .form-group input {
            width: 100%;
            padding: 14px 18px;
            border: 1.5px solid #e2e8f0;
            border-radius: 20px;
            font-size: 15px;
            font-weight: 500;
            background: #fefefe;
            transition: all 0.2s ease;
            color: #0f172a;
            font-family: inherit;
        }

        .form-group input:focus {
            outline: none;
            border-color: #003056;
            box-shadow: 0 0 0 4px rgba(0, 48, 86, 0.12);
            background: #ffffff;
        }

        /* Error styling */
        .form-error {
            margin-top: 8px;
            color: #dc2626;
            font-size: 13px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .error-banner {
            background: #fee2e2;
            border-left: 4px solid #dc2626;
            padding: 14px 18px;
            border-radius: 20px;
            margin-bottom: 28px;
            color: #991b1b;
            font-size: 14px;
            font-weight: 500;
        }

        .error-banner strong {
            display: block;
            margin-bottom: 6px;
            font-size: 14px;
        }

        /* Button */
        .login-btn {
            width: 100%;
            padding: 15px 18px;
            background: #003056;
            border: none;
            border-radius: 28px;
            color: white;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.25s ease;
            box-shadow: 0 4px 12px rgba(0, 48, 86, 0.25);
            letter-spacing: -0.2px;
        }

        .login-btn:hover {
            background: #002542;
            transform: scale(0.98);
            box-shadow: 0 6px 14px rgba(0, 48, 86, 0.3);
        }

        /* Responsive */
        @media (max-width: 500px) {
            .card-header {
                padding: 28px 24px;
            }
            .card-body {
                padding: 28px 24px;
            }
            .card-header h1 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    @php
        $errors = $errors ?? session()->get('errors', new \Illuminate\Support\ViewErrorBag());
    @endphp

    <div class="login-card">
        <div class="card-header">
            <h1>Selamat Datang</h1>
            <p>Masuk ke Sistem Informasi PKL<br>SMK Negeri 6 Malang</p>
        </div>

        <div class="card-body">
            @if ($errors->any())
                <div class="error-banner">
                    <strong>⚠️ Login gagal</strong>
                    <ul style="margin-top: 8px; margin-left: 20px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf

                <div class="form-group @error('username') error @enderror">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" value="{{ old('username') }}" required autofocus placeholder="Masukkan username Anda">
                    @error('username')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group @error('password') error @enderror">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required placeholder="••••••••">
                    @error('password')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="login-btn">Masuk ke Dashboard</button>
            </form>
        </div>
    </div>
</body>
</html>