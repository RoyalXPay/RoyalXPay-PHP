<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * TAP Balance Monitor Configuration
 * 
 * This configuration defines how often to check TAP for balance updates
 */
class TapMonitor extends BaseConfig
{
    /**
     * Enable/Disable automatic balance monitoring
     */
    public bool $enabled = true;

    /**
     * Monitoring interval in seconds
     * Default: 300 seconds (5 minutes)
     */
    public int $checkInterval = 300;

    /**
     * Minimum time between checks for same wallet (seconds)
     * Prevents too frequent checks
     * Default: 300 seconds (5 minutes)
     */
    public int $minCheckInterval = 300;

    /**
     * TAP API timeout in seconds
     */
    public int $apiTimeout = 30;

    /**
     * Retry failed balance checks
     */
    public bool $retryOnFailure = true;

    /**
     * Maximum retry attempts for failed checks
     */
    public int $maxRetries = 3;

    /**
     * Delay between retries (seconds)
     */
    public int $retryDelay = 60;

    /**
     * Log all balance changes
     */
    public bool $logBalanceChanges = true;

    /**
     * Notify on significant balance changes
     * Set threshold amount (0 to disable notifications)
     */
    public float $notifyThreshold = 100.00;

    /**
     * Only monitor wallets with status = 'active'
     */
    public bool $onlyActiveWallets = true;

    /**
     * Batch size for monitoring multiple wallets
     * Process this many wallets at once
     */
    public int $batchSize = 50;

    /**
     * Delay between processing each wallet (microseconds)
     * To avoid rate limiting: 500000 = 0.5 seconds
     */
    public int $walletProcessDelay = 500000;
}
