<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Inicio de sesión del sistema" />
    <meta name="author" content="SakCode" />
    <title>Iniciar Sesión - Sistema de Ventas</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 25%, #f093fb 50%, #4facfe 100%);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-8px); }
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .glass-effect-light {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.15);
        }

        .animate-in {
            animation: slideInUp 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .input-premium {
            background: rgba(255, 255, 255, 0.85);
            border: 2px solid rgba(102, 126, 234, 0.2);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .input-premium:focus {
            background: rgba(255, 255, 255, 1);
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .btn-premium {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 4px 15px 0 rgba(102, 126, 234, 0.4);
            position: relative;
            overflow: hidden;
        }

        .btn-premium::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s;
        }

        .btn-premium:hover::before {
            left: 100%;
        }

        .btn-premium:hover {
            box-shadow: 0 6px 20px 0 rgba(102, 126, 234, 0.6);
            transform: translateY(-2px);
        }

        .btn-premium:active {
            transform: translateY(0);
        }

        .error-badge {
            animation: shake 0.5s cubic-bezier(.36,.07,.19,.97);
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }

        .floating-icon {
            animation: float 3s ease-in-out infinite;
        }

        .card-accent {
            position: relative;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(240, 147, 251, 0.1) 100%);
        }
    </style>
</head>

<body class="min-h-screen flex flex-col overflow-hidden">
    <!-- Animated Background Elements -->
    <div class="absolute top-0 left-0 w-96 h-96 bg-white opacity-5 rounded-full mix-blend-multiply filter blur-3xl animate-pulse"></div>
    <div class="absolute bottom-0 right-0 w-96 h-96 bg-white opacity-5 rounded-full mix-blend-multiply filter blur-3xl animate-pulse" style="animation-delay: 2s;"></div>

    <!-- Main Content -->
    <div class="flex-1 flex items-center justify-center px-4 py-8 relative z-10">
        <main class="w-full">
            <div class="max-w-md mx-auto">
                <!-- Premium Header Section -->
                <div class="mb-12 text-center animate-in">
                    <!-- Floating Logo -->
                    <div class="inline-block bg-gradient-to-br from-white to-blue-50 rounded-2xl p-4 mb-6 shadow-2xl floating-icon border border-white/20">
                        <div class="w-16 h-16 flex items-center justify-center bg-gradient-to-br from-purple-600 via-pink-500 to-blue-500 rounded-xl">
                            <i class="fas fa-shopping-cart text-white text-3xl"></i>
                        </div>
                    </div>

                    <!-- Brand Text -->
                    <h1 class="text-4xl font-black text-white mb-3 tracking-tight">
                        <span class="bg-clip-text text-transparent bg-gradient-to-r from-white via-blue-100 to-white">
                            SaleHub
                        </span>
                    </h1>
                    <p class="text-white/80 text-base font-medium mb-1">Control Total de tu Negocio</p>
                    <p class="text-white/60 text-sm">Acelera tus ventas y automatiza procesos</p>
                </div>

                <!-- Premium Login Card -->
                <div class="glass-effect rounded-3xl shadow-2xl overflow-hidden border-b border-white/30 animate-in" style="animation-delay: 0.1s;">
                    <!-- Card Header with Gradient -->
                    <div class="relative px-8 pt-10 pb-8 card-accent border-b border-white/10">
                        <div class="absolute inset-0 opacity-50"></div>
                        <div class="relative">
                            <h2 class="text-3xl font-bold text-gray-900 text-center mb-2">Bienvenido de vuelta</h2>
                            <p class="text-center text-gray-600 text-sm">Accede a tu cuenta para continuar</p>
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="px-8 py-10">
                        <!-- Error Messages with Premium Styling -->
                        @if ($errors->any())
                            <div id="errors-container" class="mb-8 space-y-3">
                                @foreach ($errors->all() as $error)
                                    <div class="error-badge bg-gradient-to-r from-red-500/10 to-pink-500/10 border border-red-200/50 backdrop-blur-sm text-red-700 px-5 py-4 rounded-xl flex items-start gap-3" role="alert">
                                        <i class="fas fa-exclamation-triangle text-red-500 mt-0.5 flex-shrink-0 text-lg"></i>
                                        <div class="flex-1">
                                            <p class="text-sm font-medium">{{ $error }}</p>
                                        </div>
                                        <button
                                            type="button"
                                            class="text-red-400 hover:text-red-600 flex-shrink-0 transition-colors"
                                            onclick="this.closest('[role=alert]').remove();"
                                            aria-label="Cerrar mensaje"
                                        >
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <!-- Premium Login Form -->
                        <form action="{{ route('login.login') }}" method="POST" class="space-y-6" id="loginForm" novalidate>
                            @csrf

                            <!-- Email Field -->
                            <div class="group">
                                <label for="inputEmail" class="block text-sm font-semibold text-gray-800 mb-3 flex items-center gap-2">
                                    <i class="fas fa-at text-purple-600"></i>
                                    Correo Electrónico
                                    <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input
                                        autofocus
                                        autocomplete="email"
                                        value="{{ old('email', 'invitado@gmail.com') }}"
                                        class="input-premium w-full px-5 py-4 rounded-xl outline-none text-gray-900 placeholder-gray-400 text-base"
                                        name="email"
                                        id="inputEmail"
                                        type="email"
                                        placeholder="tu@ejemplo.com"
                                        required
                                        aria-label="Correo electrónico"
                                        aria-required="true"
                                        @error('email') aria-invalid="true" @enderror
                                    />
                                    @error('email')
                                        <span class="absolute right-4 top-4 text-red-500 text-lg">
                                            <i class="fas fa-times-circle"></i>
                                        </span>
                                    @enderror
                                </div>
                                @error('email')
                                    <p class="text-red-600 text-xs mt-2 flex items-center gap-1 font-medium">
                                        <i class="fas fa-info-circle"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Password Field -->
                            <div class="group">
                                <label for="inputPassword" class="block text-sm font-semibold text-gray-800 mb-3 flex items-center gap-2">
                                    <i class="fas fa-lock text-purple-600"></i>
                                    Contraseña
                                    <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input
                                        class="input-premium w-full px-5 py-4 pr-12 rounded-xl outline-none text-gray-900 placeholder-gray-400 text-base"
                                        name="password"
                                        value="{{ old('password', '12345678') }}"
                                        id="inputPassword"
                                        type="password"
                                        placeholder="••••••••"
                                        required
                                        aria-label="Contraseña"
                                        aria-required="true"
                                        @error('password') aria-invalid="true" @enderror
                                    />
                                    <button
                                        type="button"
                                        onclick="togglePasswordVisibility()"
                                        class="absolute right-4 top-4 text-gray-500 hover:text-purple-600 transition-all duration-200 text-lg"
                                        aria-label="Mostrar/ocultar contraseña"
                                        tabindex="0"
                                    >
                                        <i id="toggleIcon" class="fas fa-eye"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <p class="text-red-600 text-xs mt-2 flex items-center gap-1 font-medium">
                                        <i class="fas fa-info-circle"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Premium Submit Button -->
                            <button
                                type="submit"
                                class="btn-premium w-full mt-10 text-white font-bold py-4 px-6 rounded-xl transition-all duration-300 flex items-center justify-center gap-3 shadow-lg text-base disabled:opacity-50 disabled:cursor-not-allowed"
                                id="submitBtn"
                            >
                                <i id="submitIcon" class="fas fa-arrow-right"></i>
                                <span id="submitText">Iniciar Sesión</span>
                            </button>
                        </form>

                        <!-- Divider with Gradient -->
                        <div class="mt-8 flex items-center gap-4">
                            <div class="flex-1 h-px bg-gradient-to-r from-transparent via-gray-300 to-transparent"></div>
                            <span class="text-gray-500 text-xs font-medium tracking-wide">O</span>
                            <div class="flex-1 h-px bg-gradient-to-r from-transparent via-gray-300 to-transparent"></div>
                        </div>

                        <!-- Premium Demo Info Card -->
                        <div class="mt-8 card-accent border border-white/20 rounded-2xl p-6 backdrop-blur-sm">
                            <div class="flex items-start gap-3">
                                <i class="fas fa-sparkles text-purple-600 text-lg mt-0.5 flex-shrink-0"></i>
                                <div>
                                    <p class="text-sm font-bold text-gray-800 mb-3">Acceso de Demostración</p>
                                    <ul class="text-xs text-gray-700 space-y-2 font-medium">
                                        <li class="flex items-center gap-2">
                                            <span class="w-1.5 h-1.5 rounded-full bg-purple-600"></span>
                                            <span><strong>Email:</strong> invitado@gmail.com</span>
                                        </li>
                                        <li class="flex items-center gap-2">
                                            <span class="w-1.5 h-1.5 rounded-full bg-purple-600"></span>
                                            <span><strong>Pass:</strong> 12345678</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Premium Footer -->
                    <div class="px-8 py-5 border-t border-white/10 bg-white/30 backdrop-blur-sm text-center">
                        <p class="text-sm text-gray-700">
                            ¿Problemas para acceder?
                            <a href="#" class="text-purple-600 hover:text-purple-700 font-bold transition-colors underline">
                                Contacta soporte
                            </a>
                        </p>
                    </div>
                </div>

                <!-- Security & Trust Indicators -->
                <div class="mt-8 flex items-center justify-center gap-6 text-white/70 text-xs font-medium">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-shield-alt text-green-400"></i>
                        SSL Seguro
                    </div>
                    <span class="text-white/30">•</span>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-lock text-green-400"></i>
                        Datos Encriptados
                    </div>
                    <span class="text-white/30">•</span>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-check-circle text-green-400"></i>
                        Verificado
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Premium Footer -->
    <footer class="glass-effect-light border-t border-white/10 py-5 relative z-10">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex flex-col sm:flex-row items-center justify-between text-xs text-white/70 gap-4">
                <p class="font-medium">&copy; 2026 SaleHub. Todos los derechos reservados.</p>
                <div class="flex gap-6">
                    <a href="#" class="hover:text-white transition-colors duration-200 hover:underline">Privacidad</a>
                    <span class="text-white/30">•</span>
                    <a href="#" class="hover:text-white transition-colors duration-200 hover:underline">Términos</a>
                    <span class="text-white/30">•</span>
                    <a href="#" class="hover:text-white transition-colors duration-200 hover:underline">Soporte</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Loading State Script -->
    <script>
        function togglePasswordVisibility() {
            const input = document.getElementById('inputPassword');
            const icon = document.getElementById('toggleIcon');

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Premium Form submission feedback
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            const submitIcon = document.getElementById('submitIcon');
            const submitText = document.getElementById('submitText');

            submitBtn.disabled = true;
            submitIcon.classList.remove('fa-arrow-right');
            submitIcon.classList.add('fa-spinner', 'fa-spin');
            submitText.textContent = 'Autenticando...';
        });

        // Auto-remove error messages with smooth animation
        setTimeout(() => {
            const errors = document.querySelectorAll('[role="alert"]');
            errors.forEach(error => {
                setTimeout(() => {
                    error.style.animation = 'fadeOutSlide 0.5s cubic-bezier(0.4, 0, 1, 1) forwards';
                    setTimeout(() => error.remove(), 500);
                }, 4500);
            });
        }, 100);

        // Enhanced keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !document.getElementById('submitBtn').disabled) {
                document.getElementById('loginForm').submit();
            }
            if (e.altKey && e.key === 'l') {
                e.preventDefault();
                document.getElementById('inputEmail').focus();
            }
            if (e.altKey && e.key === 'p') {
                e.preventDefault();
                document.getElementById('inputPassword').focus();
            }
        });

        // Add fade-out animation for error messages
        const fadeOutStyle = document.createElement('style');
        fadeOutStyle.textContent = `
            @keyframes fadeOutSlide {
                to {
                    opacity: 0;
                    transform: translateX(-20px);
                }
            }
        `;
        document.head.appendChild(fadeOutStyle);

        // Parallax effect for background elements (optional enhancement)
        document.addEventListener('mousemove', (e) => {
            const elements = document.querySelectorAll('[style*="opacity-5"]');
            elements.forEach((el, index) => {
                const speed = (index + 1) * 0.5;
                const x = (window.innerWidth - e.clientX * speed) / 100;
                const y = (window.innerHeight - e.clientY * speed) / 100;
                el.style.transform = `translate(${x}px, ${y}px)`;
            });
        });
    </script>
</body>

</html>
