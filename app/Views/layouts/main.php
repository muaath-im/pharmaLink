<?php
/**
 * app/Views/layouts/main.php
 * Master HTML5 Layout Template with Design Tokens & Modern Typography.
 * Phase 06: Project Structure Refactoring
 */

use App\Core\View;

$pageTitle = $pageTitle ?? 'PharmaLink | Clinical Pharmacy Portal';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= View::e($pageTitle) ?></title>
  <link rel="icon" type="image/png" href="/assets/images/logo.png">
  
  <!-- Design Tokens (Layer 1 Ramps, Layer 2 Semantics, Layer 3 Components) -->
  <link rel="stylesheet" href="/assets/css/tokens.css">
  
  <!-- Google Fonts: Inter & Plus Jakarta Sans -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">

  <style>
    *, *::before, *::after {
      box-sizing: border-box;
    }
    body {
      margin: 0;
      font-family: var(--font-family-body, 'Inter', sans-serif);
      background-color: var(--color-bg-canvas, #f8fafc);
      color: var(--color-text-main, #0f172a);
      line-height: 1.6;
      -webkit-font-smoothing: antialiased;
    }
    h1, h2, h3, h4, h5, h6 {
      font-family: var(--font-family-heading, 'Plus Jakarta Sans', sans-serif);
      color: var(--color-text-main, #0f172a);
      margin-top: 0;
    }
    .main-container {
      max-width: 1200px;
      margin: 2rem auto;
      padding: 0 1.5rem;
      min-height: 70vh;
    }
  </style>
</head>
<body>

  <!-- Navigation Bar -->
  <?php include dirname(__DIR__) . '/partials/navbar.php'; ?>

  <!-- Main Content Slot -->
  <main class="main-container">
    <?= $slot ?? '' ?>
  </main>

  <!-- Global Footer -->
  <?php include dirname(__DIR__) . '/partials/footer.php'; ?>

</body>
</html>
