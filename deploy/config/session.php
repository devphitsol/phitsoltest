<?php
// 세션 설정
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // HTTPS가 아닌 경우 0으로 설정

// 세션 시작
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * 사용자 로그인 상태 확인
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) || isset($_SESSION['admin_logged_in']);
}

/**
 * 관리자 로그인 상태 확인
 */
function isAdminLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

/**
 * 현재 로그인한 사용자 ID 반환
 */
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? $_SESSION['admin_user_id'] ?? null;
}

/**
 * 현재 로그인한 사용자 정보 반환
 */
function getCurrentUser() {
    if (isset($_SESSION['user_id'])) {
        return [
            'id' => $_SESSION['user_id'],
            'email' => $_SESSION['email'] ?? '',
            'first_name' => $_SESSION['first_name'] ?? '',
            'last_name' => $_SESSION['last_name'] ?? '',
            'role' => $_SESSION['role'] ?? 'user'
        ];
    } elseif (isset($_SESSION['admin_user_id'])) {
        return [
            'id' => $_SESSION['admin_user_id'],
            'username' => $_SESSION['admin_username'] ?? '',
            'email' => $_SESSION['admin_email'] ?? '',
            'name' => $_SESSION['admin_full_name'] ?? '',
            'role' => $_SESSION['admin_role'] ?? 'admin'
        ];
    }
    
    return null;
}
?>
