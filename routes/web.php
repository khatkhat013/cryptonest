<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;

// Public routes - accessible without login
Route::view('/', 'home');

// Lightweight public price endpoint used by client UI to avoid exposing direct external API calls
Route::get('/prices', [App\Http\Controllers\PriceController::class, 'prices'])->name('prices');

// Trade routes
Route::get('/api/trade/{orderId}/price', [App\Http\Controllers\TradeController::class, 'getTradePrice']);

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
    // Redirect '/admin' to the appropriate admin route depending on auth state
    Route::get('/', function () {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('admin.login');
    });
    // Public admin routes
    // Apply guest:admin so already-authenticated admins are redirected to dashboard
    // Admin login/register GET routes use closures to explicitly redirect authenticated admins
    Route::get('/login', function () {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }
        return app(\App\Http\Controllers\Admin\AuthController::class)->showLoginForm();
    })->name('login');

    Route::post('/login', [AdminAuthController::class, 'login']);

    Route::get('/register', function () {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }
        return app(\App\Http\Controllers\Admin\AuthController::class)->showRegisterForm();
    })->name('register');

    Route::post('/register', [AdminAuthController::class, 'register']);
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
    
    // Dashboard: ensure unauthenticated visitors are redirected to admin login.
    // If authenticated, call the DashboardController@index.
    Route::get('/dashboard', function () {
        if (!Auth::guard('admin')->check()) {
            return redirect()->route('admin.login');
        }
        // Resolve controller from container and call the index method
        return app(\App\Http\Controllers\Admin\DashboardController::class)->index();
    })->name('dashboard');

    // Protected admin routes (other pages)
    Route::middleware('auth:admin')->group(function () {
        // User Management Routes
        Route::controller(App\Http\Controllers\Admin\UserController::class)->prefix('users')->name('users.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/{user}', 'show')->name('show');
            Route::post('/{user}/toggle-status', 'toggleStatus')->name('toggle-status');
            Route::post('/{user}/toggle-force-loss', 'toggleForceLoss')->name('toggle-force-loss');
        });
        // Assign user to admin (super admin only) - handled by UsersManagementController
        Route::post('/users/{user}/assign', [App\Http\Controllers\Admin\UsersManagementController::class, 'assign'])->name('users.assign');
        
    Route::get('/profile', [App\Http\Controllers\Admin\ProfileController::class, 'index'])->name('profile');
    Route::post('/profile', [App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');
        
        // Deposits Management
        Route::get('/deposits', [App\Http\Controllers\Admin\DepositAdminController::class, 'index'])->name('deposits.index');
        Route::post('/deposits/{deposit}/status', [App\Http\Controllers\Admin\DepositAdminController::class, 'updateStatus'])->name('deposits.update-status');
    // Allow deleting a deposit (only allowed for super admins or assigned admin and only if not credited)
    Route::delete('/deposits/{deposit}', [App\Http\Controllers\Admin\DepositAdminController::class, 'destroy'])->name('deposits.destroy');
        
        // Withdrawals Management
        Route::get('/withdraws', [App\Http\Controllers\Admin\WithdrawalAdminController::class, 'index'])->name('withdraws.index');
        Route::post('/withdraws/{withdrawal}/status', [App\Http\Controllers\Admin\WithdrawalAdminController::class, 'updateStatus'])->name('withdraws.update-status');
    Route::delete('/withdraws/{withdrawal}', [App\Http\Controllers\Admin\WithdrawalAdminController::class, 'destroy'])->name('withdraws.destroy');

        // Admin placeholders for Trading and AI Arbitrage pages (pages to be implemented later)
    Route::get('/trading', [App\Http\Controllers\Admin\TradeOrderAdminController::class, 'index'])->name('trading.index');
    Route::get('/trading/{trade}/edit', [App\Http\Controllers\Admin\TradeOrderAdminController::class, 'edit'])->name('trading.edit');
    Route::post('/trading/{trade}/update', [App\Http\Controllers\Admin\TradeOrderAdminController::class, 'update'])->name('trading.update');
    Route::delete('/trading/{trade}', [App\Http\Controllers\Admin\TradeOrderAdminController::class, 'destroy'])->name('trading.destroy');

        Route::get('/ai-arbitrage', function() {
            // Admin AI arbitrage list (mirror trading admin list) and restrict to assigned users
            if (!\Illuminate\Support\Facades\Schema::hasTable('ai_arbitrage_plans')) {
                return redirect()->route('admin.deposits.index');
            }

            $plansQuery = \Illuminate\Support\Facades\DB::table('ai_arbitrage_plans as p')
                ->leftJoin('users as u', 'p.user_id', '=', 'u.id')
                ->select('p.*', \Illuminate\Support\Facades\DB::raw("COALESCE(u.name, u.email) as user_name"))
                ->orderBy('p.id', 'desc');

            // Restrict non-super-admins to only see plans for users assigned to them
            $admin = \Illuminate\Support\Facades\Auth::guard('admin')->user();
            if ($admin && method_exists($admin, 'isSuperAdmin') && !$admin->isSuperAdmin()) {
                // Only include plans where the user exists and their assigned_admin_id matches current admin
                $plansQuery->where('u.assigned_admin_id', $admin->id);
            }

            $plans = $plansQuery->paginate(20);
            return view('admin.ai_arbitrage', compact('plans'));
        })->name('ai.arbitrage.index');

        // JSON endpoint for a single plan (used by admin UI to fetch details)
        Route::get('/ai-arbitrage/{id}/json', function ($id) {
            if (!\Illuminate\Support\Facades\Schema::hasTable('ai_arbitrage_plans')) {
                return response()->json(['success' => false, 'message' => 'ai_arbitrage_plans table not found'], 404);
            }

            $row = \Illuminate\Support\Facades\DB::table('ai_arbitrage_plans as p')
                ->leftJoin('users as u', 'p.user_id', '=', 'u.id')
                ->select('p.*', \Illuminate\Support\Facades\DB::raw("COALESCE(u.name, u.email) as user_name"))
                ->where('p.id', intval($id))
                ->first();

            if (!$row) {
                return response()->json(['success' => false, 'message' => 'Plan not found'], 404);
            }

            return response()->json(['success' => true, 'plan' => $row]);
        })->name('ai.arbitrage.json');

        // Admin edit form for a plan
        Route::get('/ai-arbitrage/{id}/edit', function ($id) {
            if (!\Illuminate\Support\Facades\Schema::hasTable('ai_arbitrage_plans')) {
                return redirect()->route('admin.deposits.index');
            }
            $plan = \Illuminate\Support\Facades\DB::table('ai_arbitrage_plans as p')
                ->leftJoin('users as u', 'p.user_id', '=', 'u.id')
                ->select('p.*', \Illuminate\Support\Facades\DB::raw("COALESCE(u.name, u.email) as user_name"))
                ->where('p.id', intval($id))
                ->first();

            if (!$plan) return redirect()->route('admin.ai.arbitrage.index')->with('error', 'Plan not found');

            return view('admin.ai_arbitrage_edit', ['plan' => $plan]);
        })->name('ai.arbitrage.edit');

        // Update handler
        Route::post('/ai-arbitrage/{id}/update', function (\Illuminate\Http\Request $request, $id) {
            if (!\Illuminate\Support\Facades\Schema::hasTable('ai_arbitrage_plans')) {
                return redirect()->route('admin.deposits.index');
            }

            $data = [];

            // Determine actual columns present in the table and map incoming inputs to them.
            $has = function($col) {
                return \Illuminate\Support\Facades\Schema::hasColumn('ai_arbitrage_plans', $col);
            };

            // Map amount -> amount or quantity
            if ($request->filled('amount')) {
                if ($has('amount')) {
                    $data['amount'] = $request->input('amount');
                } elseif ($has('quantity')) {
                    $data['quantity'] = $request->input('amount');
                }
            }

            // Map profit_rate -> profit_rate or daily_revenue_percentage or profit
            if ($request->filled('profit_rate')) {
                if ($has('profit_rate')) {
                    $data['profit_rate'] = $request->input('profit_rate');
                } elseif ($has('daily_revenue_percentage')) {
                    $data['daily_revenue_percentage'] = $request->input('profit_rate');
                } elseif ($has('profit')) {
                    $data['profit'] = $request->input('profit_rate');
                }
            }

            // Status
            if ($request->filled('status')) {
                if ($has('status')) $data['status'] = $request->input('status');
            }

            // Duration: duration_hours preferred; fall back to duration_days if needed
            if ($request->filled('duration_hours')) {
                $hours = intval($request->input('duration_hours'));
                if ($has('duration_hours')) {
                    $data['duration_hours'] = $hours;
                } elseif ($has('duration_days')) {
                    // convert hours to days (round up to nearest whole day)
                    $days = max(1, (int) ceil($hours / 24));
                    $data['duration_days'] = $days;
                }
            } elseif ($request->filled('duration_days')) {
                if ($has('duration_days')) $data['duration_days'] = intval($request->input('duration_days'));
            }

            // started_at and completed_at (if present)
            foreach (['started_at','completed_at'] as $tcol) {
                if ($request->filled($tcol) && $has($tcol)) {
                    // ensure we convert HTML5 datetime-local format to SQL friendly (replace T with space)
                    $val = $request->input($tcol);
                    $val = str_replace('T', ' ', $val);
                    $data[$tcol] = $val;
                }
            }

            if (count($data)) {
                \Illuminate\Support\Facades\DB::table('ai_arbitrage_plans')->where('id', intval($id))->update($data);
            }

            return redirect()->route('admin.ai.arbitrage.index')->with('success', 'Plan updated');
        })->name('ai.arbitrage.update');

        // Delete handler
        Route::delete('/ai-arbitrage/{id}', function ($id) {
            if (!\Illuminate\Support\Facades\Schema::hasTable('ai_arbitrage_plans')) {
                return redirect()->route('admin.deposits.index');
            }
            \Illuminate\Support\Facades\DB::table('ai_arbitrage_plans')->where('id', intval($id))->delete();
            return redirect()->route('admin.ai.arbitrage.index')->with('success', 'Plan deleted');
        })->name('ai.arbitrage.delete');
        
        // Admin Management Routes
        Route::resource('admins', App\Http\Controllers\Admin\AdminController::class);
    });
});

Route::view('/support', 'support');
Route::view('/knowledge', 'trade');
Route::view('/knowledge/faq', 'knowledge.faq');
Route::view('/knowledge/whitepaper', 'knowledge.whitepaper');
Route::view('/knowledge/regulatory', 'knowledge.regulatory');
Route::view('/arbitrage/introduction', 'arbitrage.introduction')->name('introduction');

// Authentication routes
// Apply 'guest' middleware so authenticated regular users are redirected away from login/register
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->name('login.post')->middleware('guest');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register')->middleware('guest');
Route::post('/register', [AuthController::class, 'register'])->name('register.post')->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Protected routes - require authentication
Route::middleware(['auth'])->group(function () {
    // Main pages
    Route::view('/wallets', 'wallets');
    Route::view('/trade', 'trade');
    Route::view('/lending', 'lending');
    Route::post('/lending/submit', [App\Http\Controllers\LendingController::class, 'store'])->name('lending.store');
    Route::get('/lending/list', [App\Http\Controllers\LendingController::class, 'index'])->name('lending.list');
    
    // Trade API routes
    Route::post('/api/trade/simulate-price', [App\Http\Controllers\TradeController::class, 'simulatePrice']);
    Route::post('/api/trade/{orderId}/complete', [App\Http\Controllers\TradeController::class, 'complete']);
    Route::get('/trade/orders', [App\Http\Controllers\TradeController::class, 'orders'])->name('trade.orders');
    Route::view('/lending-history', 'lending-history');
    Route::get('/financial/record', [App\Http\Controllers\FinancialRecordController::class, 'index'])->name('financial.record');
    
    // Wallet functionality
    Route::view('/wallet/wire-transfer', 'wallet.wire-transfer')->name('wallet.wire-transfer');
    // API: return the authenticated user's balance for a coin
    Route::get('/api/wallet/balance/{coin}', [App\Http\Controllers\Api\WalletApiController::class, 'balance']);
    Route::get('/wallet/{type}', function($type) {
        $validTypes = ['btc', 'eth', 'usdt', 'usdc', 'pyusd', 'doge', 'xrp'];
        if (!in_array(strtolower($type), $validTypes)) {
            abort(404);
        }

        // Default: no address
        $address = null;

    // Current authenticated user (route is protected by 'auth' middleware)
    $user = Auth::user();
        if ($user) {
            // Build ordered candidates: 1) assigned admin, 2) super-admins (role_id = 2), 3) regular admins (role_id = 1)
            $candidates = collect();

            if ($user->assignedAdmin) {
                $candidates->push($user->assignedAdmin);
            }

            $superAdmins = \App\Models\Admin::where('role_id', 2)->get();
            foreach ($superAdmins as $sa) { $candidates->push($sa); }

            $regularAdmins = \App\Models\Admin::where('role_id', 1)->get();
            foreach ($regularAdmins as $ra) { $candidates->push($ra); }

            // Normalize the target symbol for matching
            $targetSymbol = strtoupper($type);

            // Find the first candidate admin that has a wallet for this currency
            foreach ($candidates as $admin) {
                if (!$admin) continue;
                $wallet = \App\Models\AdminWallet::where('admin_id', $admin->id)
                    ->whereHas('currency', function($q) use ($targetSymbol) {
                        $q->whereRaw('UPPER(symbol) = ?', [$targetSymbol]);
                    })->first();

                if ($wallet) {
                    $address = $wallet->address;
                    break;
                }
            }
        }

        // Also compute current user's wallet balance server-side so the page
        // can show an initial value even if client-side fetch fails.
        $initialBalance = 0;
        if ($user) {
            $wallet = \App\Models\UserWallet::where('user_id', $user->id)
                ->whereRaw('UPPER(coin) = ?', [strtoupper($type)])->first();
            if ($wallet) {
                $initialBalance = $wallet->balance;
            }
        }

        return view('wallet.detail', [
            'type' => strtolower($type),
            'address' => $address,
            'initialBalance' => $initialBalance,
        ]);
    })->name('wallet.detail');

    // Deposit (Top-up) endpoint - handle image + amount
    Route::post('/wallet/deposit', [App\Http\Controllers\DepositController::class, 'store'])->name('wallet.deposit');

    // Withdrawal (Send) endpoint - handle user send requests
    Route::post('/wallet/withdraw', [App\Http\Controllers\WithdrawalController::class, 'store'])->name('wallet.withdraw');

    // Conversion endpoint
    Route::post('/wallet/convert', [App\Http\Controllers\ConversionController::class, 'store'])->name('wallet.convert');

    // Arbitrage section
    Route::get('/arbitrage', function () {
        $user = Auth::user();
        $totalEarned = 0;
        if ($user && \Illuminate\Support\Facades\Schema::hasTable('ai_arbitrage_plans')) {
            // Only include profit from completed plans (exclude active / in-progress incremental profits)
            $totalEarned = \Illuminate\Support\Facades\DB::table('ai_arbitrage_plans')
                ->where('user_id', $user->id)
                ->where('status', 'completed')
                ->whereNotNull('profit')
                ->sum('profit');
        }

        return view('arbitrage', ['totalEarned' => $totalEarned]);
    });
    Route::view('/arbitrage/history', 'arbitrage.history');
    // Store arbitrage plan (simple closure) — requires auth
    Route::post('/arbitrage', function (\Illuminate\Http\Request $request) {
        // Expect plan_name and quantity; duration_days optional
        $plan = strtoupper(trim($request->input('plan_name', 'A')));
        $quantity = $request->input('quantity');
        $duration = $request->input('duration_days', 1);

        // Load plan definitions from config to avoid duplication.
        $plans = config('arbitrage.plans', []);
        if (!isset($plans[$plan])) {
            return response()->json(['success' => false, 'message' => 'Unknown plan'], 422);
        }
        $cfg = $plans[$plan];

        // validate quantity is numeric and within plan bounds
        if (!is_numeric($quantity)) {
            return response()->json(['success' => false, 'message' => 'Quantity must be numeric'], 422);
        }

        $quantity = floatval($quantity);
        if ($quantity < $cfg['min'] || $quantity > $cfg['max']) {
            return response()->json(['success' => false, 'message' => "Amount for plan {$plan} must be between {$cfg['min']} and {$cfg['max']} USDT"], 422);
        }

        $user = $request->user();

        // ensure ai_arbitrage_plans table exists
        if (!\Illuminate\Support\Facades\Schema::hasTable('ai_arbitrage_plans')) {
            return response()->json(['success' => false, 'message' => 'ai_arbitrage_plans table does not exist. Run migrations.'], 500);
        }

        // ensure user_wallets table exists and the user has sufficient USDT balance
        if (!\Illuminate\Support\Facades\Schema::hasTable('user_wallets')) {
            return response()->json(['success' => false, 'message' => 'user_wallets table does not exist. Run migrations.'], 500);
        }

        $wallet = \App\Models\UserWallet::where('user_id', $user->id)->whereRaw('LOWER(coin) = ?', ['usdt'])->first();
        if (!$wallet) {
            return response()->json(['success' => false, 'message' => 'USDT wallet not found for user'], 422);
        }

        if ($wallet->balance < $quantity) {
            return response()->json(['success' => false, 'message' => 'Insufficient USDT balance'], 422);
        }

        // Enforce per-user concurrent start limit (max_times) for this plan.
        $maxTimes = isset($cfg['max_times']) ? intval($cfg['max_times']) : null;
        if ($maxTimes !== null) {
            // Count total starts for this plan by the user (do NOT allow more than max_times total starts)
            $activeCount = \Illuminate\Support\Facades\DB::table('ai_arbitrage_plans')
                ->where('user_id', $user->id)
                ->where('plan_name', $plan)
                // count any status (active, completed, etc.) as a start
                ->count();

            if ($activeCount >= $maxTimes) {
                return response()->json([
                    'success' => false,
                    'message' => "Plan {$plan} start limit reached ({$activeCount}/{$maxTimes}). Finish or wait for an active plan to complete, or select another plan."
                ], 422);
            }
        }

        // Deduct the custody amount and create the plan in a transaction to avoid races
        // capture the computed daily percentage so we can return it safely
        $createdDaily = null;
        try {
            \Illuminate\Support\Facades\DB::transaction(function() use ($user, $plan, $duration, $quantity, $cfg, $wallet, &$createdDaily) {
                // Reload wallet for update
                $w = \App\Models\UserWallet::where('id', $wallet->id)->lockForUpdate()->first();
                if (!$w || $w->balance < $quantity) {
                    throw new \Exception('Insufficient balance at time of plan creation');
                }
                $w->balance = $w->balance - $quantity;
                $w->save();

                // determine daily percentage for this specific quantity within plan bounds
                $pctMin = isset($cfg['pct_min']) ? floatval($cfg['pct_min']) : (isset($cfg['daily']) ? floatval($cfg['daily']) : 0);
                $pctMax = isset($cfg['pct_max']) ? floatval($cfg['pct_max']) : (isset($cfg['daily']) ? floatval($cfg['daily']) : 0);
                $minAmt = intval($cfg['min']);
                $maxAmt = intval($cfg['max']);

                if ($quantity <= $minAmt) {
                    $dailyPct = $pctMin;
                } elseif ($quantity >= $maxAmt) {
                    $dailyPct = $pctMax;
                } else {
                    // linear interpolation between pctMin and pctMax
                    $ratio = ($quantity - $minAmt) / max(1, ($maxAmt - $minAmt));
                    $dailyPct = $pctMin + $ratio * ($pctMax - $pctMin);
                }

                \Illuminate\Support\Facades\DB::table('ai_arbitrage_plans')->insert([
                    'user_id' => $user->id,
                    'plan_name' => $plan,
                    'duration_days' => intval($duration),
                    'quantity' => $quantity,
                    'daily_revenue_percentage' => round($dailyPct, 4),
                    'pct_min' => $pctMin,
                    'pct_max' => $pctMax,
                    'total_profit' => 0,
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                // store computed daily pct for caller
                $createdDaily = round($dailyPct, 4);
            });
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        // prefer the computed daily pct; fall back to cfg['daily'] if present
        $dailyOut = isset($createdDaily) && $createdDaily !== null ? $createdDaily : (isset($cfg['daily']) ? floatval($cfg['daily']) : null);

        return response()->json(['success' => true, 'message' => 'Plan created', 'plan' => $plan, 'daily' => $dailyOut], 201);
    })->name('arbitrage.store');
    Route::get('/arbitrage/custody-order', [App\Http\Controllers\ArbitrageController::class, 'custodyOrder'])->name('custody.order');
    Route::view('/arbitrage/ai-robots', 'arbitrage.ai-robots')->name('ai.robots');
    Route::view('/arbitrage/aplan', 'arbitrage.aplan')->name('arbitrage.aplan');

    // Trading functionality
    Route::get('/coin/{symbol}', function($symbol) {
        if (!in_array(strtolower($symbol), ['btc', 'eth', 'trx', 'xrp', 'doge'])) {
            abort(404);
        }
        return view('trade', ['symbol' => strtolower($symbol), 'type' => 'crypto']);
    });

    // Orders API
    Route::post('/orders', [App\Http\Controllers\TradeOrderController::class, 'store']);
    Route::post('/orders/{id}/finalize', [App\Http\Controllers\TradeOrderController::class, 'finalize']);

    Route::get('/forex/{symbol}', function($symbol) {
        if (!in_array(strtolower($symbol), ['gbp', 'eur', 'chf', 'cad', 'aud', 'jpy'])) {
            abort(404);
        }
        return view('trade', ['symbol' => strtolower($symbol), 'type' => 'forex']);
    });

    Route::get('/metal/{symbol}', function($symbol) {
        return view('trade', ['symbol' => $symbol, 'type' => 'metal']);
    })->where('symbol', 'xau|xag|xpt|xpd');

    // Other protected features
    Route::get('/transaction', [App\Http\Controllers\TransactionController::class, 'index'])->name('transaction.index');
    Route::view('/mining', 'mining');
    Route::view('/finance', 'trade');
});
