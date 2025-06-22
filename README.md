# Laravel JWT API Skeleton

A RESTful API application built with Laravel, designed for secure, token-based user authentication and robust resource management.

# Requirements

- PHP >= 8.2
- MySQL or any Laravel compatible DB engine.

## Features

- **User Authentication**
  - Registration and login endpoints. (Registration can be removed if not necessary)
  - User logout and profile update functionality. (Profile update can be removed if not necessary)
  - JWT cookie-based authentication for secure access.
  - Custom Cookie lifetime via COOKIE_LIFETIME .env variable.
- **Localization**<br>
  Multilingual support via url.
  - All API routes are prefixed with a `{lang}` parameter. (E.g. {{app_url}}/api/{version}/**`en`**/user-actions)
  - Automatic locale setting via the `setLocale` middleware.
  - Add as many languages as you need to support in your app
- **Role-Based Permissions**
  - Fine-grained access control using custom route permissions. Multiple permissions per route.
- **Modular Route Structure**<br>
  - Grouped routes for user actions and default controller responses. (see api routes)
  - Easy extension for additional modules and controllers. Just create more groups and add any middleware as per your needs.
- **Secure API Endpoints**
  - All protected routes under `jwt` middleware require valid JWT tokens. For improved security JWT is set into the HttpOnly cookie and not in the JSON response.
  - The `hasActions` Middleware checks for JWT validity and user actions.
  - Additional info for jwt features can be found in `jwt.php` file in config folder, or in Additional Docs section in this file.
- **Default API Response Facade**
  - The ApiResponse facade registered in the AppServiceProvider provides a common format for the API response, all devs can use the same facade without worring for custom response formats.
  - The ApiResponseFactory class provides an easy and convenient way to switch between different API response formats based on your own conditions.
- **Default Web Access Disabled**
  - Web access to the app is disabled by default. To enable this just modify `web.app` in routes folder as per your needs.
- **API Version Handling**
  - The API comes with a middleware to help validate API versions. This, along with the ApiResponseFactory feature lets you handle different response formats depending on which API version you are requesting in url. This is an example of a versioned url
  > **{{app_url}}/api/`{version}`/{lang}/user-actions**
- **Temporary Tables**
  - A utility service provided to generate temporary tables on-the-go for single use during request processing.
- **All Laravel Features you Know and Love**
  - The project is entirely based on Laravel 12. All artisan commands are available as usually.

## Additional Docs
The app makes use of `tymon/jwt` and `"spatie/laravel-permission"`. These are the official sites:

- [Tymon/jwt](https://jwt-auth.readthedocs.io/en/develop)
- [Spatie/laravel-permission](https://spatie.be/docs/laravel-permission/v6/introduction)