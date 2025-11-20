# Telegram Group Setup Guide - CryptoNest

## ✅ Complete Setup Instructions

### Step 1️⃣ - Create Private Telegram Group

**On Your Mobile/Desktop Telegram:**

1. Open Telegram
2. Tap **`+`** button (top-left or menu)
3. Select **`New Group`**
4. Type group name: **`CryptoNest Admin Notifications`**
5. **Select "Private"** (အရေးကြီး!)
6. Add your Bot: Search for **`@CryptoNest_Bot`** and add it
7. Make bot **Admin** → Settings → Permissions → Allow send messages, delete messages, etc.

---

### Step 2️⃣ - Get Group ID (Automatic Way)

Run this Laravel artisan command in your terminal:

```powershell
php artisan telegram:setup-group
```

This command will:
- ✅ Display all groups your bot is part of
- ✅ Let you select the group
- ✅ Automatically update `.env` file
- ✅ Rebuild config cache

**That's it! No manual ID hunting needed.**

---

### Step 3️⃣ - Manual Way (If Auto Method Doesn't Work)

If the command doesn't find your group:

1. **Send `/start` to your bot in the group** (in group chat)
2. Open Telegram Desktop or Web
3. Search for **`@userinfobot`**
4. Open the bot and tap **`/start`**
5. In your **private group**, type **`/start`** again
6. Bot replies with group info including ID (format: `-100xxxxxxxxxx`)
7. Copy the ID

Then manually update `.env`:

```bash
# .env file
TELEGRAM_CHANNEL_ID=-100xxxxxxxxxx
```

Rebuild cache:

```powershell
php artisan config:clear
php artisan config:cache
```

---

### Step 4️⃣ - Verify Bot Setup

Test if bot can send messages:

```powershell
php artisan tinker
```

Then in tinker:

```php
>>> App\Services\TelegramService::verifyBotAccess(config('services.telegram.channel_id'))
```

You should see:
- ✅ Bot verified
- ✅ Test message sent to group
- ✅ Message ID returned

---

### Step 5️⃣ - Test Plan Contact Button

1. Go to landing page: `http://localhost:8000`
2. Login as Admin
3. Click **"Admin သို့ ဆက်သွယ်မည်"** button on any plan card
4. Check your **Telegram private group** - message should appear! 🎉

---

## 🔧 Troubleshooting

### Issue: Bot not found in group

**Solution:**
- Add bot manually: `/addbot CryptoNest_Bot`
- Make sure group is **Private** (not public)
- Check bot has **Admin privileges**

### Issue: Group ID not showing

**Solution:**
```powershell
# Check what updates bot received
php artisan tinker
>>> App\Services\TelegramService::getGroupUpdates()
```

### Issue: Message not arriving

**Check logs:**

```powershell
tail -f storage/logs/laravel.log | findstr Telegram
```

Look for:
- ✅ "Telegram message sent successfully"
- ❌ "chat not found" → group ID is wrong
- ❌ "FORBIDDEN" → bot doesn't have permissions

---

## 📝 Environment Variables

Required in `.env`:

```bash
TELEGRAM_BOT_TOKEN=8426503372:AAEGNx3nuaAX4-8zaQ-Rg4RUO4PkRHl39ZA
TELEGRAM_CHANNEL_ID=-100xxxxxxxxxx
```

---

## 🎯 Quick Checklist

- [ ] Private group created with name "CryptoNest Admin Notifications"
- [ ] Bot added to group
- [ ] Bot has Admin privileges in group
- [ ] `/start` sent to bot in group
- [ ] Group ID obtained (format: -100xxxxx)
- [ ] `.env` file updated with correct ID
- [ ] Config cache rebuilt: `php artisan config:cache`
- [ ] Test message sent from button
- [ ] Message arrived in Telegram group ✅

---

## 📚 Useful Commands

```powershell
# Automatic setup
php artisan telegram:setup-group

# Check bot configuration
php artisan tinker
>>> config('services.telegram')

# Verify bot can send messages
php artisan tinker
>>> App\Services\TelegramService::verifyBotAccess('-100xxxxxxxxxx')

# Get all active groups
php artisan tinker
>>> App\Services\TelegramService::getGroupUpdates()

# Clear cache after changes
php artisan config:clear && php artisan config:cache

# Watch logs for Telegram messages
tail -f storage/logs/laravel.log | findstr Telegram
```

---

**✨ Done! Your Telegram notifications should now work perfectly!**
