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
                    class="w-full bg-transparent py-3 text-sm
           text-neutral-900 dark:text-neutral-100
           placeholder-neutral-400 dark:placeholder-neutral-500
           focus:outline-none"
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
                    class="w-full bg-transparent py-3 text-sm
           text-neutral-900 dark:text-neutral-100
           placeholder-neutral-400 dark:placeholder-neutral-500
           focus:outline-none"
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
<div id="forgotPasswordModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-zinc-900 rounded-2xl p-6 w-full max-w-md shadow-2xl">

        {{-- Step 1: Email Entry --}}
        <div id="step1" class="step-content">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h3 class="text-lg font-semibold">Reset your password</h3>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">Step 1 of 3</p>
                </div>
                <button onclick="closeModal()" class="text-2xl text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-200">×</button>
            </div>

            <p class="text-sm text-neutral-600 dark:text-neutral-400 mb-4">
                Enter your email address and we'll send you a verification code.
            </p>

            <input
                id="forgotPasswordEmail"
                type="email"
                placeholder="your@business.com"
                class="w-full mb-1 px-3 py-3 border rounded-lg
                       bg-white dark:bg-zinc-900
                       text-neutral-900 dark:text-neutral-100
                       placeholder-neutral-400 dark:placeholder-neutral-500
                       transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-violet-500"
            />

            <p id="step1Error" class="text-red-500 text-xs mb-4 min-h-[16px]"></p>

            <div class="flex gap-3">
                <button
                    id="sendOtpBtn"
                    class="flex-1 bg-violet-600 hover:bg-violet-700 disabled:bg-neutral-300 disabled:cursor-not-allowed text-white py-3 rounded-lg font-medium transition-colors duration-200"
                    onclick="generateOtp()"
                >
                    Send Code
                </button>
                <button onclick="closeModal()" class="px-4 py-3 border rounded-lg hover:bg-neutral-50 dark:hover:bg-neutral-800 transition-colors duration-200">
                    Cancel
                </button>
            </div>
        </div>

        {{-- Step 2: OTP Entry --}}
        <div id="step2" class="step-content hidden">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h3 class="text-lg font-semibold">Enter verification code</h3>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">Step 2 of 3</p>
                </div>
                <button onclick="closeModal()" class="text-2xl text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-200">×</button>
            </div>

            <p class="text-sm text-neutral-600 dark:text-neutral-400 mb-2">
                We've sent a 6-digit code to <span id="displayEmail" class="font-medium text-neutral-900 dark:text-neutral-100"></span>
            </p>

            <div class="flex items-center gap-2 mb-4">
                <p class="text-xs text-neutral-500">Code expires in:</p>
                <span id="timer" class="text-xs font-mono font-semibold text-violet-600">05:00</span>
            </div>

            <input
                id="otpCode"
                type="text"
                maxlength="6"
                placeholder="000000"
                class="w-full mb-1 px-3 py-3 border rounded-lg text-center text-lg font-mono tracking-widest
                       bg-white dark:bg-zinc-900
                       text-neutral-900 dark:text-neutral-100
                       placeholder-neutral-400 dark:placeholder-neutral-500
                       transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-violet-500"
                oninput="this.value = this.value.replace(/[^0-9]/g, '')"
            />

            <p id="step2Error" class="text-red-500 text-xs mb-4 min-h-[16px]"></p>

            <div class="flex gap-3 mb-3">
                <button
                    id="verifyOtpBtn"
                    class="flex-1 bg-violet-600 hover:bg-violet-700 disabled:bg-neutral-300 disabled:cursor-not-allowed text-white py-3 rounded-lg font-medium transition-colors duration-200"
                    onclick="verifyOtp()"
                >
                    Verify Code
                </button>
                <button onclick="goToStep(1)" class="px-4 py-3 border rounded-lg hover:bg-neutral-50 dark:hover:bg-neutral-800 transition-colors duration-200">
                    Back
                </button>
            </div>

            <button
                id="resendBtn"
                onclick="resendOtp()"
                class="w-full text-sm text-violet-600 hover:underline disabled:text-neutral-400 disabled:no-underline disabled:cursor-not-allowed"
                disabled
            >
                Resend code (<span id="resendTimer">60</span>s)
            </button>
        </div>

        {{-- Step 3: New Password Entry --}}
        <div id="step3" class="step-content hidden">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h3 class="text-lg font-semibold">Create new password</h3>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">Step 3 of 3</p>
                </div>
                <button onclick="closeModal()" class="text-2xl text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-200">×</button>
            </div>

            <p class="text-sm text-neutral-600 dark:text-neutral-400 mb-4">
                Create a strong password for your account.
            </p>

            <div class="mb-3">
                <label class="block mb-2 text-sm">New Password</label>
                <div class="flex items-center bg-neutral-50 dark:bg-black border rounded-lg px-3">
                    <input
                        id="newPassword"
                        type="password"
                        placeholder="••••••••"
                        class="w-full bg-transparent py-3 text-sm
                               text-neutral-900 dark:text-neutral-100
                               placeholder-neutral-400 dark:placeholder-neutral-500
                               focus:outline-none"
                    />
                    <button type="button" onclick="toggleNewPassword()" class="ml-2">
                        <span id="newEyeOpen">
                            <x-icon name="eye" class="h-4 w-4" />
                        </span>
                        <span id="newEyeClosed" class="hidden">
                            <x-icon name="eye-off" class="h-4 w-4" />
                        </span>
                    </button>
                </div>
            </div>

            <div class="mb-1">
                <label class="block mb-2 text-sm">Confirm Password</label>
                <div class="flex items-center bg-neutral-50 dark:bg-black border rounded-lg px-3">
                    <input
                        id="confirmPassword"
                        type="password"
                        placeholder="••••••••"
                        class="w-full bg-transparent py-3 text-sm
                               text-neutral-900 dark:text-neutral-100
                               placeholder-neutral-400 dark:placeholder-neutral-500
                               focus:outline-none"
                    />
                    <button type="button" onclick="toggleConfirmPassword()" class="ml-2">
                        <span id="confirmEyeOpen">
                            <x-icon name="eye" class="h-4 w-4" />
                        </span>
                        <span id="confirmEyeClosed" class="hidden">
                            <x-icon name="eye-off" class="h-4 w-4" />
                        </span>
                    </button>
                </div>
            </div>

            <p id="step3Error" class="text-red-500 text-xs mb-4 min-h-[16px]"></p>

            <div class="flex gap-3">
                <button
                    id="resetPasswordBtn"
                    class="flex-1 bg-violet-600 hover:bg-violet-700 disabled:bg-neutral-300 disabled:cursor-not-allowed text-white py-3 rounded-lg font-medium transition-colors duration-200"
                    onclick="resetPassword()"
                >
                    Reset Password
                </button>
                <button onclick="goToStep(2)" class="px-4 py-3 border rounded-lg hover:bg-neutral-50 dark:hover:bg-neutral-800 transition-colors duration-200">
                    Back
                </button>
            </div>
        </div>

    </div>
</div>

{{-- Scripts --}}
<script>
    // Dark mode
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

    function toggleNewPassword() {
        const input = document.getElementById('newPassword');
        document.getElementById('newEyeOpen').classList.toggle('hidden', input.type === 'text');
        document.getElementById('newEyeClosed').classList.toggle('hidden', input.type === 'password');
        input.type = input.type === 'password' ? 'text' : 'password';
    }

    function toggleConfirmPassword() {
        const input = document.getElementById('confirmPassword');
        document.getElementById('confirmEyeOpen').classList.toggle('hidden', input.type === 'text');
        document.getElementById('confirmEyeClosed').classList.toggle('hidden', input.type === 'password');
        input.type = input.type === 'password' ? 'text' : 'password';
    }

    // OTP Recovery Flow
    let currentStep = 1;
    let userEmail = '';
    let otpExpiryTimer = null;
    let resendTimer = null;

    function showForgotPasswordModal() {
        document.getElementById('forgotPasswordModal').classList.remove('hidden');
        goToStep(1);
    }

    function closeModal() {
        document.getElementById('forgotPasswordModal').classList.add('hidden');
        resetModal();
    }

    function resetModal() {
        currentStep = 1;
        userEmail = '';
        clearTimers();

        // Clear all inputs
        document.getElementById('forgotPasswordEmail').value = '';
        document.getElementById('otpCode').value = '';
        document.getElementById('newPassword').value = '';
        document.getElementById('confirmPassword').value = '';

        // Clear all errors
        document.getElementById('step1Error').textContent = '';
        document.getElementById('step2Error').textContent = '';
        document.getElementById('step3Error').textContent = '';

        // Reset all input states
        resetInputState('forgotPasswordEmail');
        resetInputState('otpCode');
    }

    function goToStep(step) {
        // Hide all steps
        document.querySelectorAll('.step-content').forEach(el => el.classList.add('hidden'));

        // Show current step
        document.getElementById(`step${step}`).classList.remove('hidden');
        currentStep = step;

        // Clear errors when changing steps
        document.getElementById(`step${step}Error`).textContent = '';

        // Handle timers
        if (step === 2) {
            startOtpTimer();
            startResendTimer();
        } else {
            clearTimers();
        }
    }

    function clearTimers() {
        if (otpExpiryTimer) {
            clearInterval(otpExpiryTimer);
            otpExpiryTimer = null;
        }
        if (resendTimer) {
            clearInterval(resendTimer);
            resendTimer = null;
        }
    }

    function startOtpTimer() {
        let timeLeft = 300; // 5 minutes in seconds
        const timerElement = document.getElementById('timer');

        clearInterval(otpExpiryTimer);

        otpExpiryTimer = setInterval(() => {
            timeLeft--;
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            timerElement.textContent = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;

            if (timeLeft <= 0) {
                clearInterval(otpExpiryTimer);
                timerElement.textContent = '00:00';
                showError('step2Error', 'OTP has expired. Please request a new code.');
                document.getElementById('verifyOtpBtn').disabled = true;
            }
        }, 1000);
    }

    function startResendTimer() {
        let timeLeft = 60;
        const resendBtn = document.getElementById('resendBtn');
        const resendTimerElement = document.getElementById('resendTimer');

        resendBtn.disabled = true;
        clearInterval(resendTimer);

        resendTimer = setInterval(() => {
            timeLeft--;
            resendTimerElement.textContent = timeLeft;

            if (timeLeft <= 0) {
                clearInterval(resendTimer);
                resendBtn.disabled = false;
                resendBtn.innerHTML = 'Resend code';
            }
        }, 1000);
    }

    function showError(elementId, message) {
        const errorElement = document.getElementById(elementId);
        errorElement.textContent = message;
    }

    function clearError(elementId) {
        document.getElementById(elementId).textContent = '';
    }

    function setInputState(inputId, state) {
        const input = document.getElementById(inputId);
        input.classList.remove('border-red-500', 'border-green-500');

        if (state === 'error') {
            input.classList.add('border-red-500');
        } else if (state === 'success') {
            input.classList.add('border-green-500');
        }
    }

    function resetInputState(inputId) {
        const input = document.getElementById(inputId);
        input.classList.remove('border-red-500', 'border-green-500');
    }

    function validateEmail(email) {
        if (!email || !email.trim()) {
            return { valid: false, message: "Email cannot be empty" };
        }

        email = email.trim();
        const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

        if (!emailRegex.test(email)) {
            return { valid: false, message: "Please enter a valid email address" };
        }

        const [local, domain] = email.split("@");
        if (local.includes("..") || domain.includes("..")) {
            return { valid: false, message: "Email contains invalid consecutive dots" };
        }

        return { valid: true, message: "Email is valid" };
    }

    async function generateOtp() {
        const emailInput = document.getElementById('forgotPasswordEmail');
        const email = emailInput.value.trim();
        const btn = document.getElementById('sendOtpBtn');

        clearError('step1Error');
        resetInputState('forgotPasswordEmail');

        // Validate email
        const validation = validateEmail(email);
        if (!validation.valid) {
            showError('step1Error', validation.message);
            setInputState('forgotPasswordEmail', 'error');
            return;
        }

        try {
            btn.disabled = true;
            btn.textContent = 'Sending...';

            const response = await axios.post('/otp/generate-otp', { email });

            if (response.data.success) {
                userEmail = email;
                document.getElementById('displayEmail').textContent = email;
                setInputState('forgotPasswordEmail', 'success');

                // Move to next step after a brief delay
                setTimeout(() => {
                    goToStep(2);
                }, 500);
            } else {
                showError('step1Error', response.data.message || 'Failed to send code');
                setInputState('forgotPasswordEmail', 'error');
            }

        } catch (error) {
            handleApiError(error, 'step1Error', 'forgotPasswordEmail');
        } finally {
            btn.disabled = false;
            btn.textContent = 'Send Code';
        }
    }

    async function verifyOtp() {
        const otpInput = document.getElementById('otpCode');
        const otp = otpInput.value.trim();
        const btn = document.getElementById('verifyOtpBtn');

        clearError('step2Error');
        resetInputState('otpCode');

        // Validate OTP
        if (!otp) {
            showError('step2Error', 'Please enter the verification code');
            setInputState('otpCode', 'error');
            return;
        }

        if (otp.length !== 6) {
            showError('step2Error', 'Code must be 6 digits');
            setInputState('otpCode', 'error');
            return;
        }

        try {
            btn.disabled = true;
            btn.textContent = 'Verifying...';

            const response = await axios.post('/otp/verify-otp', {
                email: userEmail,
                otp: otp
            });

            if (response.data.success) {
                setInputState('otpCode', 'success');
                clearTimers();

                // Move to next step after a brief delay
                setTimeout(() => {
                    goToStep(3);
                }, 500);
            } else {
                showError('step2Error', response.data.message || 'Invalid verification code');
                setInputState('otpCode', 'error');
            }

        } catch (error) {
            handleApiError(error, 'step2Error', 'otpCode');
        } finally {
            btn.disabled = false;
            btn.textContent = 'Verify Code';
        }
    }

    async function resendOtp() {
        const btn = document.getElementById('resendBtn');

        clearError('step2Error');

        try {
            btn.disabled = true;
            btn.textContent = 'Sending...';

            const response = await axios.post('/otp/generate-otp', { email: userEmail });

            if (response.data.success) {
                showError('step2Error', 'New code sent successfully!');
                document.getElementById('step2Error').classList.remove('text-red-500');
                document.getElementById('step2Error').classList.add('text-green-500');

                // Clear the OTP input
                document.getElementById('otpCode').value = '';
                resetInputState('otpCode');

                // Restart timers
                startOtpTimer();
                startResendTimer();
            } else {
                showError('step2Error', response.data.message || 'Failed to resend code');
            }

        } catch (error) {
            handleApiError(error, 'step2Error', null);
        }
    }

    async function resetPassword() {
        const newPassword = document.getElementById('newPassword').value;
        const confirmPassword = document.getElementById('confirmPassword').value;
        const btn = document.getElementById('resetPasswordBtn');

        clearError('step3Error');

        // Validate passwords
        if (!newPassword || !confirmPassword) {
            showError('step3Error', 'Please fill in both password fields');
            return;
        }

        if (newPassword.length < 8) {
            showError('step3Error', 'Password must be at least 8 characters long');
            return;
        }

        if (newPassword !== confirmPassword) {
            showError('step3Error', 'Passwords do not match');
            return;
        }

        try {
            btn.disabled = true;
            btn.textContent = 'Resetting...';

            const response = await axios.post('/otp/reset-password', {
                email: userEmail,
                password: newPassword,
                password_confirmation: confirmPassword
            });

            if (response.data.success) {
                // Show success message
                showError('step3Error', 'Password reset successfully! Redirecting...');
                document.getElementById('step3Error').classList.remove('text-red-500');
                document.getElementById('step3Error').classList.add('text-green-500');

                // Close modal and redirect after a delay
                setTimeout(() => {
                    closeModal();
                    window.location.href = '/login'; // Or wherever you want to redirect
                }, 2000);
            } else {
                showError('step3Error', response.data.message || 'Failed to reset password');
            }

        } catch (error) {
            handleApiError(error, 'step3Error', null);
        } finally {
            btn.disabled = false;
            btn.textContent = 'Reset Password';
        }
    }

    function handleApiError(error, errorElementId, inputId) {
        if (error.response) {
            const status = error.response.status;
            const data = error.response.data;

            if (status === 422) {
                // Validation errors
                const firstError = data.errors ? Object.values(data.errors)[0][0] : data.message;
                showError(errorElementId, firstError);
                if (inputId) setInputState(inputId, 'error');
            } else if (status === 404) {
                showError(errorElementId, data.message || 'Resource not found');
                if (inputId) setInputState(inputId, 'error');
            } else if (status === 500) {
                showError(errorElementId, 'Server error. Please try again later');
            } else {
                showError(errorElementId, data.message || 'An error occurred');
                if (inputId) setInputState(inputId, 'error');
            }
        } else if (error.request) {
            showError(errorElementId, 'Network error. Please check your connection');
        } else {
            showError(errorElementId, 'An unexpected error occurred');
        }
    }
</script>
</body>
</html>
