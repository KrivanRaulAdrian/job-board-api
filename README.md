[![Continuous Integration](https://github.com/KrivanRaulAdrian/job-board-api/actions/workflows/continuous-integration.yml/badge.svg?branch=master)](https://github.com/KrivanRaulAdrian/job-board-api/actions/workflows/continuous-integration.yml)

<p align="center">
  <img align="center" height="200" src="public/symfony.png">
</p>

<h1 align="center">Job Board API</h1>

The Job Board API is a RESTful web service built using Symfony, which allows clients to manage job postings and applications for a job board. The API provides endpoints for company and job posting management, applicant management, job application management, and reporting features.

The API has user authentication features, where clients can sign up and log in to the application via token to access the routes. The API also allows clients to search and filter job postings based on the title, company, location, and other relevant criteria.

The Job Board API consists of entities such as user, company, job, and applicant, and the fields of these entities are customizable. The API provides well-structured routes for clients to perform CRUD operations on entities.

## Requirments

- PHP 8.2
- <a href="https://getcomposer.org/" rel="nofollow">Composer</a>
- <a href="https://www.mamp.info/en/mamp/windows/" rel="nofollow">MAMP</a>
- <a href="https://symfony.com/download" rel="nofollow">Symfony CLI</a> (optional)

## Install

1. Clone the repository:

```
git clone git@github.com:KrivanRaulAdrian/job-board-api.git
```

2. Access the directory:

```
cd job-board-api/
```

3. Install the Composer dependencies:

```
composer install
```

4. Go to MySQL and create the database `job-board-api `
5. Create a file `.env.local` and add your database connection. Example:

```dotenv
DATABASE_URL="mysql://root:@localhost:3306/job-board-api"
```

6. Create the tables:

```
php bin/console doctrine:migrations:migrate
```

7. Run the application:

```
symfony server:start
# or
php -S localhost:8000 -t public
```

8. Go to http://localhost:8000

**\*Note:** To generate the JWT security [keypair], use a Linux container running the following command: `docker-compose run -it php-fpm php bin/console lexik:jwt:generate-keypair`\*

## Routes

To access the API documentation, go to http://localhost:8000/api/doc.

<p align="center">
  <img align="center" src="public/job-board-api.png">
</p>

## Quality Tools

You can run PHP CS Fixer to check the code style and PHPStan for static analysis.

## Code Style

Install PHP CodeSniffer:

```
composer require squizlabs/php_codesniffer
```

Run PHP CodeSniffer:

```
./vendor/bin/phpcs --standard=PSR12 src/
```

Run PHP CodeSniffer Fixer:

```
 ./vendor/bin/phpcbf --standard=PSR12 src/
```

## Static Analysis

Install PHPStan:

```
composer require --dev phpstan/phpstan-symfony
```

If you also install [phpstan/extension-installer](https://github.com/phpstan/extension-installer) then you're all set!

<details>
  <summary>Manual installation</summary>

If you don't want to use `phpstan/extension-installer`, include extension.neon in your project's PHPStan config:

```
includes:
    - vendor/phpstan/phpstan-symfony/extension.neon
```

To perform framework-specific checks, include also this file:

```
includes:
    - vendor/phpstan/phpstan-symfony/rules.neon
```

</details>

Run PHPStan:

```
php vendor/bin/phpstan analyze
```

## Unit Tests

Install PHPUnit

```
composer require --dev symfony/test-pack
```

Run Unit Tests

```
php bin/phpunit
```

<details>

This command automatically runs your application tests. Each test is a PHP class ending with "Test" (e.g. `BlogControllerTest`) that lives in the `tests/` directory of your application.

</details>

<hr/>

## Learn More

To know more about the development of Symfony applications, [check the official documentation](https://symfony.com/doc/current/index.html).
