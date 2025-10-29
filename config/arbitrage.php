<?php

return [
    'plans' => [
        // max_times controls how many concurrent/active starts a single user may have for this plan.
        'A' => ['min' => 500, 'max' => 2000, 'pct_min' => 1.60, 'pct_max' => 1.70, 'duration' => '1 Day', 'quantity_label' => '$500-2000', 'revenue_label' => '1.60-1.70%', 'max_times' => 2],
        'B' => ['min' => 2001, 'max' => 10000, 'pct_min' => 1.90, 'pct_max' => 2.10, 'duration' => '3 Day', 'quantity_label' => '$2001-10000', 'revenue_label' => '1.90-2.10%', 'max_times' => 2],
        'C' => ['min' => 10001, 'max' => 50000, 'pct_min' => 2.45, 'pct_max' => 2.45, 'duration' => '3 Days', 'quantity_label' => '$10001-50000', 'revenue_label' => '2.20-2.70%', 'max_times' => 2],
        'D' => ['min' => 50001, 'max' => 200000, 'pct_min' => 3.05, 'pct_max' => 3.05, 'duration' => '7 Days', 'quantity_label' => '$50001-200000', 'revenue_label' => '2.80-3.30%', 'max_times' => 3],
        'E' => ['min' => 200001, 'max' => 500000, 'pct_min' => 4.50, 'pct_max' => 4.50, 'duration' => '10 Days', 'quantity_label' => '$200001-500000', 'revenue_label' => '3.50-5.50%', 'max_times' => 5],
        'VIP' => ['min' => 500001, 'max' => 3000000, 'pct_min' => 7.00, 'pct_max' => 7.00, 'duration' => '15 Days', 'quantity_label' => '$500001-3000000', 'revenue_label' => '5.50-8.50%', 'max_times' => 7],
        'CN' => ['min' => 3000001, 'max' => 10000000, 'pct_min' => 8.25, 'pct_max' => 8.25, 'duration' => '20 Days', 'quantity_label' => '$3000001-10000000', 'revenue_label' => '6.50-10.00%', 'max_times' => 9],
    ],
];
