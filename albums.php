<?php
require 'db.php';

$pageTitle = 'Albums';

$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';
$genre = $_GET['genre'] ?? '';
$sort = $_GET['sort'] ?? 'title';

$allowedSorts = ['title', 'release_year', 'genre'];
if (!in_array($sort, $allowedSorts, true)) {
    $sort = 'title';
}

$genreStmt = $pdo->query("
    SELECT DISTINCT genre
    FROM albums
    WHERE genre IS NOT NULL AND genre <> ''
    ORDER BY genre
");
$genres = $genreStmt->fetchAll();

$where = [];
$params = [];

$hasFrom = filter_var($from, FILTER_VALIDATE_INT) !== false;
$hasTo = filter_var($to, FILTER_VALIDATE_INT) !== false;

if ($hasFrom && $hasTo) {
    if ((int)$from > (int)$to) {
        [$from, $to] = [$to, $from];
    }
    $where[] = "a.release_year BETWEEN ? AND ?";
    $params[] = (int)$from;
    $params[] = (int)$to;
} elseif ($hasFrom) {
    $where[] = "a.release_year >= ?";
    $params[] = (int)$from;
} elseif ($hasTo) {
    $where[] = "a.release_year <= ?";
    $params[] = (int)$to;
}

if ($genre !== '') {
    $validGenre = false;
    foreach ($genres as $row) {
        if ($row['genre'] === $genre) {
            $validGenre = true;
            break;
        }
    }
    if ($validGenre) {
        $where[] = "a.genre = ?";
        $params[] = $genre;
    } else {
        $genre = '';
    }
}

$sql = "
    SELECT a.album_id, a.title, a.release_year, a.genre, ar.name AS artist_name
    FROM albums a
    INNER JOIN artists ar ON ar.artist_id = a.artist_id
";
if ($where) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " ORDER BY a.$sort ASC LIMIT 50";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$albums = $stmt->fetchAll();

require 'partials/header.php';
?>

<h1>Albums</h1>

<form class="filters" method="get">
    <label>From year
        <input type="number" name="from" value="<?= htmlspecialchars($from) ?>">
    </label>
    <label>To year
        <input type="number" name="to" value="<?= htmlspecialchars($to) ?>">
    </label>
    <label>Genre
        <select name="genre">
            <option value="">All genres</option>
            <?php foreach ($genres as $row): ?>
                <option value="<?= htmlspecialchars($row['genre']) ?>" <?= $genre === $row['genre'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($row['genre']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>
    <label>Sort
        <select name="sort">
            <?php foreach ($allowedSorts as $option): ?>
                <option value="<?= htmlspecialchars($option) ?>" <?= $sort === $option ? 'selected' : '' ?>>
                    <?= htmlspecialchars(ucwords(str_replace('_', ' ', $option))) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>
    <button class="button" type="submit">Filter</button>
    <a class="button secondary" href="albums.php">Reset</a>
</form>

<table>
    <thead><tr><th>Title</th><th>Year</th><th>Genre</th><th>Artist</th></tr></thead>
    <tbody>
    <?php foreach ($albums as $album): ?>
        <tr>
            <td><?= htmlspecialchars($album['title']) ?></td>
            <td><?= $album['release_year'] === null ? 'Unknown' : (int)$album['release_year'] ?></td>
            <td><?= $album['genre'] === null ? 'Unknown' : htmlspecialchars($album['genre']) ?></td>
            <td><?= htmlspecialchars($album['artist_name']) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<?php if (!$albums): ?><p class="empty">No albums found.</p><?php endif; ?>

<?php require 'partials/footer.php'; ?>
