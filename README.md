# Laravel Sail Application

This is a Laravel application configured to run using Docker with Laravel Sail.
## Prerequisites

- [Docker](https://www.docker.com/get-started)
- [Laravel Sail](https://laravel.com/docs/11.x/sail)

## Getting Started

To get started with this application:


1. Install dependencies:

    ```bash
   composer install
    ```

2. Copy the `.env.example` file to `.env`:

    ```bash
    cp .env.example .env
    ```

3. Generate an application key:

    ```bash
    ./vendor/bin/sail artisan key:generate
    ```

4. Start the Sail environment:

    ```bash
    ./vendor/bin/sail up
    ```

5. Run migrations and seeders:

    ```bash
    ./vendor/bin/sail artisan migrate --seed
    ```
## Commands
1. Add the first librarian:

```bash
./vendor/bin/sail artisan app:add-first-librarian
```

## Environment Variables

Make sure to configure your `.env` file properly. Key settings include:

- `DB_CONNECTION=mysql`
- `DB_HOST=mysql`
- `DB_PORT=3306`
- `DB_DATABASE=laravel`
- `DB_USERNAME=sail`
- `DB_PASSWORD=password`

