<!doctype html>
<html lang="en" class="transition-colors duration-300">
<head>
    {{-- Prevent flash of wrong theme --}}
    @include('components.theme-init')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login · Love & Styles</title>

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

<body class="min-h-screen font-geist text-neutral-900 dark:text-neutral-50 flex overflow-hidden">

    {{-- LEFT PANEL --}}
    <div class="hidden lg:flex lg:w-[58%] min-h-screen flex-col bg-gradient-to-br from-violet-700 via-indigo-600 to-blue-500 text-white relative overflow-hidden">
        {{-- Decorative radial overlays --}}
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute top-0 left-0 w-[500px] h-[500px] rounded-full bg-purple-500/30 blur-3xl -translate-x-1/3 -translate-y-1/3"></div>
            <div class="absolute bottom-0 right-0 w-[400px] h-[400px] rounded-full bg-blue-400/20 blur-3xl translate-x-1/4 translate-y-1/4"></div>
        </div>

        {{-- Content --}}
        <div class="relative z-10 flex flex-col h-full p-10 xl:p-14">
            {{-- Logo / Brand --}}
            <div class="flex items-center gap-3 mb-auto">
                <div class="h-10 w-10 rounded-xl bg-white/15 border border-white/20 grid place-items-center">
                    <x-icon name="lock" class="h-5 w-5 text-white" />
                </div>
                <div>
                    <p class="text-base font-semibold leading-none">Love &amp; Styles</p>
                    <p class="text-xs text-white/60 mt-0.5">Admin Portal</p>
                </div>
            </div>

            {{-- Hero copy --}}
            <div class="py-12 xl:py-16">
                <h1 class="text-3xl xl:text-4xl font-semibold leading-tight max-w-lg">
                    Manage rentals with a clean, consistent interface.
                </h1>
                <p class="mt-4 text-white/70 text-sm leading-relaxed max-w-md">
                    Access and organize customers, invoices, rentals, and analytics all in one secure location.
                </p>

                {{-- Feature cards --}}
                <div class="mt-8 space-y-3">
                    <div class="flex items-start gap-4 rounded-2xl border border-white/15 bg-white/10 px-5 py-4">
                        <div class="mt-0.5 h-8 w-8 rounded-lg bg-white/15 grid place-items-center shrink-0">
                            <x-icon name="shield" class="h-4 w-4 text-white" />
                        </div>
                        <div>
                            <p class="text-sm font-semibold">Enterprise Security</p>
                            <p class="text-xs text-white/60 mt-0.5">Time-boxed codes, encrypted data, and comprehensive audit logs</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 rounded-2xl border border-white/15 bg-white/10 px-5 py-4">
                        <div class="mt-0.5 h-8 w-8 rounded-lg bg-white/15 grid place-items-center shrink-0">
                            <x-icon name="check-circle" class="h-4 w-4 text-white" />
                        </div>
                        <div>
                            <p class="text-sm font-semibold">Uptime-First Reliability</p>
                            <p class="text-xs text-white/60 mt-0.5">Resilient infrastructure with 99.9% SLA guarantee</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <p class="text-xs text-white/40 mt-auto">Enterprise-grade rental management system</p>
        </div>
    </div>

    {{-- RIGHT PANEL --}}
    <div class="flex-1 min-h-screen flex items-center justify-center bg-neutral-50 dark:bg-neutral-950 px-6 py-12">
        <div class="w-full max-w-sm">
            {{-- Header --}}
            <div class="mb-8">
                <p class="text-[10px] uppercase tracking-[0.25em] text-neutral-400 dark:text-neutral-500 font-medium mb-1">Welcome back</p>
                <h2 class="text-2xl font-bold text-neutral-900 dark:text-neutral-50">Sign in to dashboard</h2>
                <p class="text-sm text-neutral-500 dark:text-neutral-400 mt-1.5">Access rentals, customers, invoices, and analytics.</p>
            </div>

            {{-- Form --}}
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                {{-- Email --}}
                <div class="space-y-1.5">
                    <label class="text-sm font-medium text-neutral-700 dark:text-neutral-300">Email address</label>
                    <div class="flex items-center bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800 rounded-xl px-3.5 focus-within:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-100 dark:focus-within:ring-indigo-500/20 transition">
                        <x-icon name="mail" class="h-4 w-4 text-neutral-400 mr-2.5 shrink-0" />
                        <input
                            type="email"
                            name="email"
                            required
                            autocomplete="email"
                            placeholder="admin@example.com"
                            value="{{ old('email') }}"
                            class="w-full bg-transparent py-3 text-sm text-neutral-900 dark:text-neutral-100 placeholder-neutral-400 dark:placeholder-neutral-600 focus:outline-none"
                        />
                    </div>
                </div>

                {{-- Password --}}
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <label class="text-sm font-medium text-neutral-700 dark:text-neutral-300">Password</label>
                        <button type="button" onclick="openForgotModal()" class="text-xs font-medium text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300">Forgot password?</button>
                    </div>
                    <div class="flex items-center bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800 rounded-xl px-3.5 focus-within:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-100 dark:focus-within:ring-indigo-500/20 transition">
                        <x-icon name="lock" class="h-4 w-4 text-neutral-400 mr-2.5 shrink-0" />
                        <input
                            id="passwordInput"
                            type="password"
                            name="password"
                            required
                            autocomplete="current-password"
                            placeholder="••••••••"
                            class="w-full bg-transparent py-3 text-sm text-neutral-900 dark:text-neutral-100 placeholder-neutral-400 dark:placeholder-neutral-600 focus:outline-none"
                        />
                        <button type="button" onclick="togglePassword()" class="ml-2 text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-300 transition">
                            <span id="eyeOpen"><x-icon name="eye" class="h-4 w-4" /></span>
                            <span id="eyeClosed" class="hidden"><x-icon name="eye-off" class="h-4 w-4" /></span>
                        </button>
                    </div>
                </div>

                @error('email')
                <p class="text-xs text-red-600 dark:text-red-400 -mt-2">{{ $message }}</p>
                @enderror

                {{-- Submit --}}
                <button type="submit" class="w-full flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 rounded-xl py-3.5 text-white text-sm font-semibold shadow-md shadow-indigo-600/25 transition-all duration-150">
                    Sign in
                    <x-icon name="arrow-right" class="h-4 w-4" />
                </button>
            </form>

            {{-- Divider --}}
            <div class="mt-8 flex items-center gap-3 text-xs text-neutral-400 dark:text-neutral-600">
                <span class="h-px flex-1 bg-neutral-200 dark:bg-neutral-800"></span>
            </div>

            <p class="mt-5 text-center text-sm text-neutral-500 dark:text-neutral-400">
                Need access? <a href="mailto:admin@yourdomain.com" class="font-semibold text-neutral-800 dark:text-neutral-100 hover:underline">Contact your administrator</a>
            </p>
        </div>
    </div>

</body>

{{-- OTP / Forgot password modal --}}
<div id="otpModal" class="hidden fixed inset-0 z-50 flex items-center justify-center px-4 py-6 bg-black/60 backdrop-blur-sm">
    <div class="w-full max-w-2xl bg-white dark:bg-neutral-950 border border-neutral-200 dark:border-neutral-800 rounded-3xl shadow-2xl overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-neutral-200 dark:border-neutral-800 bg-neutral-50/80 dark:bg-neutral-900/50">
            <div>
                <p class="text-xs uppercase tracking-[0.2em] text-neutral-500 dark:text-neutral-500">Password recovery</p>
                <h3 class="text-lg font-semibold">Verify with one-time code</h3>
            </div>
            <button onclick="closeOtpModal()" class="text-neutral-500 hover:text-neutral-800 dark:hover:text-neutral-200 text-xl">×</button>
        </div>

        <div class="px-6 py-5 space-y-4">
            <div class="flex items-center gap-2 text-xs font-medium text-neutral-600 dark:text-neutral-400">
                <span id="stepBadge1" class="step-badge inline-flex h-7 w-7 items-center justify-center rounded-full border border-neutral-200 dark:border-neutral-800 bg-neutral-200 text-neutral-700 font-semibold">1</span>
                <span>Email</span>
                <span class="h-px flex-1 bg-neutral-200 dark:bg-neutral-800"></span>
                <span id="stepBadge2" class="step-badge inline-flex h-7 w-7 items-center justify-center rounded-full border border-neutral-200 dark:border-neutral-800 bg-neutral-200 text-neutral-700 font-semibold opacity-60">2</span>
                <span class="opacity-80">Code</span>
                <span class="h-px flex-1 bg-neutral-200 dark:bg-neutral-800"></span>
                <span id="stepBadge3" class="step-badge inline-flex h-7 w-7 items-center justify-center rounded-full border border-neutral-200 dark:border-neutral-800 bg-neutral-200 text-neutral-700 font-semibold opacity-60">3</span>
                <span class="opacity-80">Reset</span>
            </div>

            {{-- Step 1 --}}
            <div id="otpStep1" class="space-y-4">
                <div class="bg-neutral-50 dark:bg-neutral-900/50 border border-neutral-200 dark:border-neutral-800 rounded-2xl p-4">
                    <p class="text-sm text-neutral-700 dark:text-neutral-300">
                        Enter the email linked to your account. We'll send a 6-digit code to verify it's you.
                    </p>
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-medium">Email address</label>
                    <div class="flex items-center bg-white dark:bg-black border border-neutral-200 dark:border-neutral-800 rounded-xl px-3 focus-within:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-100 dark:focus-within:ring-indigo-500/30 transition">
                        <x-icon name="mail" class="text-neutral-400 mr-2" />
                        <input
                            id="fpEmail"
                            type="email"
                            placeholder="you@business.com"
                            class="w-full bg-transparent py-3 text-sm text-neutral-900 dark:text-neutral-100 placeholder-neutral-400 dark:placeholder-neutral-500 focus:outline-none"
                        />
                    </div>
                    <p id="otpStep1Msg" class="text-xs min-h-[18px] text-red-500"></p>
                </div>
                <div class="flex items-center gap-3">
                    <button id="otpSendBtn" onclick="handleSendOtp()" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl py-3 shadow-lg shadow-indigo-600/20 transition">
                        Send code
                    </button>
                    <button onclick="closeOtpModal()" class="px-4 py-3 rounded-xl border border-neutral-200 dark:border-neutral-800 text-neutral-700 dark:text-neutral-200 hover:bg-neutral-50 dark:hover:bg-neutral-900 transition">
                        Cancel
                    </button>
                </div>
            </div>

            {{-- Step 2 --}}
            <div id="otpStep2" class="hidden space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-neutral-800 dark:text-neutral-100">Enter verification code</p>
                        <p class="text-xs text-neutral-500">Sent to <span id="otpDisplayEmail" class="font-semibold text-neutral-800 dark:text-neutral-100"></span></p>
                    </div>
                    <div class="text-xs font-mono text-indigo-600 dark:text-indigo-400">
                        Expires in <span id="otpCountdown">05:00</span>
                    </div>
                </div>

                <div class="flex items-center bg-white dark:bg-black border border-neutral-200 dark:border-neutral-800 rounded-xl px-3 focus-within:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-100 dark:focus-within:ring-indigo-500/30 transition">
                    <x-icon name="key" class="text-neutral-400 mr-2" />
                    <input
                        id="otpCodeInput"
                        type="text"
                        maxlength="6"
                        inputmode="numeric"
                        autocomplete="one-time-code"
                        placeholder="000000"
                        class="w-full bg-transparent py-3 text-center text-lg font-geist-mono tracking-[0.4em] text-neutral-900 dark:text-neutral-50 placeholder-neutral-400 dark:placeholder-neutral-600 focus:outline-none"
                        oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                    />
                </div>
                <p id="otpStep2Msg" class="text-xs min-h-[18px] text-red-500"></p>

                <div class="flex items-center gap-3">
                    <button id="otpVerifyBtn" onclick="handleVerifyOtp()" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl py-3 shadow-lg shadow-indigo-600/20 transition">
                        Verify code
                    </button>
                    <button onclick="setOtpStep(1)" class="px-4 py-3 rounded-xl border border-neutral-200 dark:border-neutral-800 text-neutral-700 dark:text-neutral-200 hover:bg-neutral-50 dark:hover:bg-neutral-900 transition">
                        Back
                    </button>
                </div>

                <button id="otpResendBtn" onclick="handleResendOtp()" class="w-full text-sm text-indigo-600 dark:text-indigo-400 font-medium hover:underline disabled:text-neutral-400 disabled:no-underline disabled:cursor-not-allowed" disabled>
                    Resend code in <span id="otpResendCountdown">60</span>s
                </button>
            </div>

            {{-- Step 3 --}}
            <div id="otpStep3" class="hidden space-y-4">
                <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-2xl p-4 flex items-center gap-3">
                    <div class="h-10 w-10 rounded-full bg-white dark:bg-emerald-800/50 text-emerald-700 dark:text-emerald-200 grid place-items-center">
                        <x-icon name="check-circle" class="h-5 w-5" />
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-emerald-700 dark:text-emerald-200">Code verified</p>
                        <p class="text-xs text-emerald-700/80 dark:text-emerald-200/80">Create a new password to finish.</p>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium">New password</label>
                    <div class="flex items-center bg-white dark:bg-black border border-neutral-200 dark:border-neutral-800 rounded-xl px-3 focus-within:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-100 dark:focus-within:ring-indigo-500/30 transition">
                        <x-icon name="lock" class="text-neutral-400 mr-2" />
                        <input
                            id="otpNewPassword"
                            type="password"
                            placeholder="••••••••"
                            class="w-full bg-transparent py-3 text-sm text-neutral-900 dark:text-neutral-100 placeholder-neutral-400 dark:placeholder-neutral-500 focus:outline-none"
                        />
                        <button type="button" onclick="toggleFieldVisibility('otpNewPassword', 'otpNewEyeOpen', 'otpNewEyeClosed')" class="ml-2 text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-200">
                            <span id="otpNewEyeOpen"><x-icon name="eye" class="h-4 w-4" /></span>
                            <span id="otpNewEyeClosed" class="hidden"><x-icon name="eye-off" class="h-4 w-4" /></span>
                        </button>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium">Confirm password</label>
                    <div class="flex items-center bg-white dark:bg-black border border-neutral-200 dark:border-neutral-800 rounded-xl px-3 focus-within:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-100 dark:focus-within:ring-indigo-500/30 transition">
                        <x-icon name="lock" class="text-neutral-400 mr-2" />
                        <input
                            id="otpConfirmPassword"
                            type="password"
                            placeholder="••••••••"
                            class="w-full bg-transparent py-3 text-sm text-neutral-900 dark:text-neutral-100 placeholder-neutral-400 dark:placeholder-neutral-500 focus:outline-none"
                        />
                        <button type="button" onclick="toggleFieldVisibility('otpConfirmPassword', 'otpConfirmEyeOpen', 'otpConfirmEyeClosed')" class="ml-2 text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-200">
                            <span id="otpConfirmEyeOpen"><x-icon name="eye" class="h-4 w-4" /></span>
                            <span id="otpConfirmEyeClosed" class="hidden"><x-icon name="eye-off" class="h-4 w-4" /></span>
                        </button>
                    </div>
                </div>
                <p id="otpStep3Msg" class="text-xs min-h-[18px] text-red-500"></p>

                <div class="flex items-center gap-3">
                    <button id="otpResetBtn" onclick="handleResetPassword()" class="flex-1 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-semibold rounded-xl py-3 shadow-lg shadow-emerald-500/20 transition">
                        Reset password
                    </button>
                    <button onclick="setOtpStep(2)" class="px-4 py-3 rounded-xl border border-neutral-200 dark:border-neutral-800 text-neutral-700 dark:text-neutral-200 hover:bg-neutral-50 dark:hover:bg-neutral-900 transition">
                        Back
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Password field toggles
    function togglePassword() {
        const input = document.getElementById('passwordInput');
        const isText = input.type === 'text';
        input.type = isText ? 'password' : 'text';
        document.getElementById('eyeOpen').classList.toggle('hidden', !isText);
        document.getElementById('eyeClosed').classList.toggle('hidden', isText);
    }
    function toggleFieldVisibility(inputId, openId, closedId) {
        const input = document.getElementById(inputId);
        const isText = input.type === 'text';
        input.type = isText ? 'password' : 'text';
        document.getElementById(openId).classList.toggle('hidden', !isText);
        document.getElementById(closedId).classList.toggle('hidden', isText);
    }

    // OTP flow controller
    const otpState = {
        step: 1,
        email: '',
        otpTimer: null,
        otpSeconds: 300,
        resendTimer: null,
        resendSeconds: 60,
    };

    const el = (id) => document.getElementById(id);
    const setText = (id, text, color = 'text-red-500') => {
        const node = el(id);
        node.textContent = text || '';
        node.classList.remove('text-red-500', 'text-emerald-500', 'text-neutral-500', 'text-green-500');
        if (text) node.classList.add(color);
    };

    const setLoading = (btnId, isLoading, label, loadingLabel) => {
        const btn = el(btnId);
        if (!btn) return;
        btn.disabled = !!isLoading;
        btn.innerHTML = isLoading ? (loadingLabel || 'Please wait...') : label;
    };

    function openForgotModal() {
        resetOtpFlow();
        el('otpModal').classList.remove('hidden');
        setOtpStep(1);
    }

    function closeOtpModal() {
        resetOtpFlow();
        el('otpModal').classList.add('hidden');
    }

    function resetOtpFlow() {
        clearTimers();
        otpState.step = 1;
        otpState.email = '';
        el('fpEmail').value = '';
        el('otpCodeInput').value = '';
        el('otpNewPassword').value = '';
        el('otpConfirmPassword').value = '';
        setText('otpStep1Msg', '');
        setText('otpStep2Msg', '');
        setText('otpStep3Msg', '');
        setOtpStep(1);
    }

    function setOtpStep(step) {
        otpState.step = step;
        ['otpStep1','otpStep2','otpStep3'].forEach((id, index) => {
            el(id).classList.toggle('hidden', index + 1 !== step);
        });
        [1,2,3].forEach((n) => {
            const badge = el(`stepBadge${n}`);
            badge.classList.toggle('opacity-60', n !== step);
            badge.classList.toggle('bg-indigo-600', n === step);
            badge.classList.toggle('text-white', n === step);
            badge.classList.toggle('bg-neutral-200', n !== step);
            badge.classList.toggle('text-neutral-700', n !== step);
        });
        if (step !== 2) {
            clearTimers();
            el('otpCountdown').textContent = '05:00';
            el('otpResendCountdown').textContent = '60';
            el('otpResendBtn').disabled = true;
        }
    }

    function clearTimers() {
        if (otpState.otpTimer) clearInterval(otpState.otpTimer);
        if (otpState.resendTimer) clearInterval(otpState.resendTimer);
        otpState.otpTimer = null;
        otpState.resendTimer = null;
        otpState.otpSeconds = 300;
        otpState.resendSeconds = 60;
    }

    function startOtpCountdown() {
        clearInterval(otpState.otpTimer);
        otpState.otpSeconds = 300;
        otpState.otpTimer = setInterval(() => {
            otpState.otpSeconds--;
            const m = String(Math.max(0, Math.floor(otpState.otpSeconds / 60))).padStart(2, '0');
            const s = String(Math.max(0, otpState.otpSeconds % 60)).padStart(2, '0');
            el('otpCountdown').textContent = `${m}:${s}`;
            if (otpState.otpSeconds <= 0) {
                clearTimers();
                setText('otpStep2Msg', 'OTP expired. Please request a new code.');
                el('otpVerifyBtn').disabled = true;
                el('otpResendBtn').disabled = false;
            }
        }, 1000);
    }

    function startResendCountdown() {
        clearInterval(otpState.resendTimer);
        otpState.resendSeconds = 60;
        el('otpResendBtn').disabled = true;
        otpState.resendTimer = setInterval(() => {
            otpState.resendSeconds--;
            el('otpResendCountdown').textContent = otpState.resendSeconds;
            if (otpState.resendSeconds <= 0) {
                clearInterval(otpState.resendTimer);
                el('otpResendBtn').disabled = false;
                el('otpResendCountdown').textContent = '0';
            }
        }, 1000);
    }

    function validateEmail(email) {
        if (!email || !email.trim()) return 'Email cannot be empty';
        const normalized = email.trim();
        const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        if (!emailRegex.test(normalized)) return 'Please enter a valid email address';
        const [local, domain] = normalized.split('@');
        if (local.includes('..') || domain.includes('..')) return 'Email contains invalid consecutive dots';
        return '';
    }

    function validateOtp(otp) {
        if (!otp) return 'Please enter the verification code';
        if (otp.length !== 6) return 'Code must be 6 digits';
        return '';
    }

    function validatePasswords(pw, confirm) {
        if (!pw || !confirm) return 'Please fill in both password fields';
        if (pw.length < 8) return 'Password must be at least 8 characters long';
        if (pw !== confirm) return 'Passwords do not match';
        return '';
    }

    async function apiPost(url, payload) {
        try {
            return await axios.post(url, payload);
        } catch (error) {
            throw error.response || { status: 500, data: { message: 'Server error. Please try again.' } };
        }
    }

    async function handleSendOtp() {
        const email = el('fpEmail').value.trim();
        const error = validateEmail(email);
        setText('otpStep1Msg', error);
        if (error) return;

        setLoading('otpSendBtn', true, 'Send code', 'Sending...');
        try {
            const response = await apiPost('/otp/generate-otp', { email });
            const data = response.data;
            if (data.success) {
                otpState.email = email;
                el('otpDisplayEmail').textContent = email;
                setText('otpStep1Msg', 'Code sent successfully', 'text-emerald-500');
                el('otpVerifyBtn').disabled = false;
                setOtpStep(2);
                startOtpCountdown();
                startResendCountdown();
            } else {
                setText('otpStep1Msg', data.message || 'Failed to send code');
            }
        } catch (err) {
            const data = err.data || {};
            const message = data.message || 'Server error. Please try again later.';
            setText('otpStep1Msg', message);
        } finally {
            setLoading('otpSendBtn', false, 'Send code');
        }
    }

    async function handleVerifyOtp() {
        const otp = el('otpCodeInput').value.trim();
        const errMsg = validateOtp(otp);
        setText('otpStep2Msg', errMsg);
        if (errMsg) return;

        setLoading('otpVerifyBtn', true, 'Verify code', 'Verifying...');
        try {
            const response = await apiPost('/otp/verify-otp', { email: otpState.email, otp });
            const data = response.data;
            if (data.success) {
                setText('otpStep2Msg', 'Code verified', 'text-emerald-500');
                clearTimers();
                setOtpStep(3);
            } else {
                setText('otpStep2Msg', data.message || 'Invalid verification code');
            }
        } catch (err) {
            const data = err.data || {};
            const status = err.status;
            if (status === 422) {
                const firstError = data.errors ? Object.values(data.errors)[0][0] : data.message;
                setText('otpStep2Msg', firstError || 'Invalid verification code');
            } else if (status === 429) {
                setText('otpStep2Msg', data.message || 'Too many attempts. Please request a new code.');
            } else {
                setText('otpStep2Msg', data.message || 'Server error. Please try again later.');
            }
        } finally {
            setLoading('otpVerifyBtn', false, 'Verify code');
        }
    }

    async function handleResendOtp() {
        if (!otpState.email) return;
        setText('otpStep2Msg', '');
        setLoading('otpResendBtn', true, 'Resend code', 'Sending...');
        try {
            const response = await apiPost('/otp/generate-otp', { email: otpState.email });
            const data = response.data;
            if (data.success) {
                setText('otpStep2Msg', 'New code sent', 'text-emerald-500');
                el('otpCodeInput').value = '';
                el('otpVerifyBtn').disabled = false;
                startOtpCountdown();
                startResendCountdown();
            } else {
                setText('otpStep2Msg', data.message || 'Failed to resend code');
            }
        } catch (err) {
            const data = err.data || {};
            setText('otpStep2Msg', data.message || 'Server error. Please try again later.');
        } finally {
            setLoading('otpResendBtn', false, 'Resend code in <span id="otpResendCountdown">60</span>s');
        }
    }

    async function handleResetPassword() {
        const pw = el('otpNewPassword').value;
        const confirm = el('otpConfirmPassword').value;
        const err = validatePasswords(pw, confirm);
        setText('otpStep3Msg', err);
        if (err) return;

        setLoading('otpResetBtn', true, 'Reset password', 'Saving...');
        try {
            const response = await apiPost('/otp/reset-password', {
                email: otpState.email,
                password: pw,
                confirm_password: confirm,
            });
            const data = response.data;
            if (data.success) {
                setText('otpStep3Msg', 'Password reset successfully! Redirecting...', 'text-emerald-500');
                setTimeout(() => window.location.href = '/login', 1500);
            } else {
                setText('otpStep3Msg', data.message || 'Failed to reset password');
            }
        } catch (err) {
            const data = err.data || {};
            const status = err.status;
            if (status === 422) {
                const firstError = data.errors ? Object.values(data.errors)[0][0] : data.message;
                setText('otpStep3Msg', firstError || 'Validation error');
            } else {
                setText('otpStep3Msg', data.message || 'Server error. Please try again later.');
            }
        } finally {
            setLoading('otpResetBtn', false, 'Reset password');
        }
    }
</script>
</html>