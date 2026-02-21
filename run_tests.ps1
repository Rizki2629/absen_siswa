# Comprehensive Testing Script - Absensi Siswa
param()

$baseUrl = "http://localhost:8080"
$passed = 0
$failed = 0
$results = @()

function Pass {
    param($label, $info)
    $script:passed++
    if ($info) { Write-Host "PASS | $label | $info" }
    else { Write-Host "PASS | $label" }
    $script:results += [PSCustomObject]@{ Status = "PASS"; Test = $label; Info = "$info" }
}
function Fail {
    param($label, $info)
    $script:failed++
    if ($info) { Write-Host "FAIL | $label | $info" }
    else { Write-Host "FAIL | $label" }
    $script:results += [PSCustomObject]@{ Status = "FAIL"; Test = $label; Info = "$info" }
}
function Section { param($title); Write-Host "`n=== $title ===" }

# TEST 1 - Server Status
Section "TEST 1 - SERVER STATUS"
try {
    $r = Invoke-WebRequest -Uri "$baseUrl" -UseBasicParsing -TimeoutSec 10
    if ($r.StatusCode -eq 200) { Pass "Server Online" "HTTP 200" }
    else { Fail "Server Online" "HTTP $($r.StatusCode)" }
}
catch { Fail "Server Online" $_.Exception.Message }

# TEST 2 - Login Page
Section "TEST 2 - LOGIN PAGE"
try {
    $r = Invoke-WebRequest -Uri "$baseUrl/" -UseBasicParsing
    $hasForm = ($r.Content -match "username") -and ($r.Content -match "password")
    if ($hasForm) { Pass "Login Page Loads" "Form elements found" }
    else { Fail "Login Page Loads" "No login form found" }
}
catch { Fail "Login Page Loads" $_.Exception.Message }

# TEST 3 - Admin Login
Section "TEST 3 - ADMIN LOGIN"
$session = $null
try {
    # Step 1: GET login page to get CSRF token and session cookie
    $loginPage = Invoke-WebRequest -Uri "$baseUrl/" -SessionVariable adminSession -UseBasicParsing

    # Step 2: Extract CSRF token from hidden input (name="csrf_test_name")
    $csrfValue = ""
    if ($loginPage.Content -match 'name="csrf_test_name"\s+value="([^"]+)"') {
        $csrfValue = $Matches[1]
    }
    elseif ($loginPage.Content -match 'name="csrf_test_name" value="([^"]+)"') {
        $csrfValue = $Matches[1]
    }

    # Step 3: POST login with CSRF token
    $loginData = "username=admin&password=admin123&csrf_test_name=$csrfValue"
    $loginResp = Invoke-WebRequest -Uri "$baseUrl/auth/login" -Method POST `
        -Body $loginData -ContentType "application/x-www-form-urlencoded" `
        -WebSession $adminSession -UseBasicParsing -MaximumRedirection 10
    $session = $adminSession

    # Verify session by calling an authenticated API
    $testApi = Invoke-WebRequest -Uri "$baseUrl/api/admin/users" -WebSession $adminSession -UseBasicParsing
    $testData = $testApi.Content | ConvertFrom-Json
    if ($testData.status -eq "success") {
        Pass "Admin Login" "Session valid - API accessible (CSRF token used)"
    }
    else {
        Fail "Admin Login" "API not accessible after login"
    }
}
catch {
    Fail "Admin Login" $_.Exception.Message
}

# TEST 4 - Get Users
Section "TEST 4 - USERS API"
$firstUser = $null
if ($session) {
    try {
        $r = Invoke-WebRequest -Uri "$baseUrl/api/admin/users" -WebSession $session -UseBasicParsing
        $d = $r.Content | ConvertFrom-Json
        if ($d.status -eq "success") {
            $count = $d.data.Count
            $firstUser = $d.data[0]
            Pass "GET /api/admin/users" "Count=$count, Page=$($d.meta.page)/$($d.meta.total_pages)"
        }
        else { Fail "GET /api/admin/users" $d.message }
    }
    catch { Fail "GET /api/admin/users" $_.Exception.Message }

    if ($firstUser) {
        $uid = $firstUser.id
        try {
            $r = Invoke-WebRequest -Uri "$baseUrl/api/admin/users/$uid" -WebSession $session -UseBasicParsing
            $d = $r.Content | ConvertFrom-Json
            if ($d.status -eq "success") { Pass "GET /api/admin/users/$uid" "Name=$($d.data.full_name)" }
            else { Fail "GET /api/admin/users/$uid" $d.message }
        }
        catch { Fail "GET /api/admin/users/$uid" $_.Exception.Message }
    }
}
else { Fail "GET /api/admin/users" "No session (login failed)" }

# TEST 5 - Edit User (duplicate email fix)
Section "TEST 5 - EDIT USER DUPLICATE EMAIL FIX"
if ($firstUser -and $session) {
    $uid = $firstUser.id
    $nm = $firstUser.full_name + " Test"
    $un = $firstUser.username
    $em = $firstUser.email
    $rl = $firstUser.role
    $body = "{`"name`":`"$nm`",`"username`":`"$un`",`"email`":`"$em`",`"role`":`"$rl`",`"is_active`":1}"
    try {
        $r = Invoke-WebRequest -Uri "$baseUrl/api/admin/users/$uid" -Method PUT `
            -Body $body -ContentType "application/json" -WebSession $session -UseBasicParsing
        $d = $r.Content | ConvertFrom-Json
        if ($d.status -eq "success") {
            Pass "PUT users/$uid same email" "No duplicate error - $($d.message)"
        }
        else { Fail "PUT users/$uid same email" $d.message }
    }
    catch {
        $errMsg = $_.Exception.Message
        try { $eb = $_.ErrorDetails.Message | ConvertFrom-Json; $errMsg = $eb.message } catch {}
        Fail "PUT users/$uid same email" $errMsg
    }
    # Restore
    $origNm = $firstUser.full_name
    $restoreBody = "{`"name`":`"$origNm`",`"username`":`"$un`",`"email`":`"$em`",`"role`":`"$rl`",`"is_active`":1}"
    try {
        Invoke-WebRequest -Uri "$baseUrl/api/admin/users/$uid" -Method PUT `
            -Body $restoreBody -ContentType "application/json" -WebSession $session -UseBasicParsing | Out-Null
        Write-Host "   (restored original name)"
    }
    catch {}
}
else { Fail "PUT users same email" "No session or no user" }

# TEST 6 - Reset Password
Section "TEST 6 - RESET PASSWORD"
if ($firstUser -and $session) {
    $uid = $firstUser.id
    $resetBody = "{`"new_password`":`"testpass999`"}"
    try {
        $r = Invoke-WebRequest -Uri "$baseUrl/api/admin/users/$uid/reset-password" -Method POST `
            -Body $resetBody -ContentType "application/json" -WebSession $session -UseBasicParsing
        $d = $r.Content | ConvertFrom-Json
        if ($d.status -eq "success") {
            Pass "POST reset-password" "User=$($d.data.username) PW=$($d.data.new_password)"
        }
        else { Fail "POST reset-password" $d.message }
    }
    catch {
        $errMsg = $_.Exception.Message
        try { $eb = $_.ErrorDetails.Message | ConvertFrom-Json; $errMsg = $eb.message } catch {}
        Fail "POST reset-password" $errMsg
    }
    # Restore admin password
    $restorePw = "{`"new_password`":`"admin123`"}"
    try {
        Invoke-WebRequest -Uri "$baseUrl/api/admin/users/$uid/reset-password" -Method POST `
            -Body $restorePw -ContentType "application/json" -WebSession $session -UseBasicParsing | Out-Null
        Write-Host "   (restored original password)"
    }
    catch {}
}
else { Fail "POST reset-password" "No session or no user" }

# TEST 7 - Students API
Section "TEST 7 - STUDENTS API"
$firstStudent = $null
if ($session) {
    try {
        $r = Invoke-WebRequest -Uri "$baseUrl/api/admin/students" -WebSession $session -UseBasicParsing
        $d = $r.Content | ConvertFrom-Json
        if ($d.status -eq "success") {
            $count = if ($d.data) { $d.data.Count } else { 0 }
            if ($count -gt 0) { $firstStudent = $d.data[0] }
            Pass "GET /api/admin/students" "Count=$count"
        }
        else { Fail "GET /api/admin/students" $d.message }
    }
    catch { Fail "GET /api/admin/students" $_.Exception.Message }

    if ($firstStudent) {
        $sid = $firstStudent.id
        try {
            $r = Invoke-WebRequest -Uri "$baseUrl/api/admin/students/$sid" -WebSession $session -UseBasicParsing
            $d = $r.Content | ConvertFrom-Json
            if ($d.status -eq "success") {
                Pass "GET /api/admin/students/$sid" "Name=$($d.data.full_name) NIS=$($d.data.nis)"
            }
            else { Fail "GET /api/admin/students/$sid" $d.message }
        }
        catch { Fail "GET /api/admin/students/$sid" $_.Exception.Message }
    }
}
else { Fail "GET /api/admin/students" "No session" }

# TEST 8 - Classes API
Section "TEST 8 - CLASSES API"
$firstClass = $null
if ($session) {
    try {
        $r = Invoke-WebRequest -Uri "$baseUrl/api/admin/classes" -WebSession $session -UseBasicParsing
        $d = $r.Content | ConvertFrom-Json
        if ($d.status -eq "success") {
            $count = if ($d.data) { $d.data.Count } else { 0 }
            if ($count -gt 0) { $firstClass = $d.data[0] }
            Pass "GET /api/admin/classes" "Count=$count, First=$($firstClass.name)"
        }
        else { Fail "GET /api/admin/classes" $d.message }
    }
    catch { Fail "GET /api/admin/classes" $_.Exception.Message }
}
else { Fail "GET /api/admin/classes" "No session" }

# TEST 9 - Attendance API
Section "TEST 9 - ATTENDANCE API"
if ($session) {
    $today = (Get-Date).ToString("yyyy-MM-dd")

    # attendance-logs (no class_id needed)
    try {
        $r = Invoke-WebRequest -Uri "$baseUrl/api/admin/attendance-logs" -WebSession $session -UseBasicParsing
        $d = $r.Content | ConvertFrom-Json
        if ($d.status -eq "success") {
            $count = if ($d.data) { $d.data.Count } else { 0 }
            Pass "GET /api/admin/attendance-logs" "Entries=$count"
        }
        else { Fail "GET /api/admin/attendance-logs" $d.message }
    }
    catch { Fail "GET /api/admin/attendance-logs" $_.Exception.Message }

    # Attendance requires class_id - use first class
    if ($firstClass) {
        $cid = $firstClass.id
        try {
            $r = Invoke-WebRequest -Uri "$baseUrl/api/admin/attendance?class_id=$cid&date=$today" -WebSession $session -UseBasicParsing
            $d = $r.Content | ConvertFrom-Json
            # Note: attendance API returns {success: true|false, data: [...]}
            if ($d.success -eq $true) {
                $count = if ($d.data) { $d.data.Count } else { 0 }
                Pass "GET /api/admin/attendance" "class_id=$cid date=$today Records=$count"
            }
            else { Fail "GET /api/admin/attendance" "$($d.message)" }
        }
        catch { Fail "GET /api/admin/attendance" $_.Exception.Message }

        # Bulk save attendance - proper format
        if ($firstStudent) {
            $sid = $firstStudent.id
            $attBody = "{`"date`":`"$today`",`"class_id`":$cid,`"records`":[{`"student_id`":$sid,`"status`":`"hadir`"}]}"
            try {
                $r = Invoke-WebRequest -Uri "$baseUrl/api/admin/attendance" -Method POST `
                    -Body $attBody -ContentType "application/json" -WebSession $session -UseBasicParsing
                $d = $r.Content | ConvertFrom-Json
                if ($d.success -eq $true) {
                    Pass "POST /api/admin/attendance bulk" $d.message
                }
                else { Fail "POST /api/admin/attendance bulk" "$($d.message)" }
            }
            catch {
                $errMsg = $_.Exception.Message
                try { $eb = $_.ErrorDetails.Message | ConvertFrom-Json; $errMsg = $eb.message } catch {}
                Fail "POST /api/admin/attendance bulk" $errMsg
            }
        }
    }
    else { Fail "GET /api/admin/attendance" "No class data to test with" }
}
else { Fail "Attendance API" "No session" }

# TEST 10 - Habits API
Section "TEST 10 - HABITS API"
if ($session -and $firstClass) {
    $cid = $firstClass.id
    $month = (Get-Date).Month
    $year = (Get-Date).Year

    # habits GET (requires class_id)
    try {
        $r = Invoke-WebRequest -Uri "$baseUrl/api/admin/habits?class_id=$cid&month=$month&year=$year" -WebSession $session -UseBasicParsing
        $d = $r.Content | ConvertFrom-Json
        if ($d.status -eq "success") {
            $studentCount = if ($d.data.students) { $d.data.students.Count } else { 0 }
            Pass "GET /api/admin/habits" "class_id=$cid Students=$studentCount"
        }
        else { Fail "GET /api/admin/habits" $d.message }
    }
    catch { Fail "GET /api/admin/habits" $_.Exception.Message }

    # habits recap (requires class_id)
    try {
        $r = Invoke-WebRequest -Uri "$baseUrl/api/admin/habits/recap?class_id=$cid&month=$month&year=$year" -WebSession $session -UseBasicParsing
        $d = $r.Content | ConvertFrom-Json
        if ($d.status -eq "success") {
            Pass "GET /api/admin/habits/recap" "class_id=$cid month=$month/$year"
        }
        else { Fail "GET /api/admin/habits/recap" $d.message }
    }
    catch { Fail "GET /api/admin/habits/recap" $_.Exception.Message }

    # Save a habit for first student in that class (if any)
    if ($firstStudent) {
        $today = (Get-Date).ToString("yyyy-MM-dd")
        $habitBody = "{`"student_id`":$($firstStudent.id),`"date`":`"$today`",`"solat_subuh`":true,`"membaca_quran`":true}"
        try {
            $r = Invoke-WebRequest -Uri "$baseUrl/api/admin/habits" -Method POST `
                -Body $habitBody -ContentType "application/json" -WebSession $session -UseBasicParsing
            $d = $r.Content | ConvertFrom-Json
            if ($d.status -eq "success") {
                Pass "POST /api/admin/habits save" $d.message
            }
            else { Fail "POST /api/admin/habits save" $d.message }
        }
        catch {
            $errMsg = $_.Exception.Message
            try { $eb = $_.ErrorDetails.Message | ConvertFrom-Json; $errMsg = $eb.message } catch {}
            Fail "POST /api/admin/habits save" $errMsg
        }
    }
}
elseif (-not $session) {
    Fail "Habits API" "No session"
}
else {
    Fail "Habits API" "No class data available"
}

# TEST 11 - Devices API
Section "TEST 11 - DEVICES API"
if ($session) {
    try {
        $r = Invoke-WebRequest -Uri "$baseUrl/api/admin/devices" -WebSession $session -UseBasicParsing
        $d = $r.Content | ConvertFrom-Json
        if ($d.status -eq "success") {
            $count = if ($d.data) { $d.data.Count } else { 0 }
            Pass "GET /api/admin/devices" "Count=$count"
        }
        else { Fail "GET /api/admin/devices" $d.message }
    }
    catch { Fail "GET /api/admin/devices" $_.Exception.Message }
}
else { Fail "GET /api/admin/devices" "No session" }

# TEST 12 - Shifts API
Section "TEST 12 - SHIFTS API"
if ($session) {
    try {
        $r = Invoke-WebRequest -Uri "$baseUrl/api/admin/shifts" -WebSession $session -UseBasicParsing
        $d = $r.Content | ConvertFrom-Json
        # Shifts API returns {success: true, data: [...]}
        if ($d.success -eq $true) {
            $count = if ($d.data) { $d.data.Count } else { 0 }
            Pass "GET /api/admin/shifts" "Count=$count"
        }
        else { Fail "GET /api/admin/shifts" "$($d.message)" }
    }
    catch { Fail "GET /api/admin/shifts" $_.Exception.Message }
}
else { Fail "GET /api/admin/shifts" "No session" }

# TEST 13 - School Holidays API
Section "TEST 13 - SCHOOL HOLIDAYS API"
if ($session) {
    try {
        $month = (Get-Date).Month; $year = (Get-Date).Year
        $r = Invoke-WebRequest -Uri "$baseUrl/api/admin/school-holidays?year=$year&month=$month" -WebSession $session -UseBasicParsing
        $d = $r.Content | ConvertFrom-Json
        # School-holidays API returns {success: true, data: [...]}
        if ($d.success -eq $true) {
            $count = if ($d.data) { $d.data.Count } else { 0 }
            Pass "GET /api/admin/school-holidays" "Count=$count holidays in $month/$year"
        }
        else { Fail "GET /api/admin/school-holidays" "$($d.message)" }
    }
    catch { Fail "GET /api/admin/school-holidays" $_.Exception.Message }
}
else { Fail "school-holidays" "No session" }

# TEST 14 - Rekap API
Section "TEST 14 - REKAP API"
if ($session -and $firstClass) {
    try {
        $cid = $firstClass.id
        $month = (Get-Date).Month
        $year = (Get-Date).Year
        $r = Invoke-WebRequest -Uri "$baseUrl/api/admin/rekap?class_id=$cid&month=$month&year=$year" -WebSession $session -UseBasicParsing
        $d = $r.Content | ConvertFrom-Json
        # Rekap API returns {success: true, data: {...}}
        if ($d.success -eq $true) {
            Pass "GET /api/admin/rekap" "class_id=$cid month=$month/$year Class=$($d.data.class_name)"
        }
        else { Fail "GET /api/admin/rekap" "$($d.message)" }
    }
    catch { Fail "GET /api/admin/rekap" $_.Exception.Message }
}
elseif (-not $session) {
    Fail "rekap" "No session"
}
else {
    Fail "rekap" "No class data available"
}

# TEST 15 - Admin HTML Pages
Section "TEST 15 - ADMIN HTML PAGES"
if ($session) {
    $pages = @(
        "/admin/dashboard",
        "/admin/students",
        "/admin/teachers",
        "/admin/classes",
        "/admin/devices",
        "/admin/device-mapping",
        "/admin/attendance",
        "/admin/attendance-logs",
        "/admin/habits-daily",
        "/admin/habits-monthly",
        "/admin/users",
        "/admin/rekap",
        "/admin/reports",
        "/admin/calendar"
    )
    foreach ($url in $pages) {
        try {
            $r = Invoke-WebRequest -Uri "$baseUrl$url" -WebSession $session -UseBasicParsing -MaximumRedirection 5
            if ($r.StatusCode -eq 200 -and $r.Content.Length -gt 1000) {
                Pass "PAGE $url" "HTTP 200  Len=$($r.Content.Length)"
            }
            elseif ($r.StatusCode -eq 200) {
                Fail "PAGE $url" "HTTP 200 but too short ($($r.Content.Length) chars)"
            }
            else {
                Fail "PAGE $url" "HTTP $($r.StatusCode)"
            }
        }
        catch { Fail "PAGE $url" $_.Exception.Message }
    }
}
else { Fail "Admin Pages" "No session" }

# SUMMARY
Write-Host "`n======================================================"
Write-Host "  HASIL TESTING - SUMMARY"
Write-Host "======================================================"
$total = $passed + $failed
$pct = if ($total -gt 0) { [math]::Round($passed / $total * 100, 1) } else { 0 }
Write-Host "  PASSED : $passed"
Write-Host "  FAILED : $failed"
Write-Host "  TOTAL  : $total tests  ($pct% success rate)"
Write-Host "======================================================"
Write-Host ""
Write-Host "--- FAILED TESTS ---"
$failedList = $results | Where-Object { $_.Status -eq "FAIL" }
if ($failedList.Count -eq 0) {
    Write-Host "  (none - all tests passed!)"
}
else {
    foreach ($f in $failedList) { Write-Host "  FAIL: $($f.Test)  $($f.Info)" }
}
Write-Host "`nTesting completed: $(Get-Date -Format 'dd/MM/yyyy HH:mm:ss')"
