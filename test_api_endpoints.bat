@echo off
echo ============================================
echo  TESTING MITRA ROLE API ENDPOINTS
echo ============================================
echo.

REM Get Mitra token
echo [1/5] Getting Mitra authentication token...
php get_mitra_token.php > token.tmp
set /p TOKEN=<token.tmp
echo Token received: %TOKEN:~0,20%...
echo.

REM Test 1: Accept Schedule (ID 10)
echo [2/5] Testing ACCEPT endpoint (Schedule ID: 10)...
curl -X POST http://127.0.0.1:8000/api/schedules/10/accept ^
  -H "Authorization: Bearer %TOKEN%" ^
  -H "Accept: application/json" ^
  -H "Content-Type: application/json"
echo.
echo.

REM Test 2: Start Schedule (ID 11)
echo [3/5] Testing START endpoint (Schedule ID: 11)...
curl -X POST http://127.0.0.1:8000/api/schedules/11/start ^
  -H "Authorization: Bearer %TOKEN%" ^
  -H "Accept: application/json" ^
  -H "Content-Type: application/json"
echo.
echo.

REM Test 3: Complete Schedule (ID 12)
echo [4/5] Testing COMPLETE endpoint (Schedule ID: 12)...
curl -X POST http://127.0.0.1:8000/api/schedules/12/complete ^
  -H "Authorization: Bearer %TOKEN%" ^
  -H "Accept: application/json" ^
  -H "Content-Type: application/json" ^
  -d "{\"actual_weight\": 11.0, \"completion_notes\": \"Test berhasil\"}"
echo.
echo.

REM Test 4: Get All Schedules
echo [5/5] Testing GET schedules endpoint...
curl http://127.0.0.1:8000/api/schedules ^
  -H "Authorization: Bearer %TOKEN%" ^
  -H "Accept: application/json"
echo.
echo.

REM Cleanup
del token.tmp

echo ============================================
echo  API TESTING COMPLETE!
echo ============================================
pause
