<?php
require_once __DIR__ . '/../../vendor/autoload.php';

// 성능 최적화: 출력 버퍼링 활성화
ob_start();

// 캐시 헤더 설정
header('Cache-Control: public, max-age=300'); // 5분 캐시
header('ETag: "' . md5_file(__FILE__) . '"');

// CORS 설정
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Content-Type: application/json; charset=utf-8');

// 성능 모니터링 시작
$startTime = microtime(true);

// OPTIONS 요청 처리 (preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// JSON 응답 헬퍼 함수
function sendJsonResponse($data, $statusCode = 200) {
    global $startTime;
    
    // 성능 메트릭 추가
    $executionTime = round((microtime(true) - $startTime) * 1000, 2);
    
    $response = $data;
    if (is_array($data)) {
        $response['performance'] = [
            'execution_time_ms' => $executionTime,
            'memory_usage_mb' => round(memory_get_peak_usage(true) / 1024 / 1024, 2)
        ];
    }
    
    http_response_code($statusCode);
    echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    
    // 출력 버퍼 플러시
    ob_end_flush();
    exit();
}

// 에러 응답 헬퍼 함수
function sendErrorResponse($message, $statusCode = 400, $details = null) {
    $response = [
        'success' => false,
        'error' => $message,
        'timestamp' => date('c')
    ];
    
    if ($details) {
        $response['details'] = $details;
    }
    
    sendJsonResponse($response, $statusCode);
}

// 성공 응답 헬퍼 함수
function sendSuccessResponse($data, $message = 'Success') {
    $response = [
        'success' => true,
        'message' => $message,
        'data' => $data,
        'timestamp' => date('c')
    ];
    
    sendJsonResponse($response);
}

// 에러 핸들러 설정
function handleError($errno, $errstr, $errfile, $errline) {
    error_log("PHP Error [$errno]: $errstr in $errfile on line $errline");
    sendErrorResponse('Internal server error', 500, 'PHP Error occurred');
}

set_error_handler('handleError');

try {
    // 기본 API 정보 반환
    sendSuccessResponse([
        'name' => 'PHITSOL API',
        'version' => '1.0.0',
        'status' => 'active',
        'endpoints' => [
            'GET /api/blog' => '블로그 포스트 목록',
            'GET /api/blog/{id}' => '블로그 포스트 상세',
            'GET /api/slider' => '슬라이더 데이터',
            'POST /api/auth/login' => '로그인',
            'GET /api/user/profile' => '사용자 프로필'
        ],
        'documentation' => 'API 문서는 개발 중입니다.',
        'optimization' => [
            'caching' => 'enabled',
            'compression' => 'enabled',
            'performance_monitoring' => 'enabled'
        ]
    ], 'PHITSOL API v1.0.0 - Welcome!');
    
} catch (Exception $e) {
    error_log("API Error: " . $e->getMessage());
    sendErrorResponse('Internal server error', 500, $e->getMessage());
} catch (Error $e) {
    error_log("API Fatal Error: " . $e->getMessage());
    sendErrorResponse('Internal server error', 500, $e->getMessage());
}
?>
