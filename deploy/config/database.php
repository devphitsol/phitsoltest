<?php
// 데이터베이스 설정
// 이 파일은 app/Config/Database.php에서 실제 데이터베이스 연결을 처리합니다.
// 여기서는 기본 설정만 제공합니다.

// MongoDB 기본 설정
define('MONGODB_URI', $_ENV['MONGODB_URI'] ?? 'mongodb://localhost:27017');
define('MONGODB_DATABASE', $_ENV['MONGODB_DATABASE'] ?? 'phitsol_dashboard');

// 애플리케이션 설정
define('APP_ENV', $_ENV['APP_ENV'] ?? 'development');
define('APP_DEBUG', $_ENV['APP_DEBUG'] ?? true);

// 세션 설정
define('SESSION_SECRET', $_ENV['SESSION_SECRET'] ?? 'your-secret-key-here');
?>
