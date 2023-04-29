## Installation Guide

### Local setup
- Run `composer install` to install the application packages
- Start the docker process with `docker-compose up`

In a new shell 
- Copy `.env.example` to `.env`
    - run `docker-compose exec app cp .env.example .env`
- run `docker-compose exec app php artisan key:generate` to generate a new application key for your local environment
- run `docker-compose exec app php artisan config:clear` to clear out configuration cache
- Run the database migrations with `docker-compose exec app php artisan migrate`
- To seed some local users into the database run `docker-compose exec app php artisan db:seed`
    - This will add a user with the email `test@test.com` and password `kaktus1992` to your local database
