<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Minishlink\WebPush\VAPID;

class GenerateVapidKeys extends Command
{
    protected $signature   = 'subsift:generate-vapid-keys';
    protected $description = 'Generate VAPID public/private key pair for Web Push';

    public function handle(): int
    {
        try {
            $keys = VAPID::createVapidKeys();
        } catch (\Throwable $e) {
            $this->error('Failed to generate VAPID keys: ' . $e->getMessage());
            $this->line('Ensure your PHP OpenSSL extension is correctly configured (OPENSSL_CONF may need to be set).');
            return self::FAILURE;
        }

        $this->info('Add these to your .env file:');
        $this->line('');
        $this->line('VAPID_PUBLIC_KEY=' . $keys['publicKey']);
        $this->line('VAPID_PRIVATE_KEY=' . $keys['privateKey']);
        $this->line('VAPID_SUBJECT=mailto:admin@subsift.app');

        return self::SUCCESS;
    }
}
