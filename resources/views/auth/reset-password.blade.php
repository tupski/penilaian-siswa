<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Sistem Penilaian Siswa SMART</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .card-reset {
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            overflow: hidden;
            width: 100%;
            max-width: 450px;
            background: white;
        }
        
        .card-header {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            text-align: center;
            padding: 30px;
            border: none;
        }
        
        .card-body {
            padding: 40px;
        }
        
        .btn-reset {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: bold;
            width: 100%;
            color: white;
        }
        
        .btn-reset:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(30, 60, 114, 0.4);
        }
        
        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
        }
        
        .input-group-text {
            border-radius: 10px 0 0 10px;
        }
        
        .form-control {
            border-radius: 0 10px 10px 0;
        }
    </style>
</head>
<body>
    <div class="card-reset">
        <div class="card-header">
            <i class="fas fa-lock fa-3x mb-3"></i>
            <h3>Reset Password</h3>
            <p>Buat password baru untuk akun Anda</p>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="form-group">
                    <label>Email Address</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fas fa-envelope"></i>
                            </span>
                        </div>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                               value="{{ $email ?? old('email') }}" required autofocus>
                    </div>
                    @error('email')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Password Baru</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fas fa-lock"></i>
                            </span>
                        </div>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                               required>
                    </div>
                    @error('password')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Konfirmasi Password Baru</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fas fa-lock"></i>
                            </span>
                        </div>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                </div>

                <button type="submit" class="btn-reset">
                    <i class="fas fa-save"></i> Reset Password
                </button>

                <div class="text-center mt-3">
                    <a href="{{ route('login') }}" class="text-muted">
                        <i class="fas fa-arrow-left"></i> Kembali ke Login
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>