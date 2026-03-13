# Royal Mediterranean Cuisine — PHP Website

Multi-language restaurant website for [samuiroyal.com](https://samuiroyal.com), serving authentic Mediterranean cuisine in Koh Samui, Thailand.

## Features

- 7-language support: English, Turkish, Arabic, Thai, German, French, Italian
- Two branch locations: Chaweng Beach & Lamai Beach
- Dynamic menu management with admin panel
- WhatsApp reservation integration
- SEO-ready with sitemap.xml and robots.txt
- Clean URL routing with language prefixes (`/en/`, `/tr/`, `/ar/`, etc.)

## Tech Stack

- PHP (no framework, lightweight MVC-style architecture)
- MySQL with PDO
- Multi-language routing via URL path segments

## Setup

1. Clone the repo
2. Copy `config/db.example.php` → `config/db.php` and fill in your database credentials
3. Import `setup.sql` to initialize the database schema
4. Configure your web server to point root at the project folder (Apache `.htaccess` included)

## Project Structure

```
royal-php/
├── admin/          # Admin panel (menu, content, image management)
├── config/         # App config & database connection
├── includes/       # Shared partials (header, footer)
├── lang/           # Translation strings (en, tr, ar, th, de, fr, it)
├── pages/          # Page templates (home, about, menu-chaweng, menu-lamai)
├── public/         # Static assets (CSS, JS, images, menu photos)
├── router.php      # URL router & language dispatcher
└── sitemap.php     # Dynamic sitemap generator
```

## Locations

| Branch | Address | Phone |
|--------|---------|-------|
| Chaweng | 4/3 Moo 3, Chaweng Beach Road, Koh Samui, 84320 | +66 98 256 7595 |
| Lamai | 124/7 Moo 3, Lamai Beach Road, Koh Samui, 84310 | +66 94 335 8904 |
