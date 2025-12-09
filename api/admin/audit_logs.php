<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';
require_method('GET');
require_admin();

$pdo = db();
$stmt = $pdo->query("SELECT al.*, u.email AS admin_email FROM audit_logs al LEFT JOIN users u ON al.admin_user_id=u.id ORDER BY al.created_at DESC LIMIT 200");
$logs = $stmt->fetchAll();

json_response(['data' => $logs]);

