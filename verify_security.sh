#!/bin/bash

# Security Implementation Verification Script
# This script verifies all security improvements have been correctly implemented

echo "🔒 SECURITY IMPLEMENTATION VERIFICATION"
echo "======================================="
echo ""

# Color codes
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

pass() {
    echo -e "${GREEN}✓ PASS${NC}: $1"
}

fail() {
    echo -e "${RED}✗ FAIL${NC}: $1"
}

check() {
    echo -e "${YELLOW}→ Checking${NC}: $1"
}

# Test 1: Session Timeout
check "Session timeout is 30 minutes"
if grep -q "SESSION_LIFETIME=30" .env; then
    pass "Session timeout set to 30 minutes"
else
    fail "Session timeout not set to 30 minutes"
fi
echo ""

# Test 2: Session Encryption
check "Session encryption is enabled"
if grep -q "SESSION_ENCRYPT=true" .env; then
    pass "Session encryption is enabled"
else
    fail "Session encryption is not enabled"
fi
echo ""

# Test 3: Session Secure Cookie
check "Session secure cookie flag"
if grep -q "SESSION_SECURE_COOKIE=true" .env; then
    pass "Session secure cookie is enabled"
else
    fail "Session secure cookie is not enabled"
fi
echo ""

# Test 4: Session SameSite
check "Session SameSite policy"
if grep -q "SESSION_SAME_SITE=strict" .env; then
    pass "Session SameSite policy is set to 'strict'"
else
    fail "Session SameSite policy is not set to 'strict'"
fi
echo ""

# Test 5: Password Validation
check "Password minimum length enforcement"
if grep -q "min:16" app/Http/Controllers/AuthController.php; then
    pass "Password minimum 16 characters enforced"
else
    fail "Password minimum length not enforced"
fi
echo ""

# Test 6: Rate Limiting on Login
check "Rate limiting on login endpoint"
if grep -q "throttle:5,900" routes/web.php; then
    pass "Rate limiting (5 attempts/900s) on login"
else
    fail "Rate limiting on login not found"
fi
echo ""

# Test 7: Rate Limiting on OTP
check "Rate limiting on OTP endpoints"
if grep -q "throttle:3,900" routes/web.php && grep -q "generate-otp" routes/web.php; then
    pass "Rate limiting on OTP generation (3 attempts/900s)"
else
    fail "Rate limiting on OTP not found"
fi
echo ""

# Test 8: Security Headers Middleware
check "Security headers middleware"
if [ -f "app/Http/Middleware/SecurityHeadersMiddleware.php" ]; then
    pass "SecurityHeadersMiddleware file exists"
else
    fail "SecurityHeadersMiddleware file not found"
fi
echo ""

# Test 9: Sanitization Middleware
check "Input sanitization middleware"
if [ -f "app/Http/Middleware/SanitizeInputMiddleware.php" ]; then
    pass "SanitizeInputMiddleware file exists"
else
    fail "SanitizeInputMiddleware file not found"
fi
echo ""

# Test 10: Middleware Registration
check "Middleware registration in bootstrap/app.php"
if grep -q "SecurityHeadersMiddleware" bootstrap/app.php && grep -q "SanitizeInputMiddleware" bootstrap/app.php; then
    pass "Both middleware registered in bootstrap/app.php"
else
    fail "Middleware not properly registered"
fi
echo ""

# Test 11: HTTPS in AppServiceProvider
check "HTTPS enforcement in AppServiceProvider"
if grep -q "URL::forceScheme('https')" app/Providers/AppServiceProvider.php; then
    pass "HTTPS enforcement configured"
else
    fail "HTTPS enforcement not found"
fi
echo ""

# Test 12: HSTS Header
check "HSTS header in SecurityHeadersMiddleware"
if grep -q "Strict-Transport-Security" app/Http/Middleware/SecurityHeadersMiddleware.php; then
    pass "HSTS header configured"
else
    fail "HSTS header not found"
fi
echo ""

# Test 13: Tests exist
check "Security test file"
if [ -f "tests/Feature/Security/SecurityImplementationTest.php" ]; then
    pass "Security tests file exists"
else
    fail "Security tests file not found"
fi
echo ""

# Summary
echo "======================================="
echo "🎯 VERIFICATION SUMMARY"
echo "======================================="
echo ""
echo "Run the following to test functionality:"
echo ""
echo "  php artisan test tests/Feature/Security/SecurityImplementationTest.php"
echo ""
echo "Or run all tests:"
echo ""
echo "  php artisan test"
echo ""
echo "Verify security headers in development:"
echo ""
echo "  php artisan serve"
echo "  curl -I http://localhost:8000"
echo ""
echo "======================================="
