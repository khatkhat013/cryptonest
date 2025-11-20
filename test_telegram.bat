@echo off
REM Telegram Bot Connection Test

setlocal enabledelayedexpansion

set BOT_TOKEN=8426503372:AAEGNx3nuaAX4-8zaQ-Rg4RUO4PkRHl39ZA
set CHAT_ID=-5040335752

echo.
echo ╔════════════════════════════════════════════════════════════╗
echo ║    TELEGRAM BOT TEST WITH CURL                           ║
echo ╚════════════════════════════════════════════════════════════╝
echo.

echo 🔹 Bot Token: 8426503372:AAEGNx3nuaAX4-8zaQ-Rg4RUO4PkRHl39ZA
echo 🔹 Chat ID: -5040335752
echo 🔹 Testing Telegram API...
echo.

REM Test Step 1: Get Bot Info
echo 📍 Step 1: Getting bot info...
curl -s "https://api.telegram.org/bot%BOT_TOKEN%/getMe" > bot_info.json
type bot_info.json
echo.

REM Test Step 2: Send Message
echo 📍 Step 2: Sending test message...
set MESSAGE=CryptoNest Bot Test - %date% %time%

curl -s -X POST "https://api.telegram.org/bot%BOT_TOKEN%/sendMessage" ^
  -d "chat_id=%CHAT_ID%" ^
  -d "text=🤖 CryptoNest Telegram Bot Test - SUCCESS!" ^
  -d "parse_mode=HTML" > send_result.json

echo.
echo 📋 Response:
type send_result.json
echo.

REM Cleanup
del bot_info.json send_result.json 2>nul

echo ✅ Test complete. Check response above for results.
echo.
