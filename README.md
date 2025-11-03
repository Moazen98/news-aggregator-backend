# News Aggregator Backend

This project is a Laravel-based backend application developed by **Eng. Mohamad Al Moazen** as part of the **Innoscripta AG Challenge Test**.

## Overview

The application integrates multiple third-party news APIs and aggregates their data into a unified database. You can also use Postman or Laravel Artisan commands to fetch, seed, or schedule news updates.

## Available APIs

There are **4 API resources** implemented in this project:

* **News API** (`news_api`)
* **Guardian API** (`guardian`)
* **New York Times API** (`new_york`)
* **News API AI** (`news_api_ai`)

If a specific API type does not currently have stored news in the database, the application can fetch live data directly from the corresponding third-party API until it is cached locally.

## Setup Instructions

### 1. Installation

```bash
composer install
cp .env.example .env
php artisan key:generate
```

After creating the .env file, make sure to copy the necessary API keys and configuration values from .env.example. These settings are listed at the bottom of .env.example and include API credentials and other environment configurations. Default values are provided for convenience, but you can replace them with your own keys if needed.

### 2. Database Migration

Run the following command to migrate database tables:

```bash
php artisan migrate
```

### 3. Seeding Data

You can populate the database quickly and directly in two ways:

* Using Laravel seeders (it will execute the commands and fetch the data from the third parts):

  ```bash
  php artisan db:seed
  ```
* Or by executing the scheduler to run the data fetching commands:

  ```bash
  php artisan schedule:run
  ```
  here you can execute each commands by alone and pass the options filters to it like:
* news:fetch-guardian
  {--q= : Search keyword}
  {--from= : Start date (YYYY-MM-DD)}
  {--to= : End date (YYYY-MM-DD)}
  {--category= : Category (technology - science - world)}
  {--author= : Author name (Andrew)}


### 4. Postman Collection

A Postman collection is included within the project files at the root directory:


```
/news-aggregator-backend.postman_collection.json
```


You can import this file into Postman to test all API endpoints easily.

### 5. Scheduler Commands

The project includes command-line tasks that can be executed through Laravel’s scheduler. These commands are responsible for fetching and updating news from the integrated APIs
and ensure that the data is
regularly updated from the live data sources.

## Using Postman for Searching

Within Postman, you can perform **search operations** to query stored news or directly fetch results from the third-party APIs in case no local data is available yet.

* You can search by using the endpoint api, exist in the api.php file and pass the filters to the endpoint (type,q,from,source,to,category,author)

## Important Notes

Search results depend on the fields used in the search process or on the available command options.

Since the APIs used are free versions, some may not return full datasets, and certain APIs only allow searching within the last month unless upgraded to a paid plan.

Please note that in some cases, certain fields in the returned data might be null due to API limitations or missing information.

## Author

**Eng. Mohamad Al Moazen**
Project: *Innoscripta AG Challenge Test*

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
