# tms-server

Hello There!

I have created 2 separate repositories so that it fit both restful api as well as the CRUD operation requirements. Below are some guideline to install laravel.

##Laravel Installation

```
git clone https://github.com/tepafril/tms-server.git
cd tms-server
composer install
php artisan migrate:fresh --seed
php artisan db:seed --class=LevelSeeder
php artisan serve
```

##POSTMAN Docs

Because we are using `andreaselia/laravel-api-to-postman` package to auto generate postman docs, please follow below command to generate it.

```
php artisan export:postman
```

Then you can see the auto-generated json in /storage/app/postman/###.json
Please also note that there are some configuration to make the docs more readable. Please follow the package owner at https://github.com/andreaselia/laravel-api-to-postman

`Documentation`
