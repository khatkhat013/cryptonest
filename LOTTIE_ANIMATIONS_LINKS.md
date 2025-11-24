# Lottie Animations - CDN Links

## Script Libraries ကို အရင် Load လုပ်ပါ

### DotLottie Web Component Library
```html
<!-- Main Script (unpkg.com မှ) -->
<script type="module" src="https://unpkg.com/@lottiefiles/dotlottie-wc@0.8.5/dist/dotlottie-wc.js"></script>

<!-- သို့မဟုတ် jsDelivr မှ -->
<script type="module" src="https://cdn.jsdelivr.net/npm/@lottiefiles/dotlottie-wc@0.8.5/dist/dotlottie-wc.js"></script>
```

## Animation Files - လုံးလုံး 4 ခု

### 1. Header Hero Animation (Landing Page)
```
Name: MJoyjsN8w3.lottie
URL: https://lottie.host/b8c3e07f-6857-4919-95d6-0dee9e6127ce/MJoyjsN8w3.lottie
Description: Header အတွက် Main ကမ္ဘာ့ animation
```

### 2. AI Arbitrage Card Animation
```
Name: EaLJaJluzA.lottie
URL: https://lottie.host/4c89fb6d-25b1-4505-8149-a669c9c57e3d/EaLJaJluzA.lottie
Description: AI Arbitrage အတွက် animation
```

### 3. Mining Card Animation
```
Name: RNsMUkyjxt.lottie
URL: https://lottie.host/3b3a89d1-eaa6-4fb6-9118-1d5804faaf5a/RNsMUkyjxt.lottie
Description: Mining အတွက် animation
```

### 4. Invite Friends Card Animation
```
Name: I9uAcddYg9.lottie
URL: https://lottie.host/bb1ba882-5cec-4676-add1-d008f39ae2ee/I9uAcddYg9.lottie
Description: Invite Friends အတွက် animation
```

## HTML Usage Example

```html
<!-- Script ကို Head တွင် ထည့်ပါ -->
<script type="module" src="https://unpkg.com/@lottiefiles/dotlottie-wc@0.8.5/dist/dotlottie-wc.js"></script>

<!-- Animation ကို Body တွင် သုံးပါ -->
<dotlottie-wc 
    src="https://lottie.host/b8c3e07f-6857-4919-95d6-0dee9e6127ce/MJoyjsN8w3.lottie" 
    style="width: 100%; height: auto" 
    autoplay 
    loop>
</dotlottie-wc>
```

## Download အတွက် Alternative Methods

### Method 1: wget သုံးပြီး Download မပ (Windows - Git Bash လိုအပ်)
```bash
wget https://lottie.host/b8c3e07f-6857-4919-95d6-0dee9e6127ce/MJoyjsN8w3.lottie
wget https://lottie.host/4c89fb6d-25b1-4505-8149-a669c9c57e3d/EaLJaJluzA.lottie
wget https://lottie.host/3b3a89d1-eaa6-4fb6-9118-1d5804faaf5a/RNsMUkyjxt.lottie
wget https://lottie.host/bb1ba882-5cec-4676-add1-d008f39ae2ee/I9uAcddYg9.lottie
```

### Method 2: PowerShell သုံးပြီး Download မပ
```powershell
# Animation 1
$url = "https://lottie.host/b8c3e07f-6857-4919-95d6-0dee9e6127ce/MJoyjsN8w3.lottie"
$outFile = "$PSScriptRoot\public\animations\MJoyjsN8w3.lottie"
Invoke-WebRequest -Uri $url -OutFile $outFile

# Animation 2
$url = "https://lottie.host/4c89fb6d-25b1-4505-8149-a669c9c57e3d/EaLJaJluzA.lottie"
$outFile = "$PSScriptRoot\public\animations\EaLJaJluzA.lottie"
Invoke-WebRequest -Uri $url -OutFile $outFile

# Animation 3
$url = "https://lottie.host/3b3a89d1-eaa6-4fb6-9118-1d5804faaf5a/RNsMUkyjxt.lottie"
$outFile = "$PSScriptRoot\public\animations\RNsMUkyjxt.lottie"
Invoke-WebRequest -Uri $url -OutFile $outFile

# Animation 4
$url = "https://lottie.host/bb1ba882-5cec-4676-add1-d008f39ae2ee/I9uAcddYg9.lottie"
$outFile = "$PSScriptRoot\public\animations\I9uAcddYg9.lottie"
Invoke-WebRequest -Uri $url -OutFile $outFile
```

## Local အသုံးပြုခြင်း (Local ဖိုင်များ)

ဖိုင်များကို `public/animations/` ဖိုင်စုပ်ထဲ ထည့်ပါ။ ထို့ နောက် HTML မှာ:

```html
<dotlottie-wc 
    src="{{ asset('animations/MJoyjsN8w3.lottie') }}" 
    style="width: 100%; height: auto" 
    autoplay 
    loop>
</dotlottie-wc>
```

## CSP နှင့် လုံခြုံရေး Settings

အောက်ပါတွေကို CSP မှာ ခွင့်ပြုထားပြီးပါပြီ:
- `connect-src`: cdn.jsdelivr.net, unpkg.com
- `script-src`: unpkg.com
- `worker-src`: blob: (WASM လုပ်ဆောင်ခြင်းအတွက်)
