<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ActionStatus;
use Illuminate\Support\Facades\DB;

class ActionStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = ['pending', 'cancel', 'reject', 'frozen', 'complete'];

        foreach ($statuses as $name) {
            ActionStatus::firstOrCreate(['name' => $name]);
        }
    }
}

class ActionStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rows = [
            ['name' => 'pending', 'description' => 'ငွေလွှဲပြောင်းမှုကို စစ်ဆေးဆဲ အခြေအနေ။', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'complete', 'description' => 'ငွေလွှဲပြောင်းမှု အောင်မြင်ပြီး User wallet ထဲသို့ ထည့်သွင်းပြီး အခြေအနေ။', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'reject', 'description' => 'ငွေလွှဲပြောင်းမှုကို Admin က ပယ်ချလိုက်သော အခြေအနေ။', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'cancel', 'description' => 'User သို့မဟုတ် Admin က ငွေလွှဲပြောင်းမှုကို ဖျက်သိမ်းလိုက်သော အခြေအနေ။', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'frozen', 'description' => 'အကောင့် သို့မဟုတ် လွှဲပြောင်းမှုကို ခေတ္တထိန်းချုပ်ထားသော အခြေအနေ။ (deposit/withdraw အတွက် လိုအပ်မှ ထည့်ပါ)', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'closed', 'description' => 'လုပ်ငန်းစဉ်ကို အပြီးသတ် ပိတ်လိုက်သော အခြေအနေ။ (deposit/withdraw အတွက် လိုအပ်မှ ထည့်ပါ)', 'created_at' => now(), 'updated_at' => now()],
        ];

        foreach ($rows as $r) {
            DB::table('action_statuses')->updateOrInsert(['name' => $r['name']], $r);
        }
    }
}
