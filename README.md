# Royal Turkish Cuisine — PHP Website

Multi-language restaurant website for [samuiroyal.com](https://samuiroyal.com), serving Koh Samui, Thailand.

## Features

- Multi-language support: EN, TR, AR, TH, DE, FR, IT
- Two branch locations: Chaweng & Lamai
- Dynamic menu system with admin panel
- SEO-ready with sitemap and robots.txt
- WhatsApp reservation integration

## Tech Stack

- PHP (no framework, clean MVC-style)
- MySQL / PDO
- Multi-language routing via URL segments (`/en/`, `/tr/`, etc.)

## Setup

1. Clone the repo
2. Copy `config/db.example.php` to `config/db.php` and fill in your DB credentials
3. Import `setup.sql` to initialize the database schema
4. Point your web server root to the project folder

## Structure

```
royal-php/
├── admin/          # Admin panel
├── config/         # App config & DB connection
├── includes/       # Shared partials (header, footer)
├── lang/           # Translation files
├── pages/          # Page controllers
├── public/         # Static assets (CSS, JS, images)
└── router.php      # URL router
```

## Locations

- **Chaweng:** 4/3 Moo 3, Chaweng Beach Road, Koh Samui
- **Lamai:** 124/7 Moo 3, Lamai Beach Road, Koh Samui
