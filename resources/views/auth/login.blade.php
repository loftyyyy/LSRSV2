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

<body class="min-h-screen font-geist bg-gradient-to-br from-neutral-50 via-white to-neutral-100 dark:from-black dark:via-[#0a0a0a] dark:to-[#0f0f12] text-neutral-900 dark:text-neutral-50 px-4 py-10">
<div class="absolute inset-0 bg-[radial-gradient(circle_at_20%_20%,rgba(124,58,237,0.12),transparent_30%),radial-gradient(circle_at_80%_0%,rgba(14,165,233,0.12),transparent_25%),radial-gradient(circle_at_50%_90%,rgba(59,130,246,0.12),transparent_22%)] pointer-events-none"></div>

{{-- Theme Toggle --}}
<div class="fixed top-6 right-6 z-20">
    <button
        type="button"
        onclick="toggleDarkMode()"
        class="flex items-center gap-2 rounded-full px-3 py-2 text-xs font-medium text-neutral-700 dark:text-neutral-200 bg-white/70 dark:bg-white/10 border border-neutral-200/70 dark:border-white/10 shadow-sm hover:shadow transition-all duration-200 backdrop-blur"
    >
        <span class="inline-flex h-5 w-9 items-center rounded-full bg-amber-500/20 dark:bg-violet-600/25 px-0.5 relative transition-all duration-300">
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
                <span id="iconSystem" class="hidden">
                    <x-icon name="monitor" class="h-2.5 w-2.5" />
                </span>
            </span>
        </span>
        <span id="modeLabel" class="text-[11px]">Dark</span>
    </button>
</div>

<main class="relative z-10 max-w-6xl mx-auto">
    <div class="grid lg:grid-cols-2 gap-8 items-center">
        <div class="hidden lg:block">
            <div class="relative overflow-hidden rounded-3xl border border-white/50 dark:border-white/10 bg-gradient-to-br from-violet-600 via-indigo-600 to-blue-600 text-white shadow-2xl">
                <div class="absolute inset-0 opacity-20 bg-[radial-gradient(circle_at_30%_20%,rgba(255,255,255,0.7),transparent_40%),radial-gradient(circle_at_80%_0%,rgba(255,255,255,0.6),transparent_38%)]"></div>
                <div class="relative p-10 space-y-8">
                    <div class="inline-flex items-center gap-3 px-3 py-2 rounded-full bg-white/10 border border-white/20 text-xs uppercase tracking-wide">
                        <span class="h-2 w-2 rounded-full bg-emerald-300 animate-pulse"></span>
                        Secure Access Portal
                    </div>
                    <h1 class="text-3xl font-semibold leading-tight">
                        Manage rentals with a clean, consistent interface.
                    </h1>
                    <p class="text-white/80 text-sm leading-relaxed max-w-xl">
                        Stay on-brand across all screens. Use your dashboard to track rentals, customers, invoices, and more with a cohesive look and feel.
                    </p>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="rounded-2xl bg-white/10 border border-white/15 p-4">
                            <p class="text-xs text-white/70 mb-2">Reliability</p>
                            <p class="text-lg font-semibold">Uptime-first</p>
                            <p class="text-xs text-white/60">Resilient OTP, fast access, audited logs.</p>
                        </div>
                        <div class="rounded-2xl bg-white/10 border border-white/15 p-4">
                            <p class="text-xs text-white/70 mb-2">Security</p>
                            <p class="text-lg font-semibold">Strong auth</p>
                            <p class="text-xs text-white/60">Time-boxed codes, lockouts, encrypted data.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="relative">
            <div class="absolute -inset-4 blur-3xl bg-gradient-to-r from-violet-500/20 via-blue-500/15 to-emerald-400/10 dark:from-violet-500/15 dark:via-blue-500/10 dark:to-emerald-400/10"></div>
            <div class="relative w-full bg-white/80 dark:bg-neutral-950/80 backdrop-blur border border-neutral-200/80 dark:border-neutral-800 rounded-3xl shadow-xl">
                <div class="p-6 sm:p-8">
                    <div class="flex items-start justify-between mb-6">
                        <div>
                            <p class="text-xs uppercase tracking-[0.2em] text-neutral-500 dark:text-neutral-500">Welcome back</p>
                            <h2 class="text-2xl font-semibold mt-1">Sign in to dashboard</h2>
                            <p class="text-sm text-neutral-600 dark:text-neutral-400 mt-2">Access rentals, customers, invoices, and analytics.</p>
                        </div>
                        <div class="h-11 w-11 rounded-2xl bg-gradient-to-br from-violet-500 to-blue-500 text-white grid place-items-center shadow-md">
                            <x-icon name="lock" class="h-5 w-5" />
                        </div>
                    </div>

                    <form method="POST" action="{{ route('login') }}" class="space-y-4">
                        @csrf

                        <div class="space-y-2">
                            <label class="text-sm font-medium">Email address</label>
                            <div class="flex items-center bg-white dark:bg-black border border-neutral-200 dark:border-neutral-800 rounded-xl px-3 shadow-sm focus-within:border-violet-500 focus-within:ring-2 focus-within:ring-violet-100 dark:focus-within:ring-violet-500/30 transition">
                                <x-icon name="mail" class="text-neutral-400 mr-2" />
                                <input
                                    type="email"
                                    name="email"
                                    required
                                    autocomplete="email"
                                    placeholder="you@business.com"
                                    class="w-full bg-transparent py-3 text-sm text-neutral-900 dark:text-neutral-100 placeholder-neutral-400 dark:placeholder-neutral-500 focus:outline-none"
                                />
                            </div>
                        </div>

                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <label class="text-sm font-medium">Password</label>
                                <button type="button" onclick="openForgotModal()" class="text-xs font-medium text-violet-600 hover:text-violet-700 dark:text-violet-400">Forgot password?</button>
                            </div>
                            <div class="flex items-center bg-white dark:bg-black border border-neutral-200 dark:border-neutral-800 rounded-xl px-3 shadow-sm focus-within:border-violet-500 focus-within:ring-2 focus-within:ring-violet-100 dark:focus-within:ring-violet-500/30 transition">
                                <x-icon name="lock" class="text-neutral-400 mr-2" />
                                <input
                                    id="passwordInput"
                                    type="password"
                                    name="password"
                                    required
                                    autocomplete="current-password"
                                    placeholder="••••••••"
                                    class="w-full bg-transparent py-3 text-sm text-neutral-900 dark:text-neutral-100 placeholder-neutral-400 dark:placeholder-neutral-500 focus:outline-none"
                                />
                                <button type="button" onclick="togglePassword()" class="ml-2 text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-200">
                                    <span id="eyeOpen">
                                        <x-icon name="eye" class="h-4 w-4" />
                                    </span>
                                    <span id="eyeClosed" class="hidden">
                                        <x-icon name="eye-off" class="h-4 w-4" />
                                    </span>
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-gradient-to-r from-violet-600 via-indigo-600 to-blue-600 hover:from-violet-700 hover:via-indigo-700 hover:to-blue-700 rounded-xl py-3 text-white font-semibold shadow-lg shadow-violet-600/20 transition-transform hover:translate-y-[-1px]">
                            Sign in
                        </button>
                        @error('email')
                        <p class="text-xs text-red-600 dark:text-red-400 mt-1">
                            {{ $message }}
                        </p>
                        @enderror
                    </form>

                    <div class="mt-8 flex items-center gap-3 text-xs text-neutral-500 dark:text-neutral-500">
                        <span class="h-px flex-1 bg-neutral-200 dark:bg-neutral-800"></span>
                        <span class="uppercase tracking-[0.2em]">Access</span>
                        <span class="h-px flex-1 bg-neutral-200 dark:bg-neutral-800"></span>
                    </div>

                    <p class="mt-4 text-center text-sm text-neutral-600 dark:text-neutral-400">
                        Need access? <span class="text-neutral-900 dark:text-neutral-100 font-medium">Contact your system administrator</span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</main>

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
                    <div class="flex items-center bg-white dark:bg-black border border-neutral-200 dark:border-neutral-800 rounded-xl px-3 focus-within:border-violet-500 focus-within:ring-2 focus-within:ring-violet-100 dark:focus-within:ring-violet-500/30 transition">
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
                    <button id="otpSendBtn" onclick="handleSendOtp()" class="flex-1 bg-violet-600 hover:bg-violet-700 text-white font-semibold rounded-xl py-3 shadow-lg shadow-violet-600/20 transition">
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
                    <div class="text-xs font-mono text-violet-600 dark:text-violet-400">
                        Expires in <span id="otpCountdown">05:00</span>
                    </div>
                </div>

                <div class="flex items-center bg-white dark:bg-black border border-neutral-200 dark:border-neutral-800 rounded-xl px-3 focus-within:border-violet-500 focus-within:ring-2 focus-within:ring-violet-100 dark:focus-within:ring-violet-500/30 transition">
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
                    <button id="otpVerifyBtn" onclick="handleVerifyOtp()" class="flex-1 bg-violet-600 hover:bg-violet-700 text-white font-semibold rounded-xl py-3 shadow-lg shadow-violet-600/20 transition">
                        Verify code
                    </button>
                    <button onclick="setOtpStep(1)" class="px-4 py-3 rounded-xl border border-neutral-200 dark:border-neutral-800 text-neutral-700 dark:text-neutral-200 hover:bg-neutral-50 dark:hover:bg-neutral-900 transition">
                        Back
                    </button>
                </div>

                <button id="otpResendBtn" onclick="handleResendOtp()" class="w-full text-sm text-violet-600 dark:text-violet-400 font-medium hover:underline disabled:text-neutral-400 disabled:no-underline disabled:cursor-not-allowed" disabled>
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
                    <div class="flex items-center bg-white dark:bg-black border border-neutral-200 dark:border-neutral-800 rounded-xl px-3 focus-within:border-violet-500 focus-within:ring-2 focus-within:ring-violet-100 dark:focus-within:ring-violet-500/30 transition">
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
                    <div class="flex items-center bg-white dark:bg-black border border-neutral-200 dark:border-neutral-800 rounded-xl px-3 focus-within:border-violet-500 focus-within:ring-2 focus-within:ring-violet-100 dark:focus-within:ring-violet-500/30 transition">
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
    // Theme toggle
    function getThemeState() {
        if (globalThis.themeController && typeof globalThis.themeController.getState === 'function') {
            return globalThis.themeController.getState();
        }

        return {
            isDark: document.documentElement.classList.contains('dark'),
            preference: null
        };
    }

    let themeState = getThemeState();

    function updateToggleUI() {
        var isDarkMode = !!themeState.isDark;
        var preference = themeState.preference;
        var label = preference === null ? 'System' : (isDarkMode ? 'Dark' : 'Light');
        var knobPosition = preference === null ? 'translateX(7px)' : (isDarkMode ? 'translateX(14px)' : 'translateX(0)');

        document.getElementById('modeLabel').textContent = label;
        document.getElementById('iconMoon').classList.toggle('hidden', !isDarkMode || preference === null);
        document.getElementById('iconSun').classList.toggle('hidden', isDarkMode || preference === null);
        document.getElementById('iconSystem').classList.toggle('hidden', preference !== null);
        document.getElementById('toggleKnob').style.transform = knobPosition;
    }

    function toggleDarkMode() {
        if (globalThis.themeController && typeof globalThis.themeController.togglePreference === 'function') {
            globalThis.themeController.togglePreference();
        } else {
            themeState = { isDark: !themeState.isDark, preference: themeState.isDark ? 'light' : 'dark' };
            document.documentElement.classList.toggle('dark', themeState.isDark);
            document.documentElement.style.colorScheme = themeState.isDark ? 'dark' : 'light';
        }

        themeState = getThemeState();
        updateToggleUI();
    }

    window.addEventListener('theme:changed', function (event) {
        themeState = event && event.detail
            ? {
                isDark: event.detail.isDark,
                preference: event.detail.preference
            }
            : getThemeState();
        updateToggleUI();
    });

    updateToggleUI();

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
            badge.classList.toggle('bg-violet-600', n === step);
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
</body>
</html>
