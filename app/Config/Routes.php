<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// --------------------------------------------------------------------
// Web Routes (CodeIgniter Views)
// --------------------------------------------------------------------

// Authentication Routes
$routes->get('/debug-status', function () {
	header('Content-Type: application/json');
	return json_encode([
		'status' => 'ok',
		'environment' => ENVIRONMENT,
		'time' => date('Y-m-d H:i:s'),
	]);
});
$routes->get('/debug-dashboard', function () {
	// Simulate dashboard data
	$data = [
		'title' => 'Dashboard Admin',
		'pageTitle' => 'Dashboard Admin',
		'pageDescription' => 'Selamat datang di panel administrator',
		'user' => [
			'name' => 'Test User',
			'role' => 'Administrator'
		],
		'stats' => [
			'total_students' => 0,
			'present_today' => 0,
			'absent_today' => 0,
			'total_devices' => 0,
			'active_devices' => 0,
		],
		'unreadNotifications' => 0
	];
	return view('dashboard/admin', $data);
});
$routes->get('/', 'Auth::index');
$routes->post('auth/login', 'Auth::login');
$routes->get('logout', 'Auth::logout');
$routes->get('test-api', function () {
	return view('test_api');
});

// Admin Routes
$routes->group('admin', ['filter' => 'auth'], static function (RouteCollection $routes): void {
	$routes->get('dashboard', 'Admin::dashboard');
	$routes->get('devices', 'Admin::devices');
	$routes->get('device-mapping', 'Admin::deviceMapping');
	$routes->get('attendance-logs', 'Admin::attendanceLogs');
	$routes->get('shifts', 'Admin::shifts');
	$routes->get('students', 'Admin::students');
	$routes->get('classes', 'Admin::classes');
	$routes->get('users', 'Admin::users');
	$routes->get('reports', 'Admin::reports');
});

// Admin API Routes (for AJAX calls)
$routes->group('api/admin', ['filter' => 'auth'], static function (RouteCollection $routes): void {
	// Devices API
	$routes->get('devices', 'Admin::apiGetDevices');
	$routes->get('devices/(:num)', 'Admin::apiGetDevice/$1');
	$routes->post('devices', 'Admin::apiCreateDevice');
	$routes->put('devices/(:num)', 'Admin::apiUpdateDevice/$1');
	$routes->delete('devices/(:num)', 'Admin::apiDeleteDevice/$1');
	$routes->get('devices/(:num)/test', 'Admin::apiTestDevice/$1');

	// Device Mappings API
	$routes->get('device-mappings', 'Admin::apiGetDeviceMappings');
	$routes->post('device-mappings', 'Admin::apiCreateDeviceMapping');
	$routes->delete('device-mappings/(:num)', 'Admin::apiDeleteDeviceMapping/$1');

	// Attendance Logs API
	$routes->get('attendance-logs', 'Admin::apiGetAttendanceLogs');

	// Students & Classes API
	$routes->get('students', 'Admin::apiGetStudents');
	$routes->get('classes', 'Admin::apiGetClasses');
});

// Student/Parent Routes
$routes->group('student', ['filter' => 'auth'], static function (RouteCollection $routes): void {
	$routes->get('dashboard', 'Student::dashboard');
	$routes->get('attendance', 'Student::attendance');
	$routes->get('notifications', 'Student::notifications');
	$routes->get('profile', 'Student::profile');
});

// --------------------------------------------------------------------
// API Routes (Legacy - Keep for backward compatibility)
// --------------------------------------------------------------------
$routes->group('api', ['namespace' => 'App\\Controllers\\Api'], static function (RouteCollection $routes): void {
	// CORS preflight for browser requests (Vite/React).
	$routes->options('(:any)', static function () {
		return service('response')->setStatusCode(204);
	});

	$routes->post('auth/login', 'AuthController::login');

	$routes->group('', ['filter' => 'tokens'], static function (RouteCollection $routes): void {
		$routes->get('me', 'AuthController::me');
		$routes->post('auth/logout', 'AuthController::logout');

		$routes->get('classes', 'ClassesController::index');
		$routes->post('classes', 'ClassesController::create');
		$routes->get('classes/(:num)', 'ClassesController::show/$1');
		$routes->put('classes/(:num)', 'ClassesController::update/$1');
		$routes->delete('classes/(:num)', 'ClassesController::delete/$1');

		$routes->get('students', 'StudentsController::index');
		$routes->post('students', 'StudentsController::create');
		$routes->get('students/(:num)', 'StudentsController::show/$1');
		$routes->put('students/(:num)', 'StudentsController::update/$1');
		$routes->delete('students/(:num)', 'StudentsController::delete/$1');

		$routes->get('attendance', 'AttendanceController::index');
		$routes->get('attendance/summary', 'AttendanceController::summary');

		$routes->get('devices', 'DevicesController::index');
		$routes->post('devices/link-student', 'DevicesController::linkStudent');

		// Admin Routes - Device Management
		$routes->group('admin', static function (RouteCollection $routes): void {
			$routes->get('devices', 'AdminController::getDevices');
			$routes->post('devices', 'AdminController::createDevice');
			$routes->put('devices/(:num)', 'AdminController::updateDevice/$1');
			$routes->delete('devices/(:num)', 'AdminController::deleteDevice/$1');
			$routes->post('devices/(:num)/test-connection', 'AdminController::testDeviceConnection/$1');

			$routes->get('device-user-maps', 'AdminController::getDeviceUserMaps');
			$routes->get('device-user-maps/device/(:num)', 'AdminController::getDeviceUserMaps/$1');
			$routes->post('device-user-maps', 'AdminController::createDeviceUserMap');
			$routes->delete('device-user-maps/(:num)', 'AdminController::deleteDeviceUserMap/$1');

			$routes->get('shifts', 'AdminController::getShifts');
			$routes->post('shifts', 'AdminController::createShift');
			$routes->put('shifts/(:num)', 'AdminController::updateShift/$1');
			$routes->delete('shifts/(:num)', 'AdminController::deleteShift/$1');
		});

		// Guru Piket Routes
		$routes->group('guru-piket', static function (RouteCollection $routes): void {
			$routes->get('daily-summary', 'GuruPiketController::getDailySummary');
			$routes->get('not-checked-in', 'GuruPiketController::getNotCheckedIn');
			$routes->get('recent-logs', 'GuruPiketController::getRecentLogs');
			$routes->post('exceptions', 'GuruPiketController::recordException');
			$routes->get('exceptions', 'GuruPiketController::getExceptions');
			$routes->delete('exceptions/(:num)', 'GuruPiketController::deleteException/$1');
		});

		// Student/Parent Routes
		$routes->group('student', static function (RouteCollection $routes): void {
			$routes->get('profile', 'StudentController::getProfile');
			$routes->get('attendance/logs', 'StudentController::getAttendanceLogs');
			$routes->get('attendance/summary', 'StudentController::getAttendanceSummary');
			$routes->get('attendance/today', 'StudentController::getTodayAttendance');
			$routes->get('notifications', 'StudentController::getNotifications');
			$routes->put('notifications/(:num)/read', 'StudentController::markNotificationAsRead/$1');
			$routes->post('notifications/read-all', 'StudentController::markAllNotificationsAsRead');
		});
	});
});

// --------------------------------------------------------------------
// Fingerprint ADMS / iClock endpoints (ZKTeco/Solution style)
// Device will call these endpoints directly (no auth).
// --------------------------------------------------------------------
$routes->group('iclock', static function (RouteCollection $routes): void {
	$routes->match(['get', 'post'], 'cdata', 'IclockController::cdata');
	$routes->match(['get', 'post'], 'registry', 'IclockController::registry');
	$routes->match(['get', 'post'], 'getrequest', 'IclockController::getrequest');
	$routes->match(['get', 'post'], 'devicecmd', 'IclockController::devicecmd');
});
