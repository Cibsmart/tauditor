# Anambra Payroll Project

A Laravel Application for Processing and Managing Payroll in Anambra State, Nigeria.

![](https://gitlab.com/genesys_development/anambra_payroll/anambra_payroll/-/raw/master/contents/Dashboard.png)

## Tech Stack
Backend - Laravel
Frontend - Vue & Inertia (Inertia removes duplication of routes & authentication on the frontend)
Styling - Tailwind CSS (Just like bootstraps, but provides only utilities)
Database - MySQL or MariaDB(Latest Version)

## Installation and Setup (for Frontend and Backend Developers)

Clone the repo locally:

```sh
git clone https://gitlab.com/genesys_development/anambra_payroll/anambra_payroll.git
```

then change directory to anambra_payroll

```sh
cd anambra_payroll
```

Install PHP dependencies:

```sh
composer install
```

Install JavaScript dependencies

```sh 
yarn
```
or
```sh
npm ci
```



Build Assets

```sh
yarn run dev
```
or
```sh
npm run dev
```
Continously Build Assets on each change

```sh
npm run watch
```

Setup configuration:

```sh
cp .env.example .env
```

Generate application key:

```sh
php artisan key:generate
```

Create a MySQL database, and update the Database section in your .env file accordingly.


Run database migrations:

```sh
php artisan migrate
```

Run database seeder

```sh
php artisan db:seed
```


You should be able to access the homepage of the app, if you are using a separate server (e.g. apache or nginx). 

![](https://gitlab.com/genesys_development/anambra_payroll/anambra_payroll/-/raw/master/contents/Login.png)

But if you are using the dev server then run (the output will give the address):
```sh
php artisan serve
```

You are ready to go! Visit the app on your browser, and login with:

- **Username:** john@payroll.com
- **Password:** password