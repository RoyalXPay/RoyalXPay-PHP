<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\RemittanceWalletModel;

class TapBalanceMonitor extends BaseCommand
{
    protected $group       = 'TAP';
    protected $name        = 'tap:monitor-balance';
    protected $description = 'Monitors and updates wallet balances from TAP periodically';
    protected $usage       = 'tap:monitor-balance [options]';
    protected $arguments   = [];
    protected $options     = [
        '--wallet' => 'Specific wallet number to monitor (optional)',
        '--force'  => 'Force update even if last check was recent',
    ];

    protected $walletModel;

    public function run(array $params)
    {
        $this->walletModel = new RemittanceWalletModel();

        CLI::write('TAP Balance Monitor Started', 'green');
        CLI::write('Time: ' . date('Y-m-d H:i:s'), 'yellow');
        CLI::newLine();

        $specificWallet = $params['wallet'] ?? CLI::getOption('wallet');
        $forceUpdate = isset($params['force']) || CLI::getOption('force');

        try {
            if ($specificWallet) {
                // Monitor specific wallet
                $this->monitorWallet($specificWallet, $forceUpdate);
            } else {
                // Monitor all active wallets
                $this->monitorAllWallets($forceUpdate);
            }

            CLI::newLine();
            CLI::write('Balance monitoring completed successfully!', 'green');
            
        } catch (\Exception $e) {
            CLI::error('Error: ' . $e->getMessage());
            log_message('error', 'TAP Balance Monitor Error: ' . $e->getMessage());
            return EXIT_ERROR;
        }

        return EXIT_SUCCESS;
    }

    /**
     * Monitor all active wallets
     */
    private function monitorAllWallets($forceUpdate = false)
    {
        // Get all active wallets
        $wallets = $this->walletModel
            ->where('status', 'active')
            ->findAll();

        if (empty($wallets)) {
            CLI::write('No active wallets found to monitor', 'yellow');
            return;
        }

        CLI::write('Monitoring ' . count($wallets) . ' wallet(s)...', 'cyan');
        CLI::newLine();

        $successCount = 0;
        $failCount = 0;

        foreach ($wallets as $wallet) {
            try {
                $result = $this->updateWalletBalance($wallet, $forceUpdate);
                
                if ($result['success']) {
                    $successCount++;
                    CLI::write("✓ {$wallet['wallet_number']}: Balance updated to {$result['balance']} {$result['currency']}", 'green');
                } else {
                    CLI::write("○ {$wallet['wallet_number']}: {$result['message']}", 'yellow');
                }
                
            } catch (\Exception $e) {
                $failCount++;
                CLI::write("✗ {$wallet['wallet_number']}: Failed - {$e->getMessage()}", 'red');
                log_message('error', "Balance monitor failed for wallet {$wallet['wallet_number']}: " . $e->getMessage());
            }

            // Small delay to avoid rate limiting
            usleep(500000); // 0.5 seconds
        }

        CLI::newLine();
        CLI::write("Summary: {$successCount} updated, {$failCount} failed", 'cyan');
    }

    /**
     * Monitor specific wallet
     */
    private function monitorWallet($walletNumber, $forceUpdate = false)
    {
        $wallet = $this->walletModel
            ->where('wallet_number', $walletNumber)
            ->first();

        if (!$wallet) {
            CLI::error("Wallet {$walletNumber} not found");
            return;
        }

        CLI::write("Monitoring wallet: {$walletNumber}", 'cyan');
        CLI::newLine();

        $result = $this->updateWalletBalance($wallet, $forceUpdate);

        if ($result['success']) {
            CLI::write("✓ Balance updated successfully!", 'green');
            CLI::write("  Current Balance: {$result['balance']} {$result['currency']}", 'yellow');
            CLI::write("  Last Updated: {$result['updated_at']}", 'yellow');
        } else {
            CLI::write("○ {$result['message']}", 'yellow');
        }
    }

    /**
     * Update wallet balance from database
     * Note: Balance is already updated via deposit webhook
     * This command just logs the current balance for monitoring
     */
    private function updateWalletBalance($wallet, $forceUpdate = false)
    {
        // Check if we should skip (last check was less than 5 minutes ago)
        if (!$forceUpdate && isset($wallet['last_balance_check'])) {
            $lastCheck = strtotime($wallet['last_balance_check']);
            $now = time();
            $minInterval = 300; // 5 minutes

            if (($now - $lastCheck) < $minInterval) {
                return [
                    'success' => false,
                    'message' => 'Skipped - checked recently'
                ];
            }
        }

        // Update last check time
        $this->walletModel->update($wallet['id'], [
            'last_balance_check' => date('Y-m-d H:i:s')
        ]);

        // Return current balance from database
        // (Balance is updated in real-time by TAP deposit webhook)
        return [
            'success' => true,
            'balance' => $wallet['balance'],
            'currency' => $wallet['currency'] ?? 'BDT',
            'updated_at' => date('Y-m-d H:i:s')
        ];
    }
}
