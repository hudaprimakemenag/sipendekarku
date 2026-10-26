<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>Masuk — SIPENDEKARKU</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ route('sipendekarku.styles') }}">
    <style>
        body.auth-page {
            margin: 0;
            min-height: 100vh;
            min-height: 100dvh;
            background: linear-gradient(135deg, #f0fdf4 0%, #f8fafc 50%, #ecfdf5 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: max(20px, env(safe-area-inset-top)) max(16px, env(safe-area-inset-right)) max(20px, env(safe-area-inset-bottom)) max(16px, env(safe-area-inset-left));
            box-sizing: border-box;
            position: relative;
            overflow-x: hidden;
        }

        .auth-bg-pattern {
            position: absolute;
            inset: 0;
            background-image: 
                radial-gradient(circle at 15% 15%, rgba(5, 150, 105, 0.09) 0%, transparent 45%),
                radial-gradient(circle at 85% 85%, rgba(245, 158, 11, 0.08) 0%, transparent 45%);
            pointer-events: none;
            z-index: 0;
        }

        .auth-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 480px;
            margin: auto;
        }

        .auth-header {
            text-align: center;
            margin-bottom: 24px;
        }

        .auth-brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 6px 16px 6px 10px;
            background: #ffffff;
            border: 1px solid rgba(5, 150, 105, 0.22);
            border-radius: var(--radius-full);
            box-shadow: 0 4px 14px rgba(5, 150, 105, 0.08);
            margin-bottom: 14px;
        }

        .auth-brand-logo {
            width: 28px;
            height: 28px;
            object-fit: contain;
            filter: drop-shadow(0 1px 2px rgba(0, 0, 0, 0.12));
        }

        .auth-brand-tag {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            color: var(--emerald-dark);
        }

        .auth-app-title {
            margin: 0 0 6px 0;
            font-size: 32px;
            font-weight: 850;
            letter-spacing: -0.02em;
            color: #0f172a;
            line-height: 1.15;
        }

        .auth-app-title .brand-accent {
            color: var(--emerald);
        }

        .auth-app-desc-pill {
            display: inline-block;
            margin: 4px auto 10px auto;
            padding: 6px 14px;
            font-size: 12px;
            font-weight: 700;
            color: #065f46;
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(5, 150, 105, 0.22);
            border-radius: 999px;
            line-height: 1.4;
            letter-spacing: 0.1px;
            box-shadow: 0 1px 3px rgba(5, 150, 105, 0.04);
        }

        .auth-subtitle {
            font-size: 13px;
            color: var(--slate-500);
            line-height: 1.5;
            margin: 0;
        }

        .auth-card {
            background: #ffffff;
            border: 1px solid var(--slate-200);
            border-radius: var(--radius-xl);
            padding: 32px 28px;
            box-shadow: 0 10px 30px -5px rgba(15, 23, 42, 0.06), 0 0 0 1px rgba(15, 23, 42, 0.02);
        }

        .alert-box {
            padding: 12px 14px;
            border-radius: 12px;
            font-size: 12px;
            line-height: 1.5;
            margin-bottom: 20px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .alert-box.success {
            background: var(--emerald-light);
            border: 1px solid var(--emerald-border);
            color: var(--emerald-dark);
        }

        .alert-box.danger {
            background: var(--rose-light);
            border: 1px solid var(--rose-border);
            color: var(--rose);
        }

        .alert-box-icon {
            font-size: 15px;
            flex-shrink: 0;
            line-height: 1;
            margin-top: 1px;
        }

        .auth-form-group {
            margin-bottom: 18px;
        }

        .auth-label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            color: var(--slate-700);
            margin-bottom: 7px;
            letter-spacing: -0.01em;
        }

        .auth-input-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }

        .auth-input-icon {
            position: absolute;
            left: 14px;
            color: var(--slate-400);
            font-size: 15px;
            pointer-events: none;
            display: grid;
            place-items: center;
        }

        .auth-input {
            width: 100%;
            height: 48px;
            padding: 10px 14px 10px 42px;
            background: var(--slate-50);
            border: 1.5px solid var(--slate-200);
            border-radius: var(--radius-md);
            font-family: inherit;
            font-size: 13.5px;
            font-weight: 500;
            color: var(--dark);
            box-sizing: border-box;
            transition: all 0.2s ease;
            outline: none;
        }

        .auth-input:focus {
            background: #ffffff;
            border-color: var(--emerald);
            box-shadow: 0 0 0 3px var(--emerald-focus);
        }

        .auth-input.is-invalid {
            border-color: var(--rose);
            background: var(--rose-light);
        }

        .auth-input.is-invalid:focus {
            box-shadow: 0 0 0 3px rgba(225, 29, 72, 0.15);
        }

        .auth-toggle-pwd {
            position: absolute;
            right: 8px;
            background: none;
            border: none;
            color: var(--slate-400);
            cursor: pointer;
            padding: 8px 10px;
            font-size: 15px;
            border-radius: 6px;
            display: grid;
            place-items: center;
            transition: color 0.2s;
        }

        .auth-toggle-pwd:hover {
            color: var(--slate-700);
        }

        .auth-error {
            display: block;
            font-size: 11px;
            font-weight: 600;
            color: var(--rose);
            margin-top: 5px;
        }

        .auth-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 22px;
        }

        .auth-remember {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: var(--slate-600);
            font-weight: 500;
            cursor: pointer;
            user-select: none;
        }

        .auth-checkbox {
            appearance: none;
            -webkit-appearance: none;
            width: 18px;
            height: 18px;
            border: 1.5px solid var(--slate-300);
            border-radius: 5px;
            margin: 0;
            background: #ffffff;
            cursor: pointer;
            position: relative;
            transition: all 0.15s ease;
            flex-shrink: 0;
        }

        .auth-checkbox:checked {
            background: var(--emerald);
            border-color: var(--emerald);
        }

        .auth-checkbox:checked::after {
            content: '✓';
            position: absolute;
            top: -1px;
            left: 3px;
            color: #ffffff;
            font-size: 12px;
            font-weight: 800;
        }

        .auth-btn-submit {
            width: 100%;
            height: 48px;
            background: var(--emerald);
            color: #ffffff;
            border: none;
            border-radius: var(--radius-md);
            font-family: inherit;
            font-size: 14px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(5, 150, 105, 0.28);
            transition: all 0.2s ease;
        }

        .auth-btn-submit:hover {
            background: var(--emerald-dark);
            box-shadow: 0 6px 18px rgba(5, 150, 105, 0.38);
            transform: translateY(-1px);
        }

        .auth-btn-submit:active {
            transform: translateY(0);
        }

        /* Demo Quick Access */
        .auth-demo-wrap {
            margin-top: 26px;
            padding-top: 20px;
            border-top: 1px dashed var(--slate-200);
        }

        .auth-demo-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .auth-demo-title {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--slate-500);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .auth-demo-hint {
            font-size: 10px;
            color: var(--slate-400);
        }

        .auth-demo-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
        }

        .auth-demo-chip {
            background: var(--slate-50);
            border: 1px solid var(--slate-200);
            border-radius: 10px;
            padding: 9px 10px;
            cursor: pointer;
            transition: all 0.2s ease;
            text-align: left;
            font-family: inherit;
            display: flex;
            flex-direction: column;
            gap: 2px;
            min-height: 48px;
            justify-content: center;
        }

        .auth-demo-chip:hover {
            background: var(--emerald-light);
            border-color: var(--emerald-border);
            transform: translateY(-1px);
        }

        .auth-demo-chip.active {
            background: var(--emerald-light);
            border-color: var(--emerald);
            box-shadow: 0 0 0 1px var(--emerald);
        }

        .auth-demo-chip-role {
            font-size: 11px;
            font-weight: 700;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .auth-demo-chip-email {
            font-size: 9.5px;
            color: var(--slate-500);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .auth-footer {
            text-align: center;
            margin-top: 24px;
            font-size: 11px;
            color: var(--slate-400);
            line-height: 1.6;
        }

        .auth-footer p {
            margin: 0;
        }

        .auth-quote {
            font-style: italic;
            color: var(--slate-500);
            margin-top: 4px;
        }

        /* Responsive Breakpoints for Auth Page */
        @media (max-width: 520px) {
            .auth-card {
                padding: 24px 18px;
                border-radius: var(--radius-lg);
            }

            .auth-app-title {
                font-size: 26px;
            }

            .auth-app-desc-pill {
                font-size: 11px;
                padding: 5px 12px;
            }

            .auth-demo-grid {
                grid-template-columns: 1fr 1fr;
                gap: 6px;
            }

            .auth-demo-chip {
                padding: 8px 9px;
            }

            .auth-input {
                font-size: 14px;
            }
        }

        @media (max-width: 360px) {
            .auth-demo-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body class="auth-page">
    <div class="auth-bg-pattern"></div>

    <div class="auth-container">
        <header class="auth-header">
            <div class="auth-brand-badge">
                <img src="{{ asset('images/kemenag.svg') }}" alt="Logo Kemenag" class="auth-brand-logo">
                <span class="auth-brand-tag">Kemenag Kab. Kulon Progo</span>
            </div>

            <h1 class="auth-app-title">
                SIPENDEKAR<span class="brand-accent">KU</span>
            </h1>

            <div class="auth-app-desc-pill">
                Sistem Informasi Pendidikan Karakter Anak Kulon Progo
            </div>

            <p class="auth-subtitle">Masuk ke akun Anda untuk mencatat dan memonitor pembiasaan karakter peserta didik</p>
        </header>

        <div class="auth-card">
            {{-- Flash Status Alert --}}
            @if (session('status'))
                <div class="alert-box success" role="alert">
                    <span class="alert-box-icon">✓</span>
                    <div>{{ session('status') }}</div>
                </div>
            @endif

            {{-- General Validation Error Alert --}}
            @if ($errors->any())
                <div class="alert-box danger" role="alert">
                    <span class="alert-box-icon">⚠</span>
                    <div>
                        @if ($errors->has('email'))
                            {{ $errors->first('email') }}
                        @elseif ($errors->has('password'))
                            {{ $errors->first('password') }}
                        @else
                            Periksa kembali formulir login Anda.
                        @endif
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" id="login-form">
                @csrf

                {{-- Email Field --}}
                <div class="auth-form-group">
                    <label for="email" class="auth-label">Alamat Email</label>
                    <div class="auth-input-wrap">
                        <span class="auth-input-icon">✉</span>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            class="auth-input @error('email') is-invalid @enderror" 
                            placeholder="nama@sekolah.id" 
                            value="{{ old('email') }}" 
                            required 
                            autofocus 
                            autocomplete="username"
                        >
                    </div>
                    @error('email')
                        <span class="auth-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Password Field --}}
                <div class="auth-form-group">
                    <label for="password" class="auth-label">Kata Sandi</label>
                    <div class="auth-input-wrap">
                        <span class="auth-input-icon">🔒</span>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="auth-input @error('password') is-invalid @enderror" 
                            placeholder="••••••••" 
                            required 
                            autocomplete="current-password"
                        >
                        <button type="button" class="auth-toggle-pwd" id="btn-toggle-password" aria-label="Tampilkan / sembunyikan kata sandi">
                            <span id="eye-icon">👁</span>
                        </button>
                    </div>
                    @error('password')
                        <span class="auth-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Remember Me Option --}}
                <div class="auth-options">
                    <label class="auth-remember">
                        <input type="checkbox" name="remember" id="remember" class="auth-checkbox" {{ old('remember') ? 'checked' : '' }}>
                        <span>Ingat saya di perangkat ini</span>
                    </label>
                </div>

                {{-- Submit Button --}}
                <button type="submit" class="auth-btn-submit" id="btn-submit">
                    <span>Masuk ke Aplikasi</span>
                    <span>→</span>
                </button>
            </form>

            {{-- Quick Demo Account Selector --}}
            <div class="auth-demo-wrap">
                <div class="auth-demo-header">
                    <span class="auth-demo-title">
                        <span>⚡</span> Akses Cepat Akun Demo
                    </span>
                    <span class="auth-demo-hint">Klik untuk isi otomatis</span>
                </div>
                <div class="auth-demo-grid">
                    <button type="button" class="auth-demo-chip" onclick="fillDemo('siswa@sekolah.id', 'password', this)">
                        <span class="auth-demo-chip-role">🎒 Siswa (Naila)</span>
                        <span class="auth-demo-chip-email">siswa@sekolah.id</span>
                    </button>
                    <button type="button" class="auth-demo-chip" onclick="fillDemo('guru@sekolah.id', 'password', this)">
                        <span class="auth-demo-chip-role">👨‍🏫 Guru (Ust. Ahmad)</span>
                        <span class="auth-demo-chip-email">guru@sekolah.id</span>
                    </button>
                    <button type="button" class="auth-demo-chip" onclick="fillDemo('wali@sekolah.id', 'password', this)">
                        <span class="auth-demo-chip-role">👨‍👩‍👧 Wali Murid</span>
                        <span class="auth-demo-chip-email">wali@sekolah.id</span>
                    </button>
                    <button type="button" class="auth-demo-chip" onclick="fillDemo('admin@sekolah.id', 'password', this)">
                        <span class="auth-demo-chip-role">🏫 Admin Sekolah</span>
                        <span class="auth-demo-chip-email">admin@sekolah.id</span>
                    </button>
                    <button type="button" class="auth-demo-chip" style="grid-column: 1 / -1;" onclick="fillDemo('superadmin@tarbiyah.id', 'password', this)">
                        <span class="auth-demo-chip-role">👑 Superadmin Global</span>
                        <span class="auth-demo-chip-email">superadmin@tarbiyah.id</span>
                    </button>
                </div>
            </div>
        </div>

        <footer class="auth-footer">
            <p>&copy; {{ date('Y') }} <strong>SIPENDEKARKU</strong> · Sistem Informasi Pendidikan Karakter Anak Kulon Progo</p>
            <p class="auth-quote">Kementerian Agama Kabupaten Kulon Progo</p>
        </footer>
    </div>

    <script>
        // Password toggle visibility
        const toggleBtn = document.getElementById('btn-toggle-password');
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eye-icon');

        if (toggleBtn && passwordInput) {
            toggleBtn.addEventListener('click', function() {
                const isPassword = passwordInput.getAttribute('type') === 'password';
                passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                eyeIcon.textContent = isPassword ? '🙈' : '👁';
            });
        }

        // Quick demo filler
        function fillDemo(email, password, element) {
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');

            if (emailInput && passwordInput) {
                emailInput.value = email;
                passwordInput.value = password;

                // Visual active state indicator on chips
                document.querySelectorAll('.auth-demo-chip').forEach(el => el.classList.remove('active'));
                if (element) {
                    element.classList.add('active');
                }

                emailInput.focus();
            }
        }
    </script>
</body>
</html>
