<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// --------------------------------------------------------------------
// Web Routes (CodeIgniter Views)
// --------------------------------------------------------------------

// Authentication Routes
$routes->get('/', 'Auth::index');
$routes->post('auth/login', 'Auth::login');
$routes->get('logout', 'Auth::logout');

// Admin Routes
$routes->group('admin', ['filter' => 'admin_auth'], static function (RouteCollection $routes): void {
	$routes->get('dashboard', 'Admin::dashboard');
	$routes->get('devices', 'Admin::devices');
	$routes->get('device-mapping', 'Admin::deviceMapping');
	$routes->get('attendance-logs', 'Admin::attendanceLogs');
	$routes->get('attendance', 'Admin::attendance');
	$routes->get('rekap', 'Admin::rekap');
	$routes->get('shifts', 'Admin::shifts');
	$routes->get('students', 'Admin::students');
	$routes->get('students-import', 'Admin::studentsImport');
	$routes->get('teachers', 'Admin::teachers');
	$routes->get('classes', 'Admin::classes');
	$routes->get('habits', static function () {
		return redirect()->to(base_url('admin/habits-daily'));
	});
	$routes->get('habits-daily', 'Admin::habitsDaily');
	$routes->get('habits-monthly', 'Admin::habitsMonthly');
	$routes->get('users', 'Admin::users');
	$routes->get('reports', 'Admin::reports');
	$routes->get('calendar', 'Admin::calendar');
});

// Guru Piket Routes
$routes->group('guru-piket', ['filter' => 'gurupiket_auth'], static function (RouteCollection $routes): void {
	$routes->get('dashboard', 'GuruPiket::dashboard');
	$routes->get('monitoring', 'GuruPiket::monitoring');
	$routes->get('daily-recap', 'GuruPiket::dailyRecap');
	$routes->get('exceptions', 'GuruPiket::exceptions');
});

// Teacher/Guru Routes
$routes->group('teacher', ['filter' => 'auth'], static function (RouteCollection $routes): void {
	$routes->get('dashboard', 'Teacher::dashboard');
	$routes->get('attendance', 'Teacher::attendance');
	$routes->get('students', 'Teacher::students');
	$routes->get('rekap', 'Teacher::rekap');
	$routes->get('habits-daily', 'Teacher::habitsDaily');
	$routes->get('habits-monthly', 'Teacher::habitsMonthly');
});

// Teacher API Routes
$routes->group('api/teacher', ['filter' => 'auth'], static function (RouteCollection $routes): void {
	// Attendance API
	$routes->get('attendance', 'Teacher::apiGetAttendance');
	$routes->post('attendance', 'Teacher::apiSaveAttendance');

	// Rekap API
	$routes->get('rekap', 'Teacher::apiGetRekap');

	// Habits API
	$routes->get('habits', 'Teacher::apiGetHabits');
	$routes->get('habits/recap', 'Teacher::apiGetHabitRecap');
	$routes->get('habits/class-recap', 'Teacher::apiGetHabitClassRecap');
	$routes->get('habits/student', 'Teacher::apiGetStudentMonthlyHabits');

	// Classes API (only their own class)
	$routes->get('classes', 'Teacher::apiGetClasses');
	$routes->get('students', 'Teacher::apiGetStudents');
});

// Admin API Routes (for AJAX calls)
$routes->group('api/admin', ['filter' => 'admin_auth'], static function (RouteCollection $routes): void {
	// Users API
	$routes->get('users', 'Admin::apiGetUsers');
	$routes->get('users/(:num)', 'Admin::apiGetUser/$1');
	$routes->post('users', 'Admin::apiCreateUser');
	$routes->put('users/(:num)', 'Admin::apiUpdateUser/$1');
	$routes->delete('users/(:num)', 'Admin::apiDeleteUser/$1');
	$routes->post('users/(:num)/reset-password', 'Admin::apiResetPassword/$1');

	// Teachers API
	$routes->get('teachers', 'Admin::apiGetTeachers');
	$routes->get('teachers/(:num)', 'Admin::apiGetTeacher/$1');
	$routes->post('teachers', 'Admin::apiCreateTeacher');
	$routes->put('teachers/(:num)', 'Admin::apiUpdateTeacher/$1');
	$routes->delete('teachers/(:num)', 'Admin::apiDeleteTeacher/$1');

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

	// Attendance (Daftar Hadir) API
	$routes->get('attendance', 'Admin::apiGetAttendance');
	$routes->post('attendance', 'Admin::apiSaveAttendance');

	// Rekap API
	$routes->get('rekap', 'Admin::apiGetRekap');

	// Shifts API
	$routes->get('shifts', 'Admin::apiGetShifts');
	$routes->get('shifts/(:num)', 'Admin::apiGetShift/$1');
	$routes->post('shifts', 'Admin::apiCreateShift');
	$routes->put('shifts/(:num)', 'Admin::apiUpdateShift/$1');
	$routes->delete('shifts/(:num)', 'Admin::apiDeleteShift/$1');

	// Students & Classes API
	$routes->get('students', 'Admin::apiGetStudents');
	$routes->get('students/(:num)', 'Admin::apiGetStudent/$1');
	$routes->post('students/generate-accounts', 'Admin::apiGenerateStudentAccounts');
	$routes->get('students/upload-template', 'Admin::apiDownloadStudentUploadTemplate');
	$routes->post('students/import', 'Admin::apiImportStudents');
	$routes->post('students', 'Admin::apiCreateStudent');
	$routes->put('students/(:num)', 'Admin::apiUpdateStudent/$1');
	$routes->delete('students/(:num)', 'Admin::apiDeleteStudent/$1');

	$routes->get('classes', 'Admin::apiGetClasses');
	$routes->post('classes', 'Admin::apiCreateClass');
	$routes->put('classes/(:num)', 'Admin::apiUpdateClass/$1');
	$routes->delete('classes/(:num)', 'Admin::apiDeleteClass/$1');

	// Habits API (7 Kebiasaan Anak Indonesia)
	$routes->get('habits', 'Admin::apiGetHabits');
	$routes->get('habits/recap', 'Admin::apiGetHabitRecap');
	$routes->get('habits/student', 'Admin::apiGetStudentMonthlyHabits');
	$routes->post('habits', 'Admin::apiSaveHabit');
	$routes->post('habits/bulk', 'Admin::apiSaveHabitsBulk');

	// School Holidays API
	$routes->get('school-holidays', 'Admin::apiGetSchoolHolidays');
	$routes->post('school-holidays', 'Admin::apiSaveSchoolHoliday');

	// WhatsApp Test API
	$routes->post('test-whatsapp', 'Admin::apiTestWhatsapp');
});

// Student/Parent Routes
$routes->group('student', ['filter' => 'auth'], static function (RouteCollection $routes): void {
	$routes->get('dashboard', 'Student::dashboard');
	$routes->get('attendance', 'Student::attendance');
	$routes->get('habits', 'Student::habits');
	$routes->get('notifications', 'Student::notifications');
	$routes->get('profile', 'Student::profile');

	// Student API
	$routes->group('api', static function (RouteCollection $routes): void {
		$routes->get('habits/today', 'Student::apiGetTodayHabits');
		$routes->post('habits/toggle', 'Student::apiToggleHabit');
		$routes->get('habits/stats', 'Student::apiGetHabitsStats');
		$routes->get('habits/parent-summary', 'Student::apiGetParentWeeklySummary');
	});
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
