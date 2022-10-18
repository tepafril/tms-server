# tms-server

Hello There!

I have created 2 separate repositories so that it fit both restful api as well as the CRUD operation requirements. Below are some guideline to install laravel.

Server side: `http://localhost:8000`
Client side: `http://localhost:3000`

Please check here for the client `https://github.com/tepafril/tms-client`


## Laravel Installation

```
git clone https://github.com/tepafril/tms-server.git
cd tms-server
composer install
php artisan migrate:fresh --seed
php artisan db:seed --class=LevelSeeder
php artisan serve
```

## Database Configuration

We are using SQLite for demo purpose, so that the review can get up and running very fast. First thing first to do, is to create sqlite file.

```
touch database/database.sqlite
```

## POSTMAN Docs

Because we are using `andreaselia/laravel-api-to-postman` package to auto generate postman docs, please follow below command to generate it.

```
php artisan export:postman
```

Then you can see the auto-generated json in /storage/app/postman/###.json
Please also note that there are some configuration to make the docs more readable. Please follow the package owner at https://github.com/andreaselia/laravel-api-to-postman

## Documentation

We use scribe as a auto-generated docs for readable guideline for other platform development. Please run below line to generate it.

```
php artisan scribe:generate
```
After running above, you can check the doc via default endpoint `http://localhost:8000/docs`


## My Apology
This is not well documented, since I do not have enough time to finish it. Please feel free to let me know your concerns or doubt.

## Diagram Flow
Not enough time to produce.

## Product Spec
Not enough time to produce.

## Technical Spec
Not enough time to produce.
