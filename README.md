# Common Auth Package for Laravel

A highly professional, dynamic, and reusable Laravel Authentication package built specifically to support **Email, Mobile, or Username** logins seamlessly, integrated deeply with `spatie/laravel-permission`.

## Features
- **Dynamic Login**: Users can log in using Email, Mobile, or Username via a single input field.
- **Configurable Registration**: Dynamically define required registration fields across different projects without altering the package code.
- **Smart Database Migrations**: Safely extends the default Laravel `users` table without destroying existing data.
- **Spatie Integration**: Automatically assigns default roles upon registration.
- **Password Resets**: Fully integrated with Laravel's native Password Broker.
- **Clean Architecture**: Built following STRICT SOLID principles and thin controllers.

## Requirements
- PHP 8.1+
- Laravel 10.0 / 11.0 / 12.0
- Spatie Laravel Permission ^6.0

## Installation

1. Install the package via Composer (once published):
```bash
composer require arjunyuvanesh/common-auth
```

2. Run the automated install command. This will publish the configuration and migrations:
```bash
php artisan common-auth:install
```

3. Run the database migrations to safely add `username` and `mobile` to your `users` table:
```bash
php artisan migrate
```

4. Seed the default Spatie roles required by the package:
```bash
php artisan common-auth:seed-roles
```

## Setup

### 1. Update your User Model
Add the `HasCommonAuth` trait to your `App\Models\User` class. This automatically injects the Spatie roles and helper methods:

```php
namespace App\Models;

use Arjunyuvanesh\CommonAuth\Traits\HasCommonAuth;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasCommonAuth; // Add this line!
    
    protected $fillable = [
        'name', 'email', 'password', 'username', 'mobile'
    ];
}
```

### 2. Configuration
You can modify the package behavior in `config/common-auth.php`:

```php
return [
    'route_prefix' => 'api/auth', // Change the URL prefix dynamically
    'default_role' => 'User',     // Default role assigned on registration
    
    // Dynamically define what fields are required during registration
    'registration_fields' => [
        'name'     => 'required|string|max:255',
        'mobile'   => 'required|string|unique:users',
        'password' => 'required|string|min:8|confirmed',
    ]
];
```

## API Endpoints

Once installed, the following endpoints are automatically available (prefixed by your config):

- `POST /common-auth/login` - Accepts `login` (email/mobile/username) and `password`.
- `POST /common-auth/register` - Accepts your dynamically configured registration fields.
- `POST /common-auth/logout` - Logs out the authenticated user.
- `POST /common-auth/forgot-password` - Sends a password reset link (requires `email`).
- `POST /common-auth/reset-password` - Resets the password.

## Testing
Run the tests using Orchestra Testbench:
```bash
vendor/bin/phpunit
```

## License
The MIT License (MIT).
