# Laravel Sail Application

This is a Laravel application configured to run using Docker with Laravel Sail.
## Prerequisites

- [Docker](https://www.docker.com/get-started)
- [Laravel Sail](https://laravel.com/docs/11.x/sail)

## Getting Started

To get started with this application:


1. Install dependencies:

    ```bash
   ./vendor/bin/sail composer install
    ```

3. Copy the `.env.example` file to `.env`:

    ```bash
    cp .env.example .env
    ```

4. Generate an application key:

    ```bash
    ./vendor/bin/sail artisan key:generate
    ```

5. Start the Sail environment:

    ```bash
    ./vendor/bin/sail up
    ```

6. Run migrations:

    ```bash
    ./vendor/bin/sail artisan migrate
    ```


## Environment Variables

Make sure to configure your `.env` file properly. Key settings include:

- `DB_CONNECTION=mysql`
- `DB_HOST=mysql`
- `DB_PORT=3306`
- `DB_DATABASE=laravel`
- `DB_USERNAME=sail`
- `DB_PASSWORD=password`

