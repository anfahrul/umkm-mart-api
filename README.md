## How to Run Umkmart API on Local

1. Clone Repository

```
git clone https://github.com/anfahrul/umkm-mart-api.git
```

2. Install Depedencies

```
composer install
```

3. Setup .env file

To setup your `.env`, kindly duplicate your `.env.example` file and rename the duplicated file to `.env`.

4. Setup Database

On your `.env` file, locate this block of code below.

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=umkm_mart_api
DB_USERNAME=root
DB_PASSWORD=
```

5. Migrate, running seed, and generate JWT key

```
php artisan migrate:fresh --seed
php artisan key:generate
```

6. Running server

```
php artisan serve
```

Enjoy team!
