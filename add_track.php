<?php
require 'db.php';

$pageTitle = 'Add Track';
$errors = [];
$success = null;
$title = '';
$duration = '';
$streams = '0';
$albumId = '';

try {
    $albumStmt = $pdo->query("
        SELECT album_id, title, release_year
        FROM albums
        ORDER BY title ASC
    ");
    $albums = $albumStmt->fetchAll();
} catch (PDOException $e) {
    $albums = [];
    $errors[] = "Unable to load albums.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $duration = trim($_POST['duration_seconds'] ?? '');
    $streams = trim($_POST['stream_count'] ?? '0');
    $albumId = trim($_POST['album_id'] ?? '');

    if ($title === '') {
        $errors[] = "Track title is required.";
    }

    $durationInt = filter_var($duration, FILTER_VALIDATE_INT);
    if ($durationInt === false || $durationInt <= 0) {
        $errors[] = "Duration must be a positive integer.";
    }

    $streamInt = filter_var($streams, FILTER_VALIDATE_INT);
    if ($streamInt === false || $streamInt < 0) {
        $errors[] = "Stream count must be zero or a positive integer.";
    }

    $albumInt = filter_var($albumId, FILTER_VALIDATE_INT);
    if ($albumInt === false || $albumInt <= 0) {
        $errors[] = "Please select a valid album.";
    }

    if (!$errors) {
        try {
            $check = $pdo->prepare("SELECT album_id, title FROM albums WHERE album_id = ?");
            $check->execute([$albumInt]);
            $album = $check->fetch();

            if (!$album) {
                $errors[] = "The selected album does not exist.";
            } else {
                $insert = $pdo->prepare("
                    INSERT INTO tracks (album_id, title, duration_seconds, stream_count)
                    VALUES (?, ?, ?, ?)
                ");
                $insert->execute([$albumInt, $title, $durationInt, $streamInt]);
                $newId = (int)$pdo->lastInsertId();

                $success = "Track added successfully. New track ID: " . $newId;
                $successAlbumId = $albumInt;
                $title = '';
                $duration = '';
                $streams = '0';
                $albumId = '';
            }
        } catch (PDOException $e) {
            $errors[] = "The track could not be added. Please check the entered information.";
        }
    }
}

require 'partials/header.php';
?>

<h1>Add New Track</h1>

<?php if ($errors): ?>
    <div class="alert error">
        <?php foreach ($errors as $error): ?>
            <p><?= htmlspecialchars($error) ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="alert success">
        <?= htmlspecialchars($success) ?>
        <?php if (isset($successAlbumId)): ?>
            <a href="album.php?id=<?= (int)$successAlbumId ?>">View album</a>
        <?php endif; ?>
    </div>
<?php endif; ?>

<form class="form-card" method="post">
    <label>Track title
        <input type="text" name="title" maxlength="150" required value="<?= htmlspecialchars($title) ?>">
    </label>

    <label>Duration (seconds)
        <input type="number" name="duration_seconds" min="1" required value="<?= htmlspecialchars($duration) ?>">
    </label>

    <label>Stream count
        <input type="number" name="stream_count" min="0" value="<?= htmlspecialchars($streams) ?>">
    </label>

    <label>Album
        <select name="album_id" required>
            <option value="">Select an album</option>
            <?php foreach ($albums as $album): ?>
                <option value="<?= (int)$album['album_id'] ?>" <?= (string)$albumId === (string)$album['album_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($album['title']) ?> (<?= (int)$album['release_year'] ?>)
                </option>
            <?php endforeach; ?>
        </select>
    </label>

    <button class="button" type="submit">Add Track</button>
</form>

<?php require 'partials/footer.php'; ?>
