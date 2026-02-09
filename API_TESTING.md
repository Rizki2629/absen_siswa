# Testing API dengan cURL

## 1. Simulasi Data dari Mesin Fingerprint

### Send Single Attendance Log
```bash
curl -X POST http://localhost:8080/iclock/cdata?SN=DEV001&table=ATTLOG \
  -H "Content-Type: text/plain" \
  -d "105	2026-02-07 07:15:00	0	0"
```

### Send Multiple Logs (Batch)
```bash
curl -X POST http://localhost:8080/iclock/cdata?SN=DEV001&table=ATTLOG \
  -H "Content-Type: text/plain" \
  -d "105	2026-02-07 07:15:00	0	0
106	2026-02-07 07:20:00	0	0
107	2026-02-07 07:05:00	0	0
105	2026-02-07 15:30:00	0	0
106	2026-02-07 15:35:00	0	0"
```

Format: `PIN<TAB>DATETIME<TAB>STATUS<TAB>WORKCODE`
- STATUS: 0 = check in, 1 = check out, 4 = overtime in, 5 = overtime out
- WORKCODE: biasanya 0

### Device Registry
```bash
curl -X POST "http://localhost:8080/iclock/registry?SN=DEV001&DevName=Mesin%20Gerbang&LOC=Gerbang%20Utama"
```

## 2. API Authentication

### Login (Get Token)
```bash
curl -X POST http://localhost:8080/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "username": "admin",
    "password": "admin123"
  }'
```

Response akan berisi token yang digunakan untuk request berikutnya.

### Get Current User
```bash
curl -X GET http://localhost:8080/api/me \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## 3. Admin API

### List Devices
```bash
curl -X GET http://localhost:8080/api/admin/devices \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Add Device
```bash
curl -X POST http://localhost:8080/api/admin/devices \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "sn": "DEV002",
    "name": "Mesin Lantai 2",
    "ip_address": "192.168.1.101",
    "port": 4370,
    "location": "Lantai 2",
    "comm_key": ""
  }'
```

### Test Device Connection
```bash
curl -X POST http://localhost:8080/api/admin/devices/1/test-connection \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Create Device User Mapping
```bash
curl -X POST http://localhost:8080/api/admin/device-user-maps \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "device_id": 1,
    "pin": "105",
    "student_id": 1
  }'
```

### List Mappings
```bash
curl -X GET http://localhost:8080/api/admin/device-user-maps \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Create Shift
```bash
curl -X POST http://localhost:8080/api/admin/shifts \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Shift Pagi",
    "check_in_start": "06:30:00",
    "check_in_end": "07:15:00",
    "check_out_start": "15:00:00",
    "check_out_end": "17:00:00",
    "late_tolerance": 5,
    "is_active": 1
  }'
```

## 4. Guru Piket API

### Get Daily Summary
```bash
curl -X GET "http://localhost:8080/api/guru-piket/daily-summary?date=2026-02-07" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Get Students Not Checked In
```bash
curl -X GET "http://localhost:8080/api/guru-piket/not-checked-in?date=2026-02-07" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Get Recent Logs (Real-time)
```bash
curl -X GET "http://localhost:8080/api/guru-piket/recent-logs?limit=20" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Record Exception (Sakit)
```bash
curl -X POST http://localhost:8080/api/guru-piket/exceptions \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "student_id": 1,
    "date": "2026-02-07",
    "exception_type": "sakit",
    "notes": "Demam tinggi, surat dokter dilampirkan"
  }'
```

### Record Exception (Lupa Scan)
```bash
curl -X POST http://localhost:8080/api/guru-piket/exceptions \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "student_id": 2,
    "date": "2026-02-07",
    "exception_type": "lupa_scan",
    "check_in_time": "07:10:00",
    "check_out_time": "15:30:00",
    "notes": "Sidik jari rusak, hadir tapi tidak bisa scan"
  }'
```

## 5. Student/Parent API

### Get Profile
```bash
curl -X GET http://localhost:8080/api/student/profile \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Get Today's Attendance
```bash
curl -X GET http://localhost:8080/api/student/attendance/today \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Get Attendance Summary
```bash
curl -X GET "http://localhost:8080/api/student/attendance/summary?start_date=2026-01-01&end_date=2026-02-07" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Get Notifications
```bash
curl -X GET http://localhost:8080/api/student/notifications \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Mark Notification as Read
```bash
curl -X PUT http://localhost:8080/api/student/notifications/1/read \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## 6. Testing Workflow Complete

### Scenario: Siswa Budi Scan di Pagi Hari

```bash
# 1. Siswa scan jari di mesin (simulasi)
curl -X POST http://localhost:8080/iclock/cdata?SN=DEV001&table=ATTLOG \
  -H "Content-Type: text/plain" \
  -d "105	2026-02-07 07:15:00	0	0"

# 2. Cek apakah data masuk ke logs
curl -X GET "http://localhost:8080/api/guru-piket/recent-logs?limit=1" \
  -H "Authorization: Bearer GURU_TOKEN"

# 3. Cek summary hari ini
curl -X GET "http://localhost:8080/api/guru-piket/daily-summary?date=2026-02-07" \
  -H "Authorization: Bearer GURU_TOKEN"

# 4. Login sebagai siswa/ortu, cek kehadiran
curl -X GET http://localhost:8080/api/student/attendance/today \
  -H "Authorization: Bearer STUDENT_TOKEN"

# 5. Cek notifikasi
curl -X GET http://localhost:8080/api/student/notifications \
  -H "Authorization: Bearer PARENT_TOKEN"
```

### Scenario: Siswa Sakit, Guru Input Manual

```bash
# 1. Guru piket input exception
curl -X POST http://localhost:8080/api/guru-piket/exceptions \
  -H "Authorization: Bearer GURU_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "student_id": 3,
    "date": "2026-02-07",
    "exception_type": "sakit",
    "notes": "Sakit demam, tidak masuk"
  }'

# 2. Cek summary, status siswa akan berubah jadi "sakit"
curl -X GET "http://localhost:8080/api/guru-piket/daily-summary?date=2026-02-07" \
  -H "Authorization: Bearer GURU_TOKEN"
```

## 7. Batch Testing Script

Buat file `test-attendance.sh`:

```bash
#!/bin/bash

BASE_URL="http://localhost:8080"
TOKEN="YOUR_TOKEN_HERE"

echo "🚀 Testing Attendance System..."

# Test 1: Simulate attendance logs
echo "📝 Test 1: Sending attendance logs..."
curl -X POST ${BASE_URL}/iclock/cdata?SN=DEV001&table=ATTLOG \
  -H "Content-Type: text/plain" \
  -d "105	2026-02-07 07:15:00	0	0
106	2026-02-07 07:20:00	0	0
107	2026-02-07 07:05:00	0	0"

echo ""
echo "✅ Logs sent"

# Test 2: Check recent logs
echo "📊 Test 2: Checking recent logs..."
curl -X GET "${BASE_URL}/api/guru-piket/recent-logs?limit=5" \
  -H "Authorization: Bearer ${TOKEN}"

echo ""
echo "✅ Recent logs retrieved"

# Test 3: Check daily summary
echo "📈 Test 3: Checking daily summary..."
curl -X GET "${BASE_URL}/api/guru-piket/daily-summary?date=2026-02-07" \
  -H "Authorization: Bearer ${TOKEN}"

echo ""
echo "✅ Daily summary retrieved"

echo "🎉 All tests completed!"
```

Jalankan:
```bash
chmod +x test-attendance.sh
./test-attendance.sh
```

## Tips

1. **Save Token ke Variable:**
```bash
TOKEN=$(curl -X POST http://localhost:8080/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"username":"admin","password":"admin123"}' \
  | jq -r '.data.token')

echo $TOKEN
```

2. **Pretty Print JSON:**
```bash
curl ... | jq '.'
```

3. **Save Response to File:**
```bash
curl ... > response.json
```

4. **Include Response Headers:**
```bash
curl -i ...
```
