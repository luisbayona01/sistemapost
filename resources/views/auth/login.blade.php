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
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
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

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-8px);
            }
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
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
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
            animation: shake 0.5s cubic-bezier(.36, .07, .19, .97);
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            10%,
            30%,
            50%,
            70%,
            90% {
                transform: translateX(-5px);
            }

            20%,
            40%,
            60%,
            80% {
                transform: translateX(5px);
            }
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

<body class="min-h-screen flex flex-col overflow-y-auto">
    <!-- Animated Background Elements -->
    <div
        class="fixed top-0 left-0 w-96 h-96 bg-white opacity-5 rounded-full mix-blend-multiply filter blur-3xl animate-pulse pointer-events-none">
    </div>
    <div class="fixed bottom-0 right-0 w-96 h-96 bg-white opacity-5 rounded-full mix-blend-multiply filter blur-3xl animate-pulse pointer-events-none"
        style="animation-delay: 2s;"></div>

    <!-- Main Content -->
    <div class="flex-1 flex items-center justify-center px-4 py-4 relative z-10">
        <main class="w-full">
            <div class="max-w-sm mx-auto">

                <!-- Compact Header -->
                <div class="mb-5 text-center animate-in">
                    <div class="inline-flex items-center gap-3 mb-3">
                        <div
                            class="w-11 h-11 flex items-center justify-center bg-gradient-to-br from-purple-600 via-pink-500 to-blue-500 rounded-xl shadow-lg floating-icon">
                            <i class="fas fa-cash-register text-white text-lg"></i>
                        </div>
                        <h1 class="text-2xl font-black text-white tracking-tight">
                            <span
                                class="bg-clip-text text-transparent bg-gradient-to-r from-white via-blue-100 to-white">SaleHub</span>
                        </h1>
                    </div>
                    <p class="text-white/70 text-xs font-medium">Control Total de tu Negocio</p>
                </div>

                <!-- Login Card -->
                <div class="glass-effect rounded-2xl shadow-2xl overflow-hidden border border-white/30 animate-in"
                    style="animation-delay: 0.1s;">

                    <!-- Card Header -->
                    <div class="px-6 pt-6 pb-4 card-accent border-b border-gray-100">
                        <h2 class="text-xl font-bold text-gray-900 text-center mb-0.5">Bienvenido de vuelta</h2>
                        <p class="text-center text-gray-500 text-xs">Accede a tu cuenta para continuar</p>
                    </div>

                    <!-- Card Body -->
                    <div class="px-6 py-5">

                        @if ($errors->any())
                            <div id="errors-container" class="mb-4 space-y-2">
                                @foreach ($errors->all() as $error)
                                    <div class="error-badge bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-center gap-2"
                                        role="alert">
                                        <i class="fas fa-exclamation-triangle text-red-500 flex-shrink-0 text-sm"></i>
                                        <p class="text-xs font-medium flex-1">{{ $error }}</p>
                                        <button type="button" class="text-red-400 hover:text-red-600 flex-shrink-0"
                                            onclick="this.closest('[role=alert]').remove();">
                                            <i class="fas fa-times text-xs"></i>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <form action="{{ route('login.login') }}" method="POST" class="space-y-4" id="loginForm"
                            novalidate>
                            @csrf

                            <!-- Usuario -->
                            <div>
                                <label for="inputUsername"
                                    class="block text-xs font-semibold text-gray-700 mb-1.5 flex items-center gap-1.5">
                                    <i class="fas fa-user-circle text-purple-500 text-xs"></i>
                                    Usuario <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input autofocus autocomplete="username" value="{{ old('username', 'admin') }}"
                                        class="input-premium w-full px-4 py-3 rounded-xl outline-none text-gray-900 placeholder-gray-400 text-sm"
                                        name="username" id="inputUsername" type="text" placeholder="Nombre de usuario"
                                        required aria-label="Nombre de usuario" />
                                    @error('username')
                                        <span class="absolute right-3 top-3 text-red-500"><i
                                                class="fas fa-times-circle"></i></span>
                                    @enderror
                                </div>
                                @error('username')
                                    <p class="text-red-600 text-xs mt-1 flex items-center gap-1"><i
                                            class="fas fa-info-circle"></i> {{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Contraseña -->
                            <div>
                                <label for="inputPassword"
                                    class="block text-xs font-semibold text-gray-700 mb-1.5 flex items-center gap-1.5">
                                    <i class="fas fa-lock text-purple-500 text-xs"></i>
                                    Contraseña <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input
                                        class="input-premium w-full px-4 py-3 pr-11 rounded-xl outline-none text-gray-900 placeholder-gray-400 text-sm"
                                        name="password" id="inputPassword" type="password" placeholder="••••••••"
                                        required aria-label="Contraseña" />
                                    <button type="button" onclick="togglePasswordVisibility()"
                                        class="absolute right-3 top-3 text-gray-400 hover:text-purple-600 transition-colors"
                                        aria-label="Mostrar/ocultar contraseña">
                                        <i id="toggleIcon" class="fas fa-eye"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <p class="text-red-600 text-xs mt-1 flex items-center gap-1"><i
                                            class="fas fa-info-circle"></i> {{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Submit -->
                            <button type="submit"
                                class="btn-premium w-full mt-2 text-white font-bold py-3 px-6 rounded-xl transition-all duration-300 flex items-center justify-center gap-2 shadow-lg text-sm disabled:opacity-50 disabled:cursor-not-allowed"
                                id="submitBtn">
                                <i id="submitIcon" class="fas fa-arrow-right"></i>
                                <span id="submitText">Iniciar Sesión</span>
                            </button>
                        </form>

                        <!-- Demo credentials compact -->
                        <div
                            class="mt-4 bg-purple-50 border border-purple-100 rounded-xl px-4 py-3 flex items-center gap-3">
                            <i class="fas fa-info-circle text-purple-500 flex-shrink-0"></i>
                            <div class="text-xs text-gray-600">
                                <span class="font-semibold text-gray-800">Demo:</span>
                                usuario <code class="bg-purple-100 px-1 rounded font-mono">admin</code> /
                                clave <code class="bg-purple-100 px-1 rounded font-mono">12345678</code>
                            </div>
                        </div>
                    </div>

                    <!-- Footer del card -->
                    <div class="px-6 py-3 border-t border-gray-100 bg-gray-50/50 text-center">
                        <p class="text-xs text-gray-500">
                            ¿Problemas para acceder?
                            <a href="#"
                                class="text-purple-600 hover:text-purple-700 font-semibold transition-colors">Contacta
                                soporte</a>
                        </p>
                    </div>
                </div>

                <!-- Trust indicators compact -->
                <div class="mt-4 flex items-center justify-center gap-4 text-white/60 text-[10px] font-medium">
                    <div class="flex items-center gap-1"><i class="fas fa-shield-alt text-green-400"></i> SSL Seguro
                    </div>
                    <span class="text-white/30">•</span>
                    <div class="flex items-center gap-1"><i class="fas fa-lock text-green-400"></i> Encriptado</div>
                    <span class="text-white/30">•</span>
                    <div class="flex items-center gap-1"><i class="fas fa-check-circle text-green-400"></i> Verificado
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
        document.getElementById('loginForm').addEventListener('submit', function (e) {
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
        document.addEventListener('keydown', function (e) {
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