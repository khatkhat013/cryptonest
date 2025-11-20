# Telegram Bot Direct Test - PowerShell (Simple)
# Usage: .\test_telegram_ps.ps1

$botToken = "8426503372:AAEGNx3nuaAX4-8zaQ-Rg4RUO4PkRHl39ZA"
$chatId = "6345824401"

Write-Host ""
Write-Host "=====================================================" 
Write-Host "TELEGRAM BOT TEST - PowerShell"
Write-Host "=====================================================" 
Write-Host ""

Write-Host "Bot Token: $($botToken.Substring(0,20))..."
Write-Host "Chat ID: $chatId"
Write-Host "Testing Telegram API..."
Write-Host ""

# Step 1: Get Bot Info
Write-Host "Step 1: Getting bot info..."

try {
    $botUrl = "https://api.telegram.org/bot$botToken/getMe"
    $botResponse = Invoke-WebRequest -Uri $botUrl -UseBasicParsing | ConvertFrom-Json
    
    if ($botResponse.ok) {
        Write-Host "SUCCESS: Bot info retrieved"
        Write-Host "  Bot ID: $($botResponse.result.id)"
        Write-Host "  Bot Username: @$($botResponse.result.username)"
        Write-Host "  First Name: $($botResponse.result.first_name)"
        Write-Host ""
    } else {
        Write-Host "ERROR: Bot API Error - $($botResponse.description)"
        exit
    }
} catch {
    Write-Host "ERROR: Connection Error - $_"
    exit
}

# Step 2: Send Test Message
Write-Host "Step 2: Sending test message..."

$testMessage = "CryptoNest Telegram Bot Test - SUCCESS!"

$sendUrl = "https://api.telegram.org/bot$botToken/sendMessage"

$body = @{
    chat_id = $chatId
    text = $testMessage
    parse_mode = "HTML"
} | ConvertTo-Json

try {
    $response = Invoke-WebRequest -Uri $sendUrl -Method Post -Body $body -ContentType "application/json" -UseBasicParsing | ConvertFrom-Json
    
    Write-Host ""
    Write-Host "API Response:"
    
    if ($response.ok) {
        Write-Host "  Status: OK (SUCCESS)"
        Write-Host "  Message ID: $($response.result.message_id)"
        Write-Host "  Chat ID: $($response.result.chat.id)"
        Write-Host ""
        Write-Host "SUCCESS! Message sent to Telegram!"
        Write-Host "Check your Telegram group for the test message."
        Write-Host ""
    } else {
        Write-Host "  Status: FAILED"
        Write-Host "  Error Code: $($response.error_code)"
        Write-Host "  Error Message: $($response.description)"
        Write-Host ""
        Write-Host "FAILED! $($response.description)"
        Write-Host ""
        Write-Host "Troubleshooting Tips:"
        
        if ($response.description -match 'chat not found') {
            Write-Host "  - Chat ID might be incorrect"
            Write-Host "  - Bot might not be in the group"
            Write-Host "  - Try using @userinfobot to get correct ID"
        } elseif ($response.description -match 'FORBIDDEN') {
            Write-Host "  - Bot doesn't have permission to send messages"
            Write-Host "  - Make bot an Admin in the group"
            Write-Host "  - Grant Send Messages permission to bot"
        }
    }
} catch {
    Write-Host "ERROR: Request Error - $_"
    Write-Host ""
    Write-Host "Check your internet connection"
}

Write-Host ""

Write-Host "`n"
