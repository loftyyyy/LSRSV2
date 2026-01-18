<!doctype html>
<html lang="en" class="transition-colors duration-300">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login · Love &amp; Styles</title>

    {{-- Favicon --}}
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">

    {{-- Fonts --}}
    <link
        rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Geist:wght@400;500;600;700&family=Geist+Mono:wght@400;500&display=swap"
    >

    {{-- App styles --}}
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>

<body class="min-h-screen flex flex-col justify-center items-center font-geist bg-neutral-100 text-neutral-900 dark:bg-black dark:text-neutral-50 transition-colors duration-300 ease-in-out px-4 py-8">

{{-- Theme Toggle --}}
<div class="absolute top-6 right-6">
    <button
        type="button"
        onclick="toggleDarkMode()"
        class="flex items-center gap-2 rounded-xl px-3 py-2 text-xs font-medium text-neutral-700 dark:text-neutral-200 bg-white/80 dark:bg-black/80 border border-neutral-200 dark:border-neutral-800 hover:bg-neutral-100 dark:hover:bg-neutral-900 transition-all duration-200 backdrop-blur-sm"
    >
            <span class="inline-flex h-5 w-8 items-center rounded-full bg-amber-500/20 dark:bg-violet-600/20 px-0.5 relative transition-all duration-300">
                <span
                    id="toggleKnob"
                    class="flex h-4 w-4 items-center justify-center rounded-full bg-amber-400 dark:bg-violet-500 shadow-sm text-black dark:text-white transition-all duration-300 ease-in-out"
                >
                    <span id="iconMoon">
                        <x-icon name="moon" class="h-2.5 w-2.5" />
                    </span>
                    <span id="iconSun" class="hidden">
                        <x-icon name="sun" class="h-2.5 w-2.5" />
                    </span>
                </span>
            </span>
        <span id="modeLabel" class="text-[11px]">Dark</span>
    </button>
</div>

{{-- Login Card --}}
<div class="w-full max-w-md bg-white dark:bg-gradient-to-b dark:from-zinc-900 dark:to-black border border-neutral-200 dark:border-zinc-800 rounded-2xl p-6 sm:p-8 shadow-xl">

    <h2 class="text-xl font-semibold mb-1">Welcome Back</h2>
    <p class="text-sm text-neutral-600 dark:text-gray-400 mb-6">
        Sign in to access your rental management dashboard
    </p>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        {{-- Email --}}
        <div class="mb-4">
            <label class="block mb-2 text-sm">Email Address</label>
            <div class="flex items-center bg-neutral-50 dark:bg-black border rounded-lg px-3">
                <x-icon name="mail" class="text-neutral-400 mr-2" />
                <input
                    type="email"
                    name="email"
                    required
                    placeholder="your@business.com"
                    class="w-full bg-transparent py-3 text-sm focus:outline-none"
                />
            </div>
        </div>

        {{-- Password --}}
        <div class="mb-2">
            <label class="block mb-2 text-sm">Password</label>
            <div class="flex items-center bg-neutral-50 dark:bg-black border rounded-lg px-3">
                <x-icon name="lock" class="text-neutral-400 mr-2" />
                <input
                    id="passwordInput"
                    type="password"
                    name="password"
                    required
                    placeholder="••••••••"
                    class="w-full bg-transparent py-3 text-sm focus:outline-none"
                />
                <button type="button" onclick="togglePassword()" class="ml-2">
                        <span id="eyeOpen">
                            <x-icon name="eye" class="h-4 w-4" />
                        </span>
                    <span id="eyeClosed" class="hidden">
                            <x-icon name="eye-off" class="h-4 w-4" />
                        </span>
                </button>
            </div>
        </div>

        {{-- Forgot --}}
        <div class="text-right mb-6">
            <button type="button" onclick="showForgotPasswordModal()" class="text-sm text-violet-600 hover:underline">
                Forgot password?
            </button>
        </div>

        <button type="submit" class="w-full bg-violet-600 hover:bg-violet-700 rounded-lg py-3 text-white font-medium">
            Sign In
        </button>
    </form>

    <hr class="my-6">

    <p class="text-center text-sm text-neutral-600">
        Don't have access?
        <span class="text-neutral-800">Contact your system administrator</span>
    </p>
</div>

{{-- Forgot Password Modal --}}
<div id="forgotPasswordModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center">
    <div class="bg-white dark:bg-zinc-900 rounded-2xl p-6 w-full max-w-md">
        <div class="flex justify-between mb-4">
            <h3 class="text-lg font-semibold">Reset your password</h3>
            <button onclick="hideForgotPasswordModal()">×</button>
        </div>

        <input
            id="forgotPasswordEmail"
            type="email"
            placeholder="your@business.com"
            class="w-full mb-1 px-3 py-3 border border-gray-300 rounded-lg transition-colors duration-200"
        />

        <!-- Error message goes here -->
        <p id="forgotPasswordEmailError" class="text-red-500 text-[11px] mb-4 hidden"></p>

        <div class="flex gap-3">
            <button class="flex-1 bg-violet-600 text-white py-3 rounded-lg" onclick="generateOtp()">
                Send Reset Link
            </button>
            <button onclick="hideForgotPasswordModal()" class="px-4 py-3 border rounded-lg">
                Cancel
            </button>
        </div>
    </div>
</div>

{{-- Scripts --}}
<script>
    let isDarkMode = localStorage.getItem('darkMode') !== 'false';

    if (isDarkMode) {
        document.documentElement.classList.add('dark');
    }

    function toggleDarkMode() {
        isDarkMode = !isDarkMode;
        localStorage.setItem('darkMode', isDarkMode);
        document.documentElement.classList.toggle('dark', isDarkMode);

        document.getElementById('modeLabel').textContent = isDarkMode ? 'Dark' : 'Light';
        document.getElementById('iconMoon').classList.toggle('hidden', !isDarkMode);
        document.getElementById('iconSun').classList.toggle('hidden', isDarkMode);

        document.getElementById('toggleKnob').style.transform =
            isDarkMode ? 'translateX(12px)' : 'translateX(0)';
    }

    function togglePassword() {
        const input = document.getElementById('passwordInput');
        document.getElementById('eyeOpen').classList.toggle('hidden', input.type === 'text');
        document.getElementById('eyeClosed').classList.toggle('hidden', input.type === 'password');
        input.type = input.type === 'password' ? 'text' : 'password';
    }

    function showForgotPasswordModal() {
        document.getElementById('forgotPasswordModal').classList.remove('hidden');
    }

    function hideForgotPasswordModal() {
        document.getElementById('forgotPasswordModal').classList.add('hidden');
    }

    function showMessage(type, error){
        const errorText = document.getElementById('forgotPasswordEmailError');
        const forgotEmailInput = document.getElementById('forgotPasswordEmail');

        if(type === "failed"){
            errorText.textContent = error;
            errorText.classList.remove("hidden");

            forgotEmailInput.classList.add("border-red-500");
            forgotEmailInput.classList.remove("border-gray-300");

        }else{
            errorText.classList.add("hidden");
            forgotEmailInput.classList.remove("border-red-500");
            forgotEmailInput.classList.add("border-gray-300");
        }

    }

    function verifyEmail(email) {
        if (!email) {
            showMessage("failed", "Email cannot be empty");
        }

        // Trim spaces
        email = email.trim();

        // Regular expression for email validation
        const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

        if (!emailRegex.test(email)) {
            showMessage("failed", "Please enter a valid email address.");
        }

        // Extra precaution: no consecutive dots in local or domain part
        const [local, domain] = email.split("@");
        if (local.includes("..") || domain.includes("..")) {
            showMessage("failed", "Email contains invalid consecutive dots.");
        }

        showMessage('success', 'Email is valid')
        return { valid: true, message: "Email is valid." };
    }

    function generateOtp(){
        const email = document.getElementById('forgotPasswordEmail').value;
        if(!verifyEmail(email).valid){
            showMessage('failed', "Something went wrong. Please Try Again")
        }

        axios.post('otp/generate-otp', {email})
    }
</script>
</body>
</html>
