# Stadtradeln

Eine Web-App zum Tracken von Fahrradtouren und Teamvergleichen.

## Projektstruktur

```
/
├── public/                    # Öffentlich zugängliche Dateien (Document Root)
│   ├── index.php             # Einziger Einstiegspunkt
│   ├── .htaccess             # URL Rewriting
│   ├── css/app.css           # Design-System (einziges Stylesheet)
│   └── js/                   # app.js (UI-Verhalten), dashboard.js, Passwortstärke
│
├── src/                      # PHP-Quellcode
│   ├── Controllers/          # Request Handler
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   ├── TeamController.php
│   │   └── LeaderboardController.php
│   │
│   ├── Models/               # Datenklassen
│   │   ├── User.php
│   │   ├── Team.php
│   │   └── Tour.php
│   │
│   ├── Repository/           # Datenbankzugriff
│   │   ├── UserRepository.php
│   │   ├── TeamRepository.php
│   │   └── TourRepository.php
│   │
│   └── Core/                 # Kernfunktionen
│       ├── Database.php
│       ├── Session.php
│       ├── Router.php
│       ├── View.php
│       └── Icon.php
│
├── templates/                # HTML-Templates
│   ├── layout/
│   │   ├── header.php
│   │   ├── nav.php
│   │   └── footer.php
│   ├── partials/
│   └── pages/
│
├── config/
│   ├── database.example.php  # Vorlage für DB-Konfiguration
│   └── database.php          # Echte Konfiguration (nicht in Git!)
│
└── .github/workflows/
    └── deploy.yml            # GitHub Actions Deployment
```

## Installation

1. Repository klonen
2. `config/database.example.php` nach `config/database.php` kopieren
3. Datenbank-Zugangsdaten in `config/database.php` eintragen
4. Webserver so konfigurieren, dass `public/` das Document Root ist

## Architektur

- **MVC-Pattern**: Controller verarbeiten Requests, Repositories greifen auf die DB zu, Views rendern HTML
- **Single Entry Point**: Alle Requests laufen über `public/index.php`
- **PSR-4 Autoloading**: Klassen werden automatisch geladen basierend auf Namespace

## Routen

| Methode | Pfad | Beschreibung |
|---------|------|--------------|
| GET | `/` | Startseite |
| GET/POST | `/login` | Login |
| GET/POST | `/register` | Registrierung |
| POST | `/logout` | Abmelden |
| GET | `/dashboard` | Benutzer-Dashboard |
| POST | `/dashboard/tour` | Tour hinzufügen |
| GET | `/team` | Team-Übersicht |
| GET/POST | `/team/join` | Team erstellen/beitreten |
| GET | `/leaderboard` | Rangliste |
