<?php
/**
 * Post-Deploy Trigger Script
 * Access via: https://yourdomain.com/deploy.php?token=YOUR_DEPLOY_TOKEN
 * 
 * IMPORTANT: Delete this file after first successful deploy!
 */

// ─── Security ─────────────────────────────────────────────────────────────────
$token = $_GET['token'] ?? '';
$validToken = getenv('DEPLOY_TOKEN') ?: 'change-this-secret-token';

if (!hash_equals($validToken, $token)) {
    http_response_code(403);
    die('Forbidden');
}

// ─── Setup ────────────────────────────────────────────────────────────────────
$rootPath = dirname(__DIR__);
$output = [];
$errors = [];

function run(string $cmd, string $cwd): array {
    $descriptors = [
        0 => ['pipe', 'r'],
        1 => ['pipe', 'w'],
        2 => ['pipe', 'w'],
    ];
    $process = proc_open($cmd, $descriptors, $pipes, $cwd);
    $stdout = stream_get_contents($pipes[1]);
    $stderr = stream_get_contents($pipes[2]);
    foreach ($pipes as $pipe) fclose($pipe);
    $code = proc_close($process);
    return ['out' => $stdout, 'err' => $stderr, 'code' => $code];
}

// ─── Steps ────────────────────────────────────────────────────────────────────
$steps = [
    'Composer Install'   => "php -d allow_url_fopen=On $(which composer || echo composer) install --no-interaction --prefer-dist --optimize-autoloader --no-dev 2>&1",
    'Config Cache'       => 'php artisan config:cache',
    'Route Cache'        => 'php artisan route:cache',
    'View Cache'         => 'php artisan view:cache',
    'Run Migrations'     => 'php artisan migrate --force',
    'Storage Link'       => 'php artisan storage:link',
    'Clear All Cache'    => 'php artisan optimize:clear',
];

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Deploy Log</title>
<style>
  body { font-family: monospace; background: #111; color: #eee; padding: 20px; }
  h1 { color: #4ade80; }
  .step { margin: 16px 0; }
  .step-title { font-weight: bold; color: #60a5fa; margin-bottom: 4px; }
  .ok { color: #4ade80; }
  .fail { color: #f87171; }
  pre { background: #1e1e1e; padding: 10px; border-radius: 6px; white-space: pre-wrap; word-break: break-all; }
</style>
</head>
<body>
<h1>🚀 Post-Deploy Script</h1>
<p>Root: <?= htmlspecialchars($rootPath) ?></p>
<?php foreach ($steps as $label => $cmd): ?>
<?php $result = run($cmd, $rootPath); ?>
<div class="step">
  <div class="step-title"><?= htmlspecialchars($label) ?></div>
  <span class="<?= $result['code'] === 0 ? 'ok' : 'fail' ?>">
    <?= $result['code'] === 0 ? '✅ OK' : '❌ FAILED (exit ' . $result['code'] . ')' ?>
  </span>
  <pre><?= htmlspecialchars(trim($result['out'] . "\n" . $result['err'])) ?></pre>
</div>
<?php endforeach; ?>
<hr>
<p style="color:#888">Done. <strong>Please delete this file from the server for security!</strong></p>
</body>
</html>
