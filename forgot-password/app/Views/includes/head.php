<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle ?? $title ?? "Par'Axis") ?></title>

  <!-- Google Web Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@400;600;700;800&display=swap" rel="stylesheet">

  <!-- Template Stylesheet -->
  <link rel="stylesheet" href="app/Views/assets/css/front.css?v=<?= filemtime(__DIR__ . '/../assets/css/front.css') ?>">
  <!-- Users module styles -->
  <link rel="stylesheet" href="app/Views/assets/css/users.css?v=<?= filemtime(__DIR__ . '/../assets/css/users.css') ?>">
</head>
<body>
