# Frontend Integration & Setup Guide

This document is your complete master guide. Keep this file handy! It explains exactly how to install your package into a brand new project and how to connect your frontends (Web or Mobile) to every single endpoint.

---

## 1. Installing into a New Project

When you start a brand new Laravel project, here is exactly how you install your authentication engine:

1. **Install the package:**
```bash
composer require arjunyuvanesh/common-auth
```

2. **Publish the configuration and migrations:**
```bash
php artisan common-auth:install
```

3. **Run database migrations (adds username/mobile to users table):**
```bash
php artisan migrate
```

4. **Update the User Model (`app/Models/User.php`):**
Add the `HasCommonAuth` trait and `SoftDeletes` (if you want soft deletes).
```php
namespace App\Models;

use Arjunyuvanesh\CommonAuth\Traits\HasCommonAuth;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasCommonAuth, SoftDeletes;
    
    protected $fillable = [
        'name', 'email', 'password', 'username', 'mobile'
    ];
}
```

---

## 2. Using the API (For Mobile Apps: iOS / Android / Flutter)

Mobile apps use the `/api/common-auth/*` endpoints. They rely on **JSON Bearer Tokens** instead of browser cookies.

### A. Register a New User
```javascript
fetch('https://yourdomain.com/api/common-auth/register', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
    body: JSON.stringify({
        name: 'John Doe',
        email: 'john@example.com',
        mobile: '1234567890',
        username: 'johndoe',
        password: 'StrongPassword123!',
        password_confirmation: 'StrongPassword123!'
    })
});
```

### B. Login (To get the Token!)
```javascript
fetch('https://yourdomain.com/api/common-auth/login', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
    body: JSON.stringify({
        login: 'john@example.com', // Can be email, mobile, or username!
        password: 'StrongPassword123!'
    })
})
.then(res => res.json())
.then(data => {
    console.log(data.message); // "Successfully logged in."
    
    // IMPORTANT: Save this token in your mobile app's secure storage!
    const token = data.token; 
});
```

### C. Get User Profile (Requires Token)
For all authenticated routes, you MUST pass the token in the headers.
```javascript
fetch('https://yourdomain.com/api/common-auth/me', {
    method: 'GET',
    headers: { 
        'Accept': 'application/json',
        'Authorization': 'Bearer YOUR_SAVED_TOKEN_HERE' 
    }
});
```

### D. Change Password (Requires Token)
```javascript
fetch('https://yourdomain.com/api/common-auth/password', {
    method: 'PUT',
    headers: { 
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Authorization': 'Bearer YOUR_SAVED_TOKEN_HERE' 
    },
    body: JSON.stringify({
        current_password: 'StrongPassword123!',
        password: 'NewStrongPassword456!',
        password_confirmation: 'NewStrongPassword456!'
    })
});
```

### E. Logout (Requires Token)
This securely destroys the token in the database so it can never be used again.
```javascript
fetch('https://yourdomain.com/api/common-auth/logout', {
    method: 'POST',
    headers: { 
        'Accept': 'application/json',
        'Authorization': 'Bearer YOUR_SAVED_TOKEN_HERE' 
    }
});
```

---

## 3. Using Web Browsers (For React / Vue / Blade Websites)

Websites use the `/common-auth/*` endpoints (Notice there is no `/api`). Websites rely on **Cookies**. You do NOT need to handle tokens manually!

> **CRITICAL:** If you are building a React/Vue SPA on the same domain, you must first call Laravel's `/sanctum/csrf-cookie` endpoint to get a CSRF token before logging in. Axios does this automatically for you.

### A. Login (Cookie Based)
```javascript
axios.post('/common-auth/login', {
    login: 'johndoe',
    password: 'StrongPassword123!',
    remember: true
}).then(response => {
    // The browser automatically saves the secure session cookie!
    console.log("Logged in!");
});
```

### B. Forgot Password
```javascript
axios.post('/common-auth/forgot-password', {
    email: 'john@example.com'
}).then(response => {
    console.log("Password reset link sent to your email!");
});
```

### C. Reset Password
When the user clicks the link in their email, they submit this form:
```javascript
axios.post('/common-auth/reset-password', {
    token: 'THE_TOKEN_FROM_THE_URL',
    email: 'john@example.com',
    password: 'NewStrongPassword456!',
    password_confirmation: 'NewStrongPassword456!'
});
```

### D. Submit OTP (Email Verification)
```javascript
// Validates the 6-digit OTP sent to their email
axios.post('/common-auth/email/verify-otp', {
    otp: '123456'
});
```

### E. Update Profile
```javascript
// The user is already logged in, so the browser sends the cookie automatically!
axios.put('/common-auth/profile', {
    name: 'John Smith',
    mobile: '9876543210'
});
```

### F. Delete Account
```javascript
axios.delete('/common-auth/account').then(response => {
    console.log("Account has been soft-deleted and you have been logged out!");
});
```
