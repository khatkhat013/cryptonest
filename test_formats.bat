@echo off
REM Test multiple chat ID formats

setlocal enabledelayedexpansion

set BOT_TOKEN=8426503372:AAEGNx3nuaAX4-8zaQ-Rg4RUO4PkRHl39ZA

echo.
echo ╔════════════════════════════════════════════════════════════╗
echo ║    TESTING MULTIPLE CHAT ID FORMATS                      ║
echo ╚════════════════════════════════════════════════════════════╝
echo.

echo 🔹 Bot Token: 8426503372:AAEGNx3nuaAX4-8zaQ-Rg4RUO4PkRHl39ZA
echo.

REM Test Format 1: Original
echo 📍 Format 1: -5040335752
curl -s -X POST "https://api.telegram.org/bot%BOT_TOKEN%/sendMessage" ^
  -d "chat_id=-5040335752" ^
  -d "text=Test Format 1" > result1.json
echo Result:
type result1.json
echo.

REM Test Format 2: With -100 prefix
echo 📍 Format 2: -1005040335752
curl -s -X POST "https://api.telegram.org/bot%BOT_TOKEN%/sendMessage" ^
  -d "chat_id=-1005040335752" ^
  -d "text=Test Format 2" > result2.json
echo Result:
type result2.json
echo.

REM Test Format 3: Without minus
echo 📍 Format 3: 5040335752
curl -s -X POST "https://api.telegram.org/bot%BOT_TOKEN%/sendMessage" ^
  -d "chat_id=5040335752" ^
  -d "text=Test Format 3" > result3.json
echo Result:
type result3.json
echo.

del result1.json result2.json result3.json 2>nul
