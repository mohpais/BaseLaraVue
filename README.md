<div align="center">
    <h1>Base Laravel Vue Tamplate</h1>
</div>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# About this app

Type description

## Technology Stack:
- <b>Backend Service API:</b> Developed using **[Laravel](https://laravel.com/)**
- <b>Frontend Framework:</b> Built with **[Vue.js](https://vuejs.org/)**
- <b>Database Management:</b> Utilizing 
**[MySQL](https://www.mysql.com/)**

# Steps to prepare the project

1. Clone project on Github [Base Laravel Vue Template](https://github.com/mohpais/BaseLaraVue.git)
```
git clone https://github.com/mohpais/BaseLaraVue.git
```
2. Open folder using command 
```
cd BaseLaraVue
```
3. Install composer for laravel 
```
composer install
```
Note: If installing your receive the following error
```
Your requirements could not be resolved to an installable set of packages.

Problem 1
- Root composer.json requires maatwebsite/excel 3.1 -> satisfiable by maatwebsite/excel[3.1.0].
- maatwebsite/excel 3.1.0 requires php ^7.0 -> your php version (8.2.8) does not satisfy that requirement.
```
Open php.ini, search for extension gd and zip Uncomment line, then install composer again.
If success then dump autoload.
```
composer dump-autoload
```
4. Copy .env.example using command 
```
copy .env.example .env
```
5. Generate new key
```
php artisan key:generate
```
6. Install package for frontend [Vue.Js](https://vuejs.org/) 
```
npm install
```
7. Connect Database by updating .env like this:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=YOUR_DB_NAME
DB_USERNAME=YOUR_DB_USERNAME
DB_PASSWORD=YOUR_DB_PASSWORD
```
8. Migrate the database using this command:
```
php artisan migrate
```
9. Then generate JWT secret key like:
```
php artisan jwt:secret
```
10. Running laravel and vue with seperate 2 terminal 
```
php artisan serve
npm run dev
```