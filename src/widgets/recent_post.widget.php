<?php

$stmt = $pdo->query("SELECT * FROM delivery_articles WHERE event_at <= NOW() ORDER BY event_at DESC LIMIT 0,4");
$articles = $stmt->fetchAll();
