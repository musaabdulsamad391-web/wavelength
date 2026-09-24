<?php
$pageTitle = $pageTitle ?? 'Wavelength';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - Wavelength</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header class="site-header">
    <div class="container nav">
        <a class="logo" href="index.php">Wavelength</a>
        <nav>
            <a href="index.php">Dashboard</a>
            <a href="artists.php">Artists</a>
            <a href="albums.php">Albums</a>
            <a href="playlists.php">Playlists</a>
            <a href="add_track.php">Add Track</a>
        </nav>
    </div>
</header>
<main class="container">
