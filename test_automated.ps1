# Automated Testing Script
# Test Edit User & Reset Password

$baseUrl = "http://localhost:8080"
$session = $null

Write-Host "`n=============================================="
Write-Host "  TESTING APLIKASI ABSENSI SISWA"
Write-Host "==============================================`n"

# Test 1: Check Server
Write-Host "📝 TEST 1: Check Server Status" -ForegroundColor Cyan
Write-Host "----------------------------------------------"
try {
    $response = Invoke-WebRequest -Uri "$baseUrl" -Method GET -UseBasicParsing
    Write-Host "✅ PASS | Server Online | HTTP $($response.StatusCode)" -ForegroundColor Green
} catch {
    Write-Host "❌ FAIL | Server Offline | $_" -ForegroundColor Red
    exit 1
}

Write-Host ""

# Test 2: Login Admin
Write-Host "📝 TEST 2: Login Admin" -ForegroundColor Cyan
Write-Host "----------------------------------------------"
try {
    $loginBody = @{
        username = "admin"
        password = "admin123"
    } | ConvertTo-Json
    
    $loginResponse = Invoke-WebRequest -Uri "$baseUrl/api/auth/login" `
        -Method POST `
        -Body $loginBody `
        -ContentType "application/json" `
        -SessionVariable session `
        -UseBasicParsing
    
    $loginData = $loginResponse.Content | ConvertFrom-Json
    
    if ($loginData.status -eq "success") {
        Write-Host "✅ PASS | Login Berhasil | User: $($loginData.data.username)" -ForegroundColor Green
    } else {
        Write-Host "❌ FAIL | Login Gagal | $($loginData.message)" -ForegroundColor Red
        exit 1
    }
} catch {
    Write-Host "❌ FAIL | Login Error | $_" -ForegroundColor Red
    Write-Host "ℹ️  Coba login manual di browser untuk verifikasi username/password" -ForegroundColor Yellow
    exit 1
}

Write-Host ""

# Test 3: Get Users List
Write-Host "📝 TEST 3: Get Users List" -ForegroundColor Cyan
Write-Host "----------------------------------------------"
try {
    $usersResponse = Invoke-WebRequest -Uri "$baseUrl/api/admin/users" `
        -Method GET `
        -WebSession $session `
        -UseBasicParsing
    
    $usersData = $usersResponse.Content | ConvertFrom-Json
    
    if ($usersData.status -eq "success") {
        $userCount = $usersData.data.Count
        Write-Host "✅ PASS | Get Users | Total: $userCount users" -ForegroundColor Green
        
        # Ambil user pertama untuk test edit
        $testUser = $usersData.data[0]
        $testUserId = $testUser.id
        $testUserEmail = $testUser.email
        $testUserName = $testUser.full_name
        
        Write-Host "ℹ️  Test User: ID=$testUserId, Name=$testUserName, Email=$testUserEmail" -ForegroundColor Yellow
    } else {
        Write-Host "❌ FAIL | Get Users | $($usersData.message)" -ForegroundColor Red
        exit 1
    }
} catch {
    Write-Host "❌ FAIL | Get Users Error | $_" -ForegroundColor Red
    exit 1
}

Write-Host ""

# Test 4: Edit User (Same Email - Test Duplicate Fix)
Write-Host "📝 TEST 4: Edit User (Same Email - Test Duplicate Fix)" -ForegroundColor Cyan
Write-Host "----------------------------------------------"
try {
    $updateBody = @{
        name = "$testUserName - Updated"
        username = $testUser.username
        email = $testUserEmail
        role = $testUser.role
        is_active = 1
    } | ConvertTo-Json
    
    Write-Host "ℹ️  Updating user $testUserId with SAME email: $testUserEmail" -ForegroundColor Yellow
    
    $updateResponse = Invoke-WebRequest -Uri "$baseUrl/api/admin/users/$testUserId" `
        -Method PUT `
        -Body $updateBody `
        -ContentType "application/json" `
        -WebSession $session `
        -UseBasicParsing
    
    $updateData = $updateResponse.Content | ConvertFrom-Json
    
    if ($updateData.status -eq "success") {
        Write-Host "✅ PASS | Edit User (Same Email) | NO DUPLICATE ERROR!" -ForegroundColor Green
        Write-Host "ℹ️  User updated successfully without duplicate email error" -ForegroundColor Yellow
    } else {
        Write-Host "❌ FAIL | Edit User | $($updateData.message)" -ForegroundColor Red
    }
} catch {
    $errorResponse = $_.ErrorDetails.Message | ConvertFrom-Json
    Write-Host "❌ FAIL | Edit User Error | $($errorResponse.message)" -ForegroundColor Red
    Write-Host "ℹ️  Error Detail: $_" -ForegroundColor Yellow
}

Write-Host ""

# Test 5: Reset Password
Write-Host "📝 TEST 5: Reset Password" -ForegroundColor Cyan
Write-Host "----------------------------------------------"
try {
    $resetBody = @{
        new_password = "testpass123"
    } | ConvertTo-Json
    
    $resetResponse = Invoke-WebRequest -Uri "$baseUrl/api/admin/users/$testUserId/reset-password" `
        -Method POST `
        -Body $resetBody `
        -ContentType "application/json" `
        -WebSession $session `
        -UseBasicParsing
    
    $resetData = $resetResponse.Content | ConvertFrom-Json
    
    if ($resetData.status -eq "success") {
        Write-Host "✅ PASS | Reset Password | Password berhasil direset" -ForegroundColor Green
        Write-Host "ℹ️  Username: $($resetData.data.username)" -ForegroundColor Yellow
        Write-Host "ℹ️  New Password: $($resetData.data.new_password)" -ForegroundColor Yellow
    } else {
        Write-Host "❌ FAIL | Reset Password | $($resetData.message)" -ForegroundColor Red
    }
} catch {
    Write-Host "❌ FAIL | Reset Password Error | $_" -ForegroundColor Red
}

Write-Host ""

# Test 6: Get Devices
Write-Host "📝 TEST 6: Get Devices (Device Connection)" -ForegroundColor Cyan
Write-Host "----------------------------------------------"
try {
    $devicesResponse = Invoke-WebRequest -Uri "$baseUrl/api/admin/devices" `
        -Method GET `
        -WebSession $session `
        -UseBasicParsing
    
    $devicesData = $devicesResponse.Content | ConvertFrom-Json
    
    if ($devicesData.status -eq "success") {
        $deviceCount = $devicesData.data.Count
        Write-Host "✅ PASS | Get Devices | Total: $deviceCount devices" -ForegroundColor Green
        
        if ($deviceCount -eq 0) {
            Write-Host "⚠️  WARNING | Tidak ada device terdaftar" -ForegroundColor Yellow
            Write-Host "ℹ️  Tambahkan device di /admin/devices untuk test koneksi" -ForegroundColor Yellow
        } else {
            foreach ($device in $devicesData.data) {
                Write-Host "ℹ️  Device: $($device.name) | IP: $($device.ip_address) | Port: $($device.port)" -ForegroundColor Yellow
            }
        }
    } else {
        Write-Host "❌ FAIL | Get Devices | $($devicesData.message)" -ForegroundColor Red
    }
} catch {
    Write-Host "❌ FAIL | Get Devices Error | $_" -ForegroundColor Red
}

Write-Host ""

# Test 7: Get Attendance
Write-Host "📝 TEST 7: Get Attendance (Attendance Recording)" -ForegroundColor Cyan
Write-Host "----------------------------------------------"
try {
    $attendanceResponse = Invoke-WebRequest -Uri "$baseUrl/api/admin/attendance" `
        -Method GET `
        -WebSession $session `
        -UseBasicParsing
    
    $attendanceData = $attendanceResponse.Content | ConvertFrom-Json
    
    if ($attendanceData.status -eq "success") {
        $attendanceCount = if ($attendanceData.data) { $attendanceData.data.Count } else { 0 }
        Write-Host "✅ PASS | Get Attendance | Total: $attendanceCount records" -ForegroundColor Green
        
        if ($attendanceCount -eq 0) {
            Write-Host "⚠️  WARNING | Tidak ada data absensi" -ForegroundColor Yellow
            Write-Host "ℹ️  Test dengan tap fingerprint di device atau manual input" -ForegroundColor Yellow
        } else {
            Write-Host "ℹ️  Ada data absensi tersedia" -ForegroundColor Yellow
        }
    } else {
        Write-Host "❌ FAIL | Get Attendance | $($attendanceData.message)" -ForegroundColor Red
    }
} catch {
    Write-Host "❌ FAIL | Get Attendance Error | $_" -ForegroundColor Red
}

Write-Host ""

# Test 8: Get Habits
Write-Host "📝 TEST 8: Get Habits (Habits Tracking)" -ForegroundColor Cyan
Write-Host "----------------------------------------------"
try {
    $habitsResponse = Invoke-WebRequest -Uri "$baseUrl/api/admin/habits" `
        -Method GET `
        -WebSession $session `
        -UseBasicParsing
    
    $habitsData = $habitsResponse.Content | ConvertFrom-Json
    
    if ($habitsData.status -eq "success") {
        $habitsCount = if ($habitsData.data) { $habitsData.data.Count } else { 0 }
        Write-Host "✅ PASS | Get Habits | Total: $habitsCount habit categories" -ForegroundColor Green
        
        if ($habitsCount -eq 0) {
            Write-Host "⚠️  WARNING | Tidak ada kategori kebiasaan" -ForegroundColor Yellow
            Write-Host "ℹ️  Tambahkan kategori kebiasaan di /admin/habits" -ForegroundColor Yellow
        } else {
            Write-Host "ℹ️  Ada kategori kebiasaan tersedia" -ForegroundColor Yellow
        }
    } else {
        Write-Host "❌ FAIL | Get Habits | $($habitsData.message)" -ForegroundColor Red
    }
} catch {
    Write-Host "❌ FAIL | Get Habits Error | $_" -ForegroundColor Red
}

Write-Host ""
Write-Host "=============================================="
Write-Host "  TESTING SELESAI"
Write-Host "==============================================`n"
