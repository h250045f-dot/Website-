<?php
// process.php - handles the Join/Contact form
require __DIR__ . '/config.php';

function clean($v) { return htmlspecialchars(trim($v ?? ''), ENT_QUOTES, 'UTF-8'); }

$errors = [];
$data = [];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: join.html');
    exit;
}

// ---- Collect & sanitise ----
$data['purpose']   = ($_POST['purpose'] ?? 'join') === 'contact' ? 'contact' : 'join';
$data['fullname']  = clean($_POST['fullname'] ?? '');
$data['regnumber'] = clean($_POST['regnumber'] ?? '');
$data['program']   = clean($_POST['program'] ?? '');
$data['email']     = trim($_POST['email'] ?? '');
$data['phone']     = clean($_POST['phone'] ?? '');
$data['message']   = clean($_POST['message'] ?? '');
$games = isset($_POST['games']) && is_array($_POST['games']) ? array_map('clean', $_POST['games']) : [];
$data['games']     = implode(', ', $games);

// ---- Server-side validation (never trust the browser) ----
if (strlen($data['fullname']) < 3)                          $errors[] = 'Please enter your full name.';
if (!preg_match('/^[A-Za-z0-9\/-]{5,20}$/', $data['regnumber'])) $errors[] = 'Invalid registration number.';
if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL))     $errors[] = 'Invalid email address.';
if ($data['program'] === '')                                $errors[] = 'Please select your programme.';
if (empty($_POST['consent']))                               $errors[] = 'You must agree to the club rules.';
$data['email'] = clean($data['email']);

$saved = false;
$storedIn = '';

if (empty($errors)) {
    // ---- 1) Try the database ----
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS,
                       [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        $stmt = $pdo->prepare("INSERT INTO submissions
            (purpose, fullname, regnumber, program, email, phone, games, message)
            VALUES (:purpose, :fullname, :regnumber, :program, :email, :phone, :games, :message)");
        $stmt->execute($data);
        $saved = true;
        $storedIn = 'database';
    } catch (Exception $e) {
        // ---- 2) Fallback: CSV file in /data (works on hosts without MySQL set up) ----
        $file = __DIR__ . '/data/submissions.csv';
        $fh = @fopen($file, 'a');
        if ($fh) {
            fputcsv($fh, [date('Y-m-d H:i:s'), $data['purpose'], $data['fullname'], $data['regnumber'],
                          $data['program'], $data['email'], $data['phone'], $data['games'], $data['message']]);
            fclose($fh);
            $saved = true;
            $storedIn = 'file';
        } else {
            $errors[] = 'Sorry, we could not save your details right now. Please try again later.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Submission Result | HIT Gaming Club</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header>
  <nav class="container">
    <a class="logo" href="index.html">HIT<span>GAMING</span></a>
    <ul class="nav-links">
      <li><a href="index.html">Home</a></li>
      <li><a href="about.html">About Us</a></li>
      <li><a href="events.html">Events</a></li>
      <li><a href="gallery.html">Gallery</a></li>
      <li><a href="join.html" class="active">Join / Contact</a></li>
    </ul>
  </nav>
</header>
<main>
  <section>
    <div class="container">
      <div class="card form-card">
<?php if ($saved): ?>
        <div class="notice ok">
          <h2>&#127918; Thank you, <?php echo $data['fullname']; ?>!</h2>
          <p><?php echo $data['purpose'] === 'join'
              ? 'Your membership request has been received. A committee member will contact you soon.'
              : 'Your message has been received. We will reply as soon as possible.'; ?></p>
        </div>
        <h3>Your details</h3>
        <ul class="about-list">
          <li><strong>Reg. number:</strong> <?php echo $data['regnumber']; ?></li>
          <li><strong>Programme:</strong> <?php echo $data['program']; ?></li>
          <li><strong>Email:</strong> <?php echo $data['email']; ?></li>
          <li><strong>Games:</strong> <?php echo $data['games'] !== '' ? $data['games'] : 'Not specified'; ?></li>
        </ul>
        <p style="margin-top:1.2rem"><a class="btn" href="index.html">Back to Home</a></p>
<?php else: ?>
        <div class="notice bad">
          <h2>Please fix the following</h2>
          <ul class="about-list">
            <?php foreach ($errors as $e) echo '<li>' . htmlspecialchars($e) . '</li>'; ?>
          </ul>
        </div>
        <p><a class="btn" href="javascript:history.back()">Go back</a></p>
<?php endif; ?>
      </div>
    </div>
  </section>
</main>
<footer><div class="container"><p>&copy; <?php echo date('Y'); ?> HIT Gaming Club</p></div></footer>
</body>
</html>
