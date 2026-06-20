<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Sistem Penilaian Siswa SMART</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: #f0f4f8;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
            position: relative;
        }
        
        .card-login {
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.08);
            overflow: hidden;
            width: 100%;
            max-width: 400px;
            position: relative;
            z-index: 1;
            background: #ffffff;
            animation: fadeInUp 0.6s ease-out;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .card-header {
            background: #4a6fa5;
            color: white;
            text-align: center;
            padding: 30px 20px;
            border: none;
        }
        
        /* Logo container */
        .logo-container {
            margin-bottom: 15px;
        }
        
        .logo-img {
            width: 80px;
            height: 80px;
            object-fit: contain;
            border-radius: 50%;
            background: white;
            padding: 8px;
        }
        
        .card-header h3 {
            margin: 0;
            font-weight: 600;
            font-size: 1.5rem;
        }
        
        .card-header p {
            margin: 8px 0 0;
            opacity: 0.9;
            font-size: 0.8rem;
        }
        
        .card-body {
            padding: 30px 25px;
            background: white;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 8px;
            display: block;
            color: #334155;
        }
        
        .form-control {
            border-radius: 12px;
            padding: 10px 15px;
            border: 1px solid #e2e8f0;
            transition: all 0.3s;
            font-size: 0.9rem;
            height: auto;
            background: #ffffff;
        }
        
        .form-control:focus {
            border-color: #4a6fa5;
            box-shadow: 0 0 0 3px rgba(74,111,165,0.15);
            outline: none;
        }
        
        /* Password wrapper */
        .password-wrapper {
            position: relative;
        }
        
        .password-wrapper input {
            padding-right: 40px;
        }
        
        .toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #94a3b8;
            z-index: 10;
            background: white;
            padding: 0;
            font-size: 1rem;
            transition: color 0.3s;
        }
        
        .toggle-password:hover {
            color: #4a6fa5;
        }
        
        .btn-login {
            background: #4a6fa5;
            border: none;
            border-radius: 12px;
            padding: 12px;
            font-weight: 600;
            font-size: 0.9rem;
            width: 100%;
            color: white;
            transition: all 0.3s;
        }
        
        .btn-login:hover {
            background: #3a5a8c;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(74,111,165,0.3);
        }
        
        .register-link {
            text-align: center;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
        }
        
        .register-link p {
            margin: 0;
            font-size: 0.8rem;
            color: #64748b;
        }
        
        .register-link a {
            color: #4a6fa5;
            text-decoration: none;
            font-weight: 600;
        }
        
        .register-link a:hover {
            text-decoration: underline;
            color: #3a5a8c;
        }
        
        .custom-control-label {
            font-size: 0.8rem;
            color: #64748b;
        }
        
        .custom-control-input:checked ~ .custom-control-label::before {
            background-color: #4a6fa5;
            border-color: #4a6fa5;
        }
        
        .alert {
            border-radius: 12px;
            padding: 10px 15px;
            font-size: 0.8rem;
            margin-bottom: 20px;
            border: none;
        }
        
        .alert-success {
            background: #e6f7ec;
            color: #1e6f3f;
        }
        
        .alert-danger {
            background: #ffe8e8;
            color: #a03a3a;
        }
        
        .input-group-text {
            background: white;
            border-right: none;
            border-radius: 12px 0 0 12px;
            padding: 10px 12px;
            font-size: 0.9rem;
            border: 1px solid #e2e8f0;
            color: #94a3b8;
        }
        
        .input-group .form-control {
            border-left: none;
            border-radius: 0 12px 12px 0;
        }
        
        /* Responsive */
        @media (max-width: 480px) {
            .card-login {
                max-width: 100%;
            }
            
            .card-header {
                padding: 25px 15px;
            }
            
            .logo-img {
                width: 60px;
                height: 60px;
            }
            
            .card-header h3 {
                font-size: 1.3rem;
            }
            
            .card-body {
                padding: 25px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="card-login">
        <div class="card-header">
            <div class="logo-container">
                <img src="{{ asset('images/logo.png') }}" alt="Logo MIN 3 Tangerang" class="logo-img" onerror="this.style.display='none'">
            </div>
            <h3>Sistem Penilaian Siswa</h3>
            <p>MIN 3 Tangerang</p>
        </div>
        <div class="card-body">
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle"></i> 
                    @foreach($errors->all() as $error)
                        {{ $error }}
                    @endforeach
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="form-group">
                    <label><i class="fas fa-envelope"></i> Email Address</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fas fa-envelope"></i>
                            </span>
                        </div>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" 
                               placeholder="Masukkan email Anda" required autofocus>
                    </div>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-lock"></i> Password</label>
                    <div class="password-wrapper">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-lock"></i>
                                </span>
                            </div>
                            <input type="password" name="password" id="password" class="form-control" 
                                   placeholder="Masukkan password" required>
                        </div>
                        <i class="fas fa-eye toggle-password" id="togglePassword"></i>
                    </div>
                </div>

                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="remember" name="remember">
                        <label class="custom-control-label" for="remember">Ingat Saya</label>
                    </div>
                    <div class="text-right mt-2">
                        <a href="{{ route('password.request') }}" class="text-muted small">
                            <i class="fas fa-key"></i> Lupa Password?
                        </a>
                    </div>
                </div>

                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>

                <div class="register-link">
                    <p>Belum punya akun? <a href="{{ route('register') }}">Daftar Sekarang</a></p>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        $(document).ready(function() {
            setTimeout(function() {
                $(".alert").fadeOut("slow");
            }, 5000);
            
            const togglePassword = document.querySelector('#togglePassword');
            const password = document.querySelector('#password');
            
            if (togglePassword && password) {
                togglePassword.addEventListener('click', function() {
                    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                    password.setAttribute('type', type);
                    this.classList.toggle('fa-eye');
                    this.classList.toggle('fa-eye-slash');
                });
            }
        });
    </script>
</body>
</html>