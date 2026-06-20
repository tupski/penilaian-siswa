<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Register - Sistem Penilaian Siswa SMART</title>
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
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 15px;
            position: relative;
        }
        
        .card-register {
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.08);
            overflow: hidden;
            width: 100%;
            max-width: 400px;
            position: relative;
            z-index: 1;
            background: #ffffff;
            animation: fadeInUp 0.5s ease-out;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
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
            padding: 25px 20px;
            border: none;
        }
        
        /* Logo container */
        .logo-container {
            margin-bottom: 15px;
        }
        
        .logo-img {
            width: 70px;
            height: 70px;
            object-fit: contain;
            border-radius: 50%;
            background: white;
            padding: 8px;
        }
        
        .card-header h3 {
            margin: 0;
            font-weight: 600;
            font-size: 1.3rem;
        }
        
        .card-header p {
            margin: 5px 0 0;
            opacity: 0.9;
            font-size: 0.75rem;
        }
        
        .card-body {
            padding: 25px;
            background: white;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            font-size: 0.75rem;
            font-weight: 600;
            margin-bottom: 6px;
            display: block;
            color: #334155;
        }
        
        .form-control {
            border-radius: 12px;
            padding: 8px 12px;
            border: 1px solid #e2e8f0;
            transition: all 0.3s;
            font-size: 0.85rem;
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
            padding-right: 35px;
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
            font-size: 0.9rem;
            transition: color 0.3s;
        }
        
        .toggle-password:hover {
            color: #4a6fa5;
        }
        
        .btn-register {
            background: #4a6fa5;
            border: none;
            border-radius: 12px;
            padding: 10px;
            font-weight: 600;
            font-size: 0.85rem;
            width: 100%;
            color: white;
            transition: all 0.3s;
            margin-top: 5px;
        }
        
        .btn-register:hover {
            background: #3a5a8c;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(74,111,165,0.3);
        }
        
        .login-link {
            text-align: center;
            margin-top: 18px;
            padding-top: 12px;
            border-top: 1px solid #e2e8f0;
        }
        
        .login-link p {
            margin: 0;
            font-size: 0.75rem;
            color: #64748b;
        }
        
        .login-link a {
            color: #4a6fa5;
            text-decoration: none;
            font-weight: 600;
        }
        
        .login-link a:hover {
            text-decoration: underline;
            color: #3a5a8c;
        }
        
        .alert {
            border-radius: 12px;
            padding: 10px 15px;
            font-size: 0.75rem;
            margin-bottom: 18px;
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
        
        .alert ul {
            padding-left: 20px;
            margin-top: 5px;
            margin-bottom: 0;
        }
        
        .input-group-text {
            background: white;
            border-right: none;
            border-radius: 12px 0 0 12px;
            padding: 8px 12px;
            font-size: 0.85rem;
            border: 1px solid #e2e8f0;
            color: #94a3b8;
        }
        
        .input-group .form-control {
            border-left: none;
            border-radius: 0 12px 12px 0;
        }
        
        .close {
            font-size: 1.2rem;
            padding: 0 5px;
        }
        
        /* Responsive */
        @media (max-width: 480px) {
            body {
                padding: 10px;
            }
            
            .card-register {
                max-width: 100%;
            }
            
            .card-header {
                padding: 20px 15px;
            }
            
            .logo-img {
                width: 55px;
                height: 55px;
            }
            
            .card-header h3 {
                font-size: 1.1rem;
            }
            
            .card-body {
                padding: 20px;
            }
            
            .form-group {
                margin-bottom: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="card-register">
        <div class="card-header">
            <div class="logo-container">
                <img src="{{ asset('images/logo.png') }}" alt="Logo MIN 3 Tangerang" class="logo-img" onerror="this.style.display='none'">
            </div>
            <h3>Daftar Akun Baru</h3>
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

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle"></i> Terdapat kesalahan:
                    <ul class="mb-0 mt-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf
                <div class="form-group">
                    <label><i class="fas fa-user"></i> Nama Lengkap</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fas fa-user"></i>
                            </span>
                        </div>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" 
                               placeholder="Masukkan nama lengkap" required autofocus>
                    </div>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-envelope"></i> Email Address</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fas fa-envelope"></i>
                            </span>
                        </div>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" 
                               placeholder="Masukkan email aktif" required>
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
                                   placeholder="Minimal 6 karakter" required>
                        </div>
                        <i class="fas fa-eye toggle-password" id="togglePassword"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-check-circle"></i> Konfirmasi Password</label>
                    <div class="password-wrapper">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-check-circle"></i>
                                </span>
                            </div>
                            <input type="password" name="password_confirmation" id="passwordConfirm" class="form-control" 
                                   placeholder="Ulangi password" required>
                        </div>
                        <i class="fas fa-eye toggle-password" id="togglePasswordConfirm"></i>
                    </div>
                </div>

                <button type="submit" class="btn-register">
                    <i class="fas fa-user-plus"></i> Daftar
                </button>

                <div class="login-link">
                    <p>Sudah punya akun? <a href="{{ route('login') }}">Login Sekarang</a></p>
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
            
            // Toggle password for Password field
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
            
            // Toggle password for Confirm Password field
            const togglePasswordConfirm = document.querySelector('#togglePasswordConfirm');
            const passwordConfirm = document.querySelector('#passwordConfirm');
            
            if (togglePasswordConfirm && passwordConfirm) {
                togglePasswordConfirm.addEventListener('click', function() {
                    const type = passwordConfirm.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordConfirm.setAttribute('type', type);
                    this.classList.toggle('fa-eye');
                    this.classList.toggle('fa-eye-slash');
                });
            }
        });
    </script>
</body>
</html>