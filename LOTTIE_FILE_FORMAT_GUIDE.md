# Lottie Animation Files - File Format လမ်းညွှန်

## ပုံမှန် Lottie Animation File Formats

### 1. **.lottie** (Recommended - Best)
- **Description**: Lottie host ကနေ ရသော compressed binary format
- **Usage**: 
  ```html
  <dotlottie-wc 
      src="path/to/animation.lottie" 
      autoplay loop>
  </dotlottie-wc>
  ```
- **Advantages**: အငယ်ဆုံး file size, အလျင်ဆုံး loading
- **Storage**: `public/animations/animation-name.lottie`

### 2. **.json** (JSON Format)
- **Description**: Lottie animation ကို JSON အဖြစ်
- **Usage**:
  ```html
  <dotlottie-wc 
      src="path/to/animation.json" 
      autoplay loop>
  </dotlottie-wc>
  ```
- **Advantages**: Text-based, ပြင်ဆင်လို့ ရ, GIT မှာ version control အလွယ်
- **Storage**: `public/animations/animation-name.json`

### 3. **.gif** (GIF Format - အသုံးပြုမ ကောင်း)
- **Description**: Static GIF အဖြစ် export
- **Issues**: 
  - ❌ အကျယ်အ ကျယ် လုံး မဟုတ်
  - ❌ Interactive မဖြစ်
  - ❌ Quality မကောင်း

---

## ကျွန်တော့် အကြံပြုချက်

### Step 1: Animation ကို Download လုပ်ပါ
```
URL: https://app.lottiefiles.com/animation/94a46239-de96-463e-b38a-aaf372186fc8
Download Format: .lottie (သို့မဟုတ်) .json
```

### Step 2: Laravel Project မှာ ထည့်ပါ
```
ဖိုင်လမ်းကြောင်း: public/animations/
```

### Step 3: Blade Template မှာ သုံးပါ
```html
<!-- Script import (if not already in layout) -->
<script type="module" src="https://unpkg.com/@lottiefiles/dotlottie-wc@0.8.5/dist/dotlottie-wc.js"></script>

<!-- Animation -->
<dotlottie-wc 
    src="{{ asset('animations/your-animation.lottie') }}" 
    style="width: 100%; height: auto" 
    autoplay 
    loop>
</dotlottie-wc>
```

---

## File Structure ဥပမာ

```
public/
├── animations/
│   ├── MJoyjsN8w3.lottie
│   ├── EaLJaJluzA.lottie
│   ├── RNsMUkyjxt.lottie
│   ├── I9uAcddYg9.lottie
│   └── 94a46239-new-animation.lottie  ← သင့်နတ်မ် animation
```

---

## Download လုပ်တဲ့ နည်း (PowerShell)

```powershell
# Lottie Files မှ .lottie format download လုပ်
# (သင့် browser မှ download လုပ်ပြီး manual ထည့်တဲ့ အသုံးအများဆုံး)

# သို့မဟုတ် direct link ရှိရင်:
$url = "https://assets.lottiefiles.com/your-animation-id/animation.lottie"
$destination = "public/animations/94a46239-new-animation.lottie"
Invoke-WebRequest -Uri $url -OutFile $destination
```

---

## CSP Error မဖြစ်အောင်

✅ ပြီးဆံပြီး CSP settings မှာ ခွင့်ပြုထားတယ်:
- `connect-src` → unpkg.com, cdn.jsdelivr.net
- `script-src` → unpkg.com
- `worker-src` → blob: (WASM)

---

## Summary

| Format | အကောင်းဆုံး | Storage | Size |
|--------|-----------|---------|------|
| .lottie | ✅ Yes | public/animations/ | အငယ်ဆုံး |
| .json | ✅ Yes | public/animations/ | အလယ်လယ် |
| .gif | ❌ No | - | အကြီးဆုံး |

**အကြံပြုချက်**: **.lottie** format ကို သုံးပါ။ အမြန် load ဖြစ်တယ်။
