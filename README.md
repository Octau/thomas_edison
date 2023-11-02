### Command need to run

1. copy `.env.example` to `.env` and do adjustment if needed
2. run `php artisan key:generate` to generate or rotate application key
3. run `php artisan migrate` or `php artisan migrate:fresh` to reset the DB
4. run `php artisan passport:keys --force` to generate oauth2 keys
5. run `php artisan passport:client --password --provider users` to support oauth2 client on `User`
6. run `php artisan passport:client --personal` to support personal access token (OPTIONAL)
7. run `php artisan db:seed` if any (OPTIONAL)
8. run `php artisan config:cache` to cache config (OPTIONAL)

### Run Psalm

```
./vendor/bin/psalm
```

### Using Telescope

Run `php artisan telescope:publish` and comment out the service provider on `AppServiceProvider`.
We disable it by default because it has bugs, use when you are on debugging mode only

### Command that has been run to set up this project

```
php artisan passport:install --uuids
php artisan horizon:install
php artisan telescope:install
```
