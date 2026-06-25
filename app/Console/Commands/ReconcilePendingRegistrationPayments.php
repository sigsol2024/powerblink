<?php

namespace App\Console\Commands;

use App\Models\RegistrationPayment;
use App\Services\PaystackService;
use App\Services\RegistrationPaymentCompletionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ReconcilePendingRegistrationPayments extends Command
{
    protected $signature = 'powerblink:reconcile-payments
                            {--minutes=15 : Pending payments older than this many minutes}
                            {--limit=50 : Maximum payments to process per run}';

    protected $description = 'Verify and complete stale pending registration Paystack payments';

    public function handle(
        PaystackService $paystack,
        RegistrationPaymentCompletionService $completion,
    ): int {
        $minutes = max(5, (int) $this->option('minutes'));
        $limit = max(1, (int) $this->option('limit'));
        $cutoff = now()->subMinutes($minutes);

        $payments = RegistrationPayment::query()
            ->where('status', 'pending')
            ->where('provider', 'paystack')
            ->where('created_at', '<=', $cutoff)
            ->orderBy('id')
            ->limit($limit)
            ->get();

        $completed = 0;
        $failed = 0;
        $skipped = 0;

        foreach ($payments as $payment) {
            try {
                $verified = $paystack->verify($payment->reference);
                $txStatus = (string) data_get($verified, 'data.status', '');

                if ($completion->complete($payment, $verified)) {
                    $completed++;

                    continue;
                }

                if ($txStatus === 'failed' || $txStatus === 'abandoned') {
                    $payment->update(['status' => 'failed']);
                    $failed++;
                } else {
                    $skipped++;
                }
            } catch (\Throwable $e) {
                Log::warning('Payment reconciliation failed', [
                    'payment_id' => $payment->id,
                    'reference' => $payment->reference,
                    'message' => $e->getMessage(),
                ]);
                $skipped++;
            }
        }

        $this->info("Reconciled: {$completed} completed, {$failed} failed, {$skipped} skipped/pending.");

        return self::SUCCESS;
    }
}
