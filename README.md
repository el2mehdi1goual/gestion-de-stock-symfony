# Stock Management System (Symfony 6)

Inventory management web application built with **PHP (Symfony 6)** as part of my engineering studies (**EMSI – 3IIR: Computer Science & Networks**).

The project focuses on **backend development and database design**, with a strong interest in **SQL and data-oriented features** (tracking, alerts, reporting).

## Features (planned modules)
- Authentication & Roles (Admin / Employee)
- Product management (CRUD)
- Supplier management (CRUD)
- Stock movements (IN / OUT tracking)
- Low stock alerts
- Dashboard & reporting (data-driven metrics)

## Tech Stack
- PHP 8 + Symfony 6
- Twig, HTML/CSS, JavaScript
- Doctrine ORM
- MySQL (target DB)
- Linux-friendly project structure

## Getting Started

### Requirements
- PHP 8+
- Composer
- MySQL (or any compatible DB)

### Install & Run
```bash
composer install
cp .env.example .env
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
symfony serve