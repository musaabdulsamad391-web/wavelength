# Wavelength

A simple PHP music library management system built with PDO and MySQL WITH BASIC HTML AND CSS.

## Project Overview

This project lets users:
- view artists, albums, and playlists
- browse music statistics on the dashboard
- add new tracks
- edit artist details
- delete tracks safely

It is a lightweight database-driven web app with no framework and no Composer setup.

## Tech Stack

- PHP 8
- MySQL
- PDO
- HTML/CSS
- Apache / Laragon

## Requirements

- PHP 8.x
- MySQL 8.x
- Apache, Laragon, or XAMPP
- The supplied `music_streaming.sql` database file

## Project Structure

```text
wavelength/
├── db.php
├── index.php
├── artists.php
├── artist.php
├── albums.php
├── playlists.php
├── playlist.php
├── add_track.php
├── edit_artist.php
├── delete_track.php
├── partials/
│   ├── header.php
│   └── footer.php
├── css/
│   └── style.css
├── sql/
│   └── music_streaming.sql
└── README.md
```

## Setup

1. Import the database from `sql/music_streaming.sql` into MySQL.
2. Place the project in your web folder, for example:
   `C:\laragon\www\wavelength`
3. Open `db.php` and confirm your database settings:
   - host: `localhost`
   - database: `music_streaming`
   - username: `root`
   - password: update if your MySQL root password is set
4. Start Apache and MySQL.
5. Open this in the browser:
   `http://localhost/wavelength/`

## Database Relationship

```text
artists
  |
  +----< albums
           |
           +----< tracks
                    |
                    +----< playlist_tracks >---- playlists ----> users
```

## Main Features

- Dashboard with total artists, albums, tracks, streams, and average duration
- Artist search and filtering
- Album browsing with sorting and filters
- Playlist management
- Safe CRUD operations using prepared statements
- Input validation and basic security checks

## Technical Notes

- Uses `PDO` instead of `mysqli`
- Everyday database operations are protected with prepared statements
- User input is sanitized before display with `htmlspecialchars()`
- URL parameters are validated as integers
- Redirects are used for safe navigation after deletion and updates

## Limitations

- No user authentication system
- No dedicated track browsing page
- Simple UI focused on functionality rather than advanced design

## Conclusion

Wavelength is a clean PHP-based music library manager that demonstrates database connectivity, SQL querying, filtering, joins, and CRUD operations in a simple web application.
