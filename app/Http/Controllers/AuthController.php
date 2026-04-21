<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

/**
 * Controller handling user authentication operations including login, registration,
 * password reset, and account management.
 * 
 * This controller manages the complete authentication lifecycle for users in the system,
 * providing secure authentication mechanisms and session management.
 */
class AuthController extends Controller
{
    /**
     * Display the login view.
     * 
     * @return \Illuminate\View\View The login form view
     */
    public function showLoginForm(): View
    {
        // Return the login view for unauthenticated users
        return view('auth.login');
    }

    /**
     * Process user login request.
     * 
     * Validates user credentials, authenticates the user, and redirects to the intended page.
     * 
     * @param \Illuminate\Http\Request $request The HTTP request containing login credentials
     * @return \Illuminate\Http\RedirectResponse Redirect to dashboard or back with errors
     */
    public function login(Request $request): RedirectResponse
    {
        // Validate incoming request data
        $credentials = $request->validate([
            'email' => ['required', 'email'],           // Email must be valid format
            'password' => ['required'],                 // Password is required
        ]);

        // Attempt to authenticate user with provided credentials
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            // Regenerate session to prevent session fixation attacks
            $request->session()->regenerate();

            // Redirect to intended page or dashboard if none specified
            return redirect()->intended('/dashboard');
        }

        // Authentication failed, return with error
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email'); // Only repopulate email field for security
    }

    /**
     * Display the registration view.
     * 
     * @return \Illuminate\View\View The registration form view
     */
    public function showRegisterForm(): View
    {
        // Return the registration view for new users
        return view('auth.register');
    }

    /**
     * Process new user registration.
     * 
     * Validates user input, creates a new user account, and redirects to login page.
     * 
     * @param \Illuminate\Http\Request $request The HTTP request containing registration data
     * @return \Illuminate\Http\RedirectResponse Redirect to login page
     */
    public function register(Request $request): RedirectResponse
    {
        // Validate registration data according to business rules
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],          // User's full name
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'], // Unique email
            'password' => ['required', 'string', 'min:8', 'confirmed'], // Password with confirmation
        ]);

        // Create new user with hashed password for security
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']), // Hash password before storage
        ]);

        // Redirect to login page after successful registration
        return redirect('/login');
    }

    /**
     * Display the forgot password view.
     * 
     * @return \Illuminate\View\View The forgot password form view
     */
    public function showForgotPasswordForm(): View
    {
        // Return the forgot password view for password recovery
        return view('auth.forgot-password');
    }

    /**
     * Process password reset request.
     * 
     * Validates reset request data, verifies user identity, and updates password.
     * 
     * @param \Illuminate\Http\Request $request The HTTP request containing reset data
     * @return \Illuminate\Http\JsonResponse JSON response indicating success or failure
     */
    public function resetPassword(Request $request): JsonResponse
    {
        // Validate password reset request data
        $request->validate([
            'email' => ['required', 'email'],           // User's email address
            'password' => ['required', 'string', 'min:8'], // New password
            'confirm_password' => ['required', 'string', 'min:8'], // Password confirmation
        ]);

        // Find user by email address
        $user = User::where('email', $request->email)->first();

        // Return error if user not found
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        // Verify passwords match
        if ($request['password'] !== $request['confirm_password']) {
            return response()->json([
                'success' => false,
                'message' => 'Password does not match with the confirm password',
            ], 422);
        }

        // Update user's password with hashed value
        $user->update(['password' => Hash::make($request['password'])]);

        // Return success response
        return response()->json([
            'success' => true,
            'message' => 'New password has been changed successfully',
        ], 200);
    }

    /**
     * Verify the current authenticated user's password.
     * 
     * Used for sensitive operations requiring password re-verification.
     * 
     * @param \Illuminate\Http\Request $request The HTTP request containing password to verify
     * @return \Illuminate\Http\JsonResponse JSON response indicating verification result
     */
    public function verifyPassword(Request $request): JsonResponse
    {
        // Validate password input
        $request->validate([
            'password' => ['required', 'string'],
        ]);

        // Check if provided password matches current user's hashed password
        if (Hash::check($request->password, Auth::user()->password)) {
            return response()->json([
                'valid' => true,
                'message' => 'Password verified successfully'
            ]);
        }

        // Return verification failure
        return response()->json([
            'valid' => false,
            'message' => 'Password verification failed'
        ], 401);
    }

    /**
     * Process user logout request.
     * 
     * Clears user session data and redirects to login page.
     * 
     * @param \Illuminate\Http\Request $request The HTTP request
     * @return \Illuminate\Http\RedirectResponse Redirect to login page
     */
    public function logout(Request $request): RedirectResponse
    {
        // Clear authentication session
        Auth::logout();
        
        // Invalidate session to remove all session data
        $request->session()->invalidate();
        
        // Regenerate CSRF token for security
        $request->session()->regenerateToken();

        // Redirect to login page
        return redirect('/login');
    }
}
