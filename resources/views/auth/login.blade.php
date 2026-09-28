<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SI-PELITA RS</title>
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 10px 10px -5px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            max-width: 420px;
            width: 100%;
        }

        .login-header {
            background-color: #ffffff;
            padding: 2.5rem 2rem 1.5rem;
            text-align: center;
        }

        .brand-icon {
            width: 60px;
            height: 60px;
            background-color: #eff6ff;
            color: #2563eb;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            font-size: 1.75rem;
        }

        .login-body {
            background-color: #ffffff;
            padding: 0 2rem 2.5rem;
        }

        .form-control:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 0.25rem rgba(37, 99, 235, 0.15);
        }

        .btn-primary-custom {
            background-color: #2563eb;
            border: none;
            padding: 0.75rem;
            font-weight: 600;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
        }

        .btn-primary-custom:hover {
            background-color: #1d4ed8;
        }
    </style>
</head>
<body>

<div class="container px-3">
    <div class="login-card mx-auto">
        <!-- Card Header / Logo -->
        <div class="login-header">
            <div class="brand-icon shadow-sm">
                <i class="bi bi-hospital"></i>
            </div>
            <h4 class="fw-bold text-dark mb-1">SI-PELITA</h4>
            <p class="text-muted small mb-0">Sistem Pelaporan & Informasi Terpadu Antar-Unit RS</p>
        </div>

        <!-- Card Body / Form -->
        <div class="login-body">
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 small" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf

                <!-- Email Input -->
                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold small text-secondary">Email Pengguna / NIP</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-secondary border-end-0"><i class="bi bi-envelope"></i></span>
                        <input type="email" class="form-control bg-light border-start-0 @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="contoh: admin@sipelita.id" required autofocus>
                    </div>
                    @error('email')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Password Input -->
                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold small text-secondary">Kata Sandi</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-secondary border-end-0"><i class="bi bi-lock"></i></span>
                        <input type="password" class="form-control bg-light border-start-0 @error('password') is-invalid @enderror" id="password" name="password" placeholder="••••••••" required>
                    </div>
                    @error('password')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="remember" name="remember">
                        <label class="form-check-label small text-secondary" for="remember">
                            Ingat Saya
                        </label>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary btn-primary-custom w-100 text-white shadow-sm">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Masuk Aplikasi
                </button>
            </form>
        </div>
    </div>

    <!-- Footer Note -->
    <div class="text-center mt-4">
        <small class="text-light opacity-50">&copy; {{ date('Y') }} RS SIMRS — Hak Cipta Dilindungi</small>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
