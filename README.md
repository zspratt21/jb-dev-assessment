# JuiceBox API Developer Assessment
This is my implementation of a simple Blog API.

## Requirements
- PHP 8.3
- Composer
- NodeJS(optional: for running the scripts in package.json)
- Docker & Docker Compose

## Features
- Redis for caching and queueing jobs
- Mailpit for catching emails
- Sentry integration

## Installation and Setup
1. Begin by cloning this repository with git or an IDE of your choice
2. Run `composer install`
3. Run `cp .env.example .env`
4. Set the SENTRY_DSN variable to your Sentry DSN's address if you want to use sentry.
5. Modify any other variables in the .env file as needed.
6. Run `php artisan key:generate` or use the generate-app-key utility script to setup the application key.
7. if using docker(recommended), run `docker-compose up -d` to start the provided development environment.
8. Run `php artisan migrate` from within the web container or run the utility script provided in package.json'.

## Usage
When using docker and the provided example environment, you can access the application on http://localhost:800/

Included in the project is a set of utility scripts in package.json that can either be run from your IDE if you have set up a Node.js interpreter or by running `npm <script-name>` from your terminal.

### Postman Collection
A postman collection has been provided in the root of the project called endpoints.postman_collection.json. You can import this collection into postman to test the endpoints. Once imported, create a new environment and environment variable called app_url with the value of the url you are accessing, then after sending the login request, create another environment variable called bearer_token and copy the provided token from the login response into it.

### Authentication
The API uses bearer tokens for authentication which are issued by the laravel sanctum middleware. To access routes restricted to users only, you will need to login and copy the bearer token provided in the response. if using the provided postman endpoints, you can set the bearer token in the preconfigured environment variable.

### Endpoints
All endpoints mentioned in the brief can be utilized via their respective url and request type in addition to the following:
1. GET / - simple home page which shows the installed version of laravel
2. GET /api/users - view all users
3. GET /api/users/{id}/comments - view all comments by a user
4. GET /api/posts/{id}/comments - view all comments on a post
5. POST /api/posts/{id}/comments - create a comment on a post
6. PATCH /api/comments/{id} - update a comment you made
7. DELETE /api/comments/{id} - delete a comment you made

### Running the queue worker
For ease of use, a script has been provided in package.json to run the queue worker within the provided docker environment called docker-web-start-queue. If you are not using docker you can start the queue worker by running `php artisan queue:work` in your terminal.

### Sending the test email
After starting the queue worker you can either run the utility script docker-web-send-test-email in package.json or run `php artisan user:send-test-welcome-email` in your terminal to send a test email.

### Running the tests
A utility script is provided to run the tests from within the docker environment called test. If you are not using docker you can run the tests by running `php artisan test` in your terminal.
