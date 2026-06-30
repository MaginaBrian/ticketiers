# Ticket Tiers — Backend Intern Take-Home

A clean CRUD slice for ticket tiers, built around Data classes, single-purpose
Actions, a Query Builder index, a shaping-only Resource, and a permission-backed
Policy, following the conventions in the brief.

## Status

Built and tested locally on PHP 8.3 / Laravel 10 / SQLite.
Two small bugs surfaced and were fixed during this verification pass, both in
`app/Models/User.php`:
- Missing the `HasFactory` trait, which broke `User::factory()` in the test
  `beforeEach`.
- Missing Sanctum's `HasApiTokens` trait, which the routes need since every
  endpoint sits behind `auth:sanctum` middleware.

## Setup

composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate
php artisan test
