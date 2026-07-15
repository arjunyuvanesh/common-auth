# Common Auth Package for Laravel

A highly professional, dynamic, and enterprise-grade Laravel Authentication package. Built specifically to support **Email, Mobile, or Username** logins seamlessly, integrated deeply with `spatie/laravel-permission`, Dual Email Verification, and Dynamic Rate Limiting.

## Features
- **Headless Design**: Provides 100% of the backend logic. You build the UI in React, Vue, Blade, or Mobile SDKs.
- **Dynamic Login**: Users log in using Email, Mobile, or Username via a single input field.
- **Dual Architecture**: Native support for both Web Browsers (Session/CSRF) and Mobile Apps (Sanctum Tokens).
- **Dual Email Verification**: Supports both Magic Links and stateless 6-digit OTPs (stored efficiently in Cache).
- **Account Management**: Endpoints for updating profiles, changing passwords securely, and account deletion.
- **Enterprise Security**: Mathematically strict password enforcement and dynamic Brute-Force Rate Limiting.

---

## Installation & Setup

1. **Install the package:**
```bash
composer require arjunyuvanesh/common-auth
```

2. **Publish config and migrations:**
```bash
php artisan common-auth:install
```

3. **Run migrations:**
```bash
php artisan migrate
```

4. **Update your User Model (`app/Models/User.php`):**
Add the `HasCommonAuth` trait to inject the package engine into your app.
```php
namespace App\Models;

use Arjunyuvanesh\CommonAuth\Traits\HasCommonAuth;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasCommonAuth; // Add this line!
    
    // Add any custom fields you want users to submit during registration
    protected $fillable = [
        'name', 'email', 'password', 'username', 'mobile', 'address'
    ];
}
```

---

## How to Connect Your Frontend

This package exposes two identical sets of endpoints. 
- Use the `/common-auth/*` endpoints for **Web Browsers** (Blade, React/Vue SPAs using Sanctum stateful domains).
- Use the `/api/common-auth/*` endpoints for **Mobile Apps** (iOS/Android/Flutter using Bearer Tokens).

### Scenario 1: Connecting a Web Browser (React / Vue / Blade)
Web browsers rely on Cookies and CSRF tokens.

1. **Submit the Login Request:**
Your frontend should make an AJAX request to `/common-auth/login`.
```javascript
// Example using Axios
axios.post('/common-auth/login', {
    login: 'arjun15247@gmail.com', // Can be email, mobile, or username
    password: 'SuperSecretPassword123!',
    remember: true
}).then(response => {
    console.log(response.data.message); // "Successfully logged in."
    console.log(response.data.user);    // The user's profile
});
```

2. **Accessing Authenticated Routes:**
Because Web Browsers use cookies, once you log in, your browser automatically saves the session. You can immediately call `/common-auth/me` without passing any tokens!

### Scenario 2: Connecting a Mobile App (React Native / Flutter / Swift)
Mobile apps cannot use cookies or CSRF tokens. They must use the `/api` prefix.

1. **Submit the Login Request:**
Make a JSON request to `/api/common-auth/login`.
```javascript
fetch('https://yourwebsite.com/api/common-auth/login', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
    body: JSON.stringify({
        login: 'arjun15247@gmail.com',
        password: 'SuperSecretPassword123!'
    })
})
.then(res => res.json())
.then(data => {
    // SAVE THIS TOKEN IN YOUR MOBILE APP (e.g. AsyncStorage or SecureStorage)
    const token = data.token; 
});
```

2. **Accessing Authenticated API Routes:**
To get the user's profile or change their password, you must pass the token in the `Authorization` header.
```javascript
fetch('https://yourwebsite.com/api/common-auth/me', {
    method: 'GET',
    headers: {
        'Accept': 'application/json',
        'Authorization': 'Bearer ' + token // Pass the token here!
    }
});
```

---

## Complete API Reference

### Guest Endpoints
*Available under both `/common-auth/` and `/api/common-auth/`*

- `POST /login` - Accepts `login` (email/mobile/username) and `password`. Returns a Token if called via `/api`.
- `POST /register` - Accepts your dynamically configured registration fields (see `config/common-auth.php`).
- `POST /forgot-password` - Sends a password reset link (requires `email`).
- `POST /reset-password` - Resets the password (requires `token`, `email`, `password`, `password_confirmation`).

### Authenticated Endpoints
*Web uses Cookies. APIs require `Authorization: Bearer <token>`*

- `GET /me` - Returns the current user's profile.
- `POST /logout` - Logs the user out and securely revokes API tokens.
- `PUT /profile` - Updates the user profile. Accepts `name`, `email`, `mobile`, `username`.
- `PUT /password` - Securely changes the password. Accepts `current_password`, `password`, `password_confirmation`.
- `DELETE /account` - Permanently deletes the user and revokes all tokens.

### Verification Endpoints
- `GET /email/verify/{id}/{hash}` - Magic Link verification endpoint.
- `POST /email/verify-otp` - Submits the 6-digit OTP. Accepts `otp`.
- `POST /email/verification-notification` - Resends the OTP/Link to the user's email.
