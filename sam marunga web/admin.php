<?php
// admin.php - committee view of submissions (retrieves stored data)
session_start();
require __DIR__ . '/config.php';
if (isset($_POST['password'])) {
    if ($_POST['password'] === ADMIN_PASSWORD) { $_SESSION['ok'] = true; } else { $err = 'Wrong password.'; }
}
if (isset($_GET['logout'])) { session_destroy(); header('Location: admin.php'); exit; }
?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin | HIT Gaming Club</title><link rel="stylesheet" href="css/style.css"></head>
<body><main><section><div class="container">
<h2>Committee <span>Admin</span></h2>
<?php if (empty($_SESSION['ok'])): ?>
  <form method="post" class="card form-card">
    <?php if (!empty($err)) echo '<div class="notice bad">' . $err . '</div>'; ?>
    <label for="password">Password</label>
    <input type="password" id="password" name="password">
    <button class="btn" style="margin-top:1rem" type="submit">Login</button>
  </form>
<?php else:
  $rows = [];
  try {
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4", DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    foreach ($pdo->query("SELECT submitted_at, purpose, fullname, regnumber, program, email, phone, games, message FROM submissions ORDER BY id DESC", PDO::FETCH_NUM) as $r) $rows[] = $r;
    $source = 'database';
  } catch (Exception $e) {
    $source = 'CSV file';
    $f = __DIR__ . '/data/submissions.csv';
    if (is_readable($f)) { $fh = fopen($f, 'r'); while (($r = fgetcsv($fh)) !== false) array_unshift($rows, $r); fclose($fh); }
  }
?>
  <p>Showing <?php echo count($rows); ?> submission(s) from the <?php echo $source; ?>. <a href="admin.php?logout=1">Logout</a></p>
  <div class="table-wrap"><table>
    <thead><tr><th>Date</th><th>Type</th><th>Name</th><th>Reg No.</th><th>Programme</th><th>Email</th><th>Phone</th><th>Games</th><th>Message</th></tr></thead>
    <tbody>
    <?php foreach ($rows as $r) { echo '<tr>'; foreach ($r as $c) echo '<td>' . htmlspecialchars((string)$c) . '</td>'; echo '</tr>'; } ?>
    </tbody>
  </table></div>
<?php endif; ?>
</div></section></main></body></html>
