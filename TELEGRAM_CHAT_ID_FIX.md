# Telegram Setup - Complete Verification

## 🔍 Chat ID Verification Steps

The error **"chat not found"** means one of these:

1. **Bot is NOT in the group**
2. **Chat ID is incorrect**
3. **Chat ID format is wrong**
4. **Group has been deleted or archived**

---

## ✅ Complete Fix Steps (Follow Exactly)

### Step 1: Create New Private Group

1. Open **Telegram**
2. Tap **`+`** (New)
3. Select **`New Group`**
4. Name: **`CryptoNest Notifications`**
5. Choose **`Private`** (အရေးကြီး!)
6. Click **`Create`**

---

### Step 2: Add Bot to Group

1. Tap **`Add Members`**
2. Search: **`CryptoNest_Bot`** (or search for `8426503372`)
3. Select bot and **`Add`**
4. Make bot **`Admin`**:
   - Tap bot name
   - Click **`...`** (menu)
   - Select **`Make Admin`** or **`Promote to Admin`**
   - Enable: ✅ Send Messages, ✅ Delete Messages, ✅ Pin Messages

---

### Step 3: Get Correct Chat ID

**Method A: Using @userinfobot (Easiest)**

1. In the group chat, type: **`/start`**
2. Search for **`@userinfobot`**
3. Click `/start`
4. In your group, type **`/start`**
5. Bot will reply with group ID like: `-100xxxxxxxxxx` or `-xxxxxxxxxx`
6. **Copy this ID exactly**

**Method B: Check Group Settings**

1. Group name → Click ℹ️ info
2. Look for ID in URL or group details

**Method C: Send Message and Check Logs**

1. Send a message to bot in group
2. Check where group ID appears

---

### Step 4: Update .env File

```bash
# .env

# Copy exactly as shown by @userinfobot
TELEGRAM_BOT_TOKEN=8426503372:AAEGNx3nuaAX4-8zaQ-Rg4RUO4PkRHl39ZA
TELEGRAM_CHANNEL_ID=-100xxxxxxxxxx   # ← Replace with your ID from @userinfobot
```

Example:
```bash
TELEGRAM_CHANNEL_ID=-1004567890123
```
or
```bash
TELEGRAM_CHANNEL_ID=-5040335752
```

---

### Step 5: Rebuild Configuration

Run this command:

```powershell
php artisan config:clear && php artisan config:cache
```

---

### Step 6: Verify Bot Can Send

Run PowerShell command:

```powershell
$botToken = "8426503372:AAEGNx3nuaAX4-8zaQ-Rg4RUO4PkRHl39ZA"
$chatId = "-5040335752"  # ← Your group ID

$body = @{
    chat_id = $chatId
    text = "🤖 CryptoNest Test Message"
    parse_mode = "HTML"
} | ConvertTo-Json

Invoke-WebRequest -Uri "https://api.telegram.org/bot$botToken/sendMessage" `
    -Method Post `
    -Body $body `
    -ContentType "application/json" | Select-Object -ExpandProperty Content | ConvertFrom-Json
```

If you see `"ok": true` - ✅ **Bot works!**

If you see `"ok": false` and `"error_code": 400` - ❌ **Chat ID or access issue**

---

### Step 7: Test from Landing Page

1. Open: `http://localhost:8000`
2. Login as Admin
3. Click **"Admin သို့ ဆက်သွယ်မည်"** on any plan
4. Check your Telegram group - message should appear!

---

## 🆘 Troubleshooting

| Error | Cause | Fix |
|-------|-------|-----|
| `chat not found` | Bot not in group or wrong ID | Add bot to group, verify ID with @userinfobot |
| `FORBIDDEN` | Bot no admin permissions | Make bot Admin, check permissions |
| `Bad Request` | Invalid chat ID format | Use ID from @userinfobot exactly |
| No message arrives | Config cache not rebuilt | Run `php artisan config:cache` |

---

## 📋 Checklist

- [ ] Created NEW private group
- [ ] Added bot to group
- [ ] Made bot Admin
- [ ] Got correct ID from @userinfobot
- [ ] Updated .env with exact ID
- [ ] Ran `php artisan config:cache`
- [ ] PowerShell test returned `"ok": true`
- [ ] Tested plan contact button

---

## 💡 Important Notes

✅ **Group must be PRIVATE** (not public)
✅ **Use ID from @userinfobot exactly** (copy-paste)
✅ **Bot must be Admin** (not just member)
✅ **Rebuild cache after any .env change**

---

**Still having issues? Provide:**
- Group ID from @userinfobot
- Screenshot of bot admin status in group
- Output from PowerShell test command above
