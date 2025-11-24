# Download Missing Lottie Animation Files

## PowerShell Script - Download လုပ်ပါ

ဒီအောက်ပါ PowerShell command ကို run ပါ။ ဖိုင်တွေကို `public/` directory ထဲ download လုပ်ပါ့မယ်။

```powershell
# Set working directory
$projectPath = "C:\Users\Black Coder\OneDrive\Desktop\crypto-nest\cryptonest backup"
$publicPath = "$projectPath\public"

# Animation URLs - Lottie Host မှ
$animations = @(
    @{
        Name = "EaLJaJluzA.lottie"
        Url = "https://lottie.host/4c89fb6d-25b1-4505-8149-a669c9c57e3d/EaLJaJluzA.lottie"
    },
    @{
        Name = "RNsMUkyjxt.lottie"
        Url = "https://lottie.host/3b3a89d1-eaa6-4fb6-9118-1d5804faaf5a/RNsMUkyjxt.lottie"
    },
    @{
        Name = "I9uAcddYg9.lottie"
        Url = "https://lottie.host/bb1ba882-5cec-4676-add1-d008f39ae2ee/I9uAcddYg9.lottie"
    }
)

# Download each animation
foreach ($animation in $animations) {
    $outputPath = "$publicPath\$($animation.Name)"
    Write-Host "Downloading: $($animation.Name)..."
    
    try {
        Invoke-WebRequest -Uri $animation.Url -OutFile $outputPath -UseBasicParsing
        Write-Host "✅ Success: $($animation.Name)" -ForegroundColor Green
    } catch {
        Write-Host "❌ Failed: $($animation.Name)" -ForegroundColor Red
        Write-Host "Error: $_" -ForegroundColor Red
    }
}

Write-Host "`nDownload Complete!" -ForegroundColor Cyan
```

## အလျင်မြန်ဆုံး - One-liner Command

```powershell
$p="C:\Users\Black Coder\OneDrive\Desktop\crypto-nest\cryptonest backup\public"; 
Invoke-WebRequest -Uri "https://lottie.host/4c89fb6d-25b1-4505-8149-a669c9c57e3d/EaLJaJluzA.lottie" -OutFile "$p\EaLJaJluzA.lottie" -UseBasicParsing;
Invoke-WebRequest -Uri "https://lottie.host/3b3a89d1-eaa6-4fb6-9118-1d5804faaf5a/RNsMUkyjxt.lottie" -OutFile "$p\RNsMUkyjxt.lottie" -UseBasicParsing;
Invoke-WebRequest -Uri "https://lottie.host/bb1ba882-5cec-4676-add1-d008f39ae2ee/I9uAcddYg9.lottie" -OutFile "$p\I9uAcddYg9.lottie" -UseBasicParsing;
Write-Host "Done!" -ForegroundColor Green
```

## အက်ခ်တ် ကျေးဇူးလုပ်ပါ - Manual Download

အကယ်၍ PowerShell မဖြည့စွဲမည်ဆိုလျှင် browser မှ ဒေါင်းလုပ်နိုင်ပါတယ်:

1. https://lottie.host/4c89fb6d-25b1-4505-8149-a669c9c57e3d/EaLJaJluzA.lottie
   - Save as: `EaLJaJluzA.lottie` → `public/` folder

2. https://lottie.host/3b3a89d1-eaa6-4fb6-9118-1d5804faaf5a/RNsMUkyjxt.lottie
   - Save as: `RNsMUkyjxt.lottie` → `public/` folder

3. https://lottie.host/bb1ba882-5cec-4676-add1-d008f39ae2ee/I9uAcddYg9.lottie
   - Save as: `I9uAcddYg9.lottie` → `public/` folder

## အပြီးသတ်

ဖိုင်တွေ အားလုံး `public/` directory မှာ ထည့်ပြီးတဲ့ နောက်:

```
public/
├── MJoyjsN8w3.lottie          ✅ (ပြီးဆံပြီး)
├── EaLJaJluzA.lottie          (ထည့်ရန်လိုအပ်)
├── RNsMUkyjxt.lottie          (ထည့်ရန်လိုအပ်)
└── I9uAcddYg9.lottie          (ထည့်ရန်လိုအပ်)
```

Page ကို refresh ပြီး animations အားလုံး အလုပ်လုပ်ပါ့မယ়။ ✨
