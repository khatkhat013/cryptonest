<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class BackupDatabaseToTelegram extends Command
{
    protected $signature = 'backup:database-telegram
                            {--chat-id= : Override target Telegram chat/channel id}
                            {--keep= : Override retention days for local backup files}
                            {--dry-run : Create backup locally without sending to Telegram}';

    protected $description = 'Create a database backup and send it to Telegram as a document';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        if (!config('services.backup.enabled', true)) {
            $this->warn('Database backup is disabled (DB_BACKUP_ENABLED=false).');
            return self::SUCCESS;
        }

        $botToken = (string) (config('services.telegram.bot_token') ?: env('TELEGRAM_BOT_TOKEN'));
        $chatId = (string) ($this->option('chat-id')
            ?: config('services.backup.telegram_chat_id')
            ?: config('services.telegram.channel_id')
            ?: env('TELEGRAM_CHANNEL_ID'));

        if ($botToken === '' && !$dryRun) {
            $this->error('Missing TELEGRAM_BOT_TOKEN.');
            return self::FAILURE;
        }

        if ($chatId === '' && !$dryRun) {
            $this->error('Missing Telegram chat id. Set DB_BACKUP_TELEGRAM_CHAT_ID or TELEGRAM_CHANNEL_ID.');
            return self::FAILURE;
        }

        $backupDir = storage_path('app/backups/database');
        if (!is_dir($backupDir) && !mkdir($backupDir, 0755, true) && !is_dir($backupDir)) {
            $this->error('Cannot create backup directory: ' . $backupDir);
            return self::FAILURE;
        }

        $stamp = now()->format('Ymd_His');
        $appName = Str::slug((string) config('app.name', 'cryptonest'));
        $connectionName = (string) config('database.default');

        $this->info("Creating backup using connection [{$connectionName}]...");

        try {
            $archivePath = $this->createArchive($connectionName, $backupDir, $appName, $stamp);
            $sizeBytes = filesize($archivePath) ?: 0;

            $this->line('Backup file: ' . basename($archivePath));
            $this->line('Size: ' . $this->formatBytes($sizeBytes));

            if (!$dryRun) {
                $this->sendToTelegram($botToken, $chatId, $archivePath, $connectionName, $sizeBytes);
                $this->info('Backup sent to Telegram successfully.');
            } else {
                $this->warn('Dry-run mode: backup was NOT sent to Telegram.');
            }

            $retentionDays = (int) ($this->option('keep') ?: config('services.backup.retention_days', 14));
            $this->cleanupOldBackups($backupDir, max(1, $retentionDays));

            return self::SUCCESS;
        } catch (\Throwable $e) {
            Log::error('Database backup command failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $this->error('Backup failed: ' . $e->getMessage());
            return self::FAILURE;
        }
    }

    private function createArchive(string $connectionName, string $backupDir, string $appName, string $stamp): string
    {
        $driver = (string) config("database.connections.{$connectionName}.driver", '');

        if ($driver === 'mysql') {
            return $this->createMySqlArchive($connectionName, $backupDir, $appName, $stamp);
        }

        if ($driver === 'sqlite') {
            return $this->createSqliteArchive($connectionName, $backupDir, $appName, $stamp);
        }

        throw new \RuntimeException("Unsupported database driver for backup: {$driver}");
    }

    private function createMySqlArchive(string $connectionName, string $backupDir, string $appName, string $stamp): string
    {
        $host = (string) config("database.connections.{$connectionName}.host", '127.0.0.1');
        $port = (string) config("database.connections.{$connectionName}.port", '3306');
        $database = (string) config("database.connections.{$connectionName}.database", '');
        $username = (string) config("database.connections.{$connectionName}.username", '');
        $password = (string) config("database.connections.{$connectionName}.password", '');

        if ($database === '' || $username === '') {
            throw new \RuntimeException('Missing MySQL database credentials for backup.');
        }

        $sqlPath = $backupDir . DIRECTORY_SEPARATOR . "{$appName}_{$connectionName}_{$stamp}.sql";
        $archivePath = $sqlPath . '.gz';

        $dumpCmd = sprintf(
            'mysqldump --single-transaction --quick --skip-lock-tables --default-character-set=utf8mb4 -h %s -P %s -u %s %s > %s',
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            escapeshellarg($database),
            escapeshellarg($sqlPath)
        );

        $dump = Process::fromShellCommandline($dumpCmd, base_path(), ['MYSQL_PWD' => $password]);
        $dump->setTimeout(900);
        $dump->run();

        if (!$dump->isSuccessful()) {
            throw new \RuntimeException('mysqldump failed: ' . trim($dump->getErrorOutput() ?: $dump->getOutput()));
        }

        if (!is_file($sqlPath) || (filesize($sqlPath) ?: 0) === 0) {
            throw new \RuntimeException('mysqldump completed but SQL file is missing or empty.');
        }

        $this->gzipFile($sqlPath, $archivePath);
        @unlink($sqlPath);

        return $archivePath;
    }

    private function createSqliteArchive(string $connectionName, string $backupDir, string $appName, string $stamp): string
    {
        $dbFile = (string) config("database.connections.{$connectionName}.database", '');
        if ($dbFile === '' || !is_file($dbFile)) {
            throw new \RuntimeException('SQLite database file not found for backup.');
        }

        $copyPath = $backupDir . DIRECTORY_SEPARATOR . "{$appName}_{$connectionName}_{$stamp}.sqlite";
        $archivePath = $copyPath . '.gz';

        if (!copy($dbFile, $copyPath)) {
            throw new \RuntimeException('Failed to copy SQLite database for backup.');
        }

        $this->gzipFile($copyPath, $archivePath);
        @unlink($copyPath);

        return $archivePath;
    }

    private function gzipFile(string $sourcePath, string $targetPath): void
    {
        $gzipCmd = sprintf('gzip -c %s > %s', escapeshellarg($sourcePath), escapeshellarg($targetPath));
        $gzip = Process::fromShellCommandline($gzipCmd, base_path());
        $gzip->setTimeout(900);
        $gzip->run();

        if (!$gzip->isSuccessful()) {
            throw new \RuntimeException('gzip failed: ' . trim($gzip->getErrorOutput() ?: $gzip->getOutput()));
        }

        if (!is_file($targetPath) || (filesize($targetPath) ?: 0) === 0) {
            throw new \RuntimeException('gzip completed but archive file is missing or empty.');
        }
    }

    private function sendToTelegram(string $botToken, string $chatId, string $archivePath, string $connectionName, int $sizeBytes): void
    {
        $url = "https://api.telegram.org/bot{$botToken}/sendDocument";
        $caption = sprintf(
            "🗄️ <b>CryptoNest DB Backup</b>\nConnection: <code>%s</code>\nTime: <code>%s</code>\nSize: <code>%s</code>",
            $connectionName,
            now()->format('Y-m-d H:i:s'),
            $this->formatBytes($sizeBytes)
        );

        $handle = fopen($archivePath, 'rb');
        if ($handle === false) {
            throw new \RuntimeException('Failed to open backup file for Telegram upload.');
        }

        try {
            $response = Http::timeout(180)
                ->attach('document', $handle, basename($archivePath))
                ->post($url, [
                    'chat_id' => $chatId,
                    'caption' => $caption,
                    'parse_mode' => 'HTML',
                ]);
        } finally {
            fclose($handle);
        }

        if (!$response->successful()) {
            throw new \RuntimeException('Telegram sendDocument failed: ' . $response->body());
        }
    }

    private function cleanupOldBackups(string $backupDir, int $retentionDays): void
    {
        $threshold = now()->subDays($retentionDays)->getTimestamp();

        foreach (glob($backupDir . DIRECTORY_SEPARATOR . '*') ?: [] as $file) {
            if (!is_file($file)) {
                continue;
            }

            $mtime = filemtime($file) ?: 0;
            if ($mtime > 0 && $mtime < $threshold) {
                @unlink($file);
            }
        }
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes < 1024) {
            return $bytes . ' B';
        }

        $units = ['KB', 'MB', 'GB', 'TB'];
        $value = $bytes / 1024;
        $idx = 0;

        while ($value >= 1024 && $idx < count($units) - 1) {
            $value /= 1024;
            $idx++;
        }

        return number_format($value, 2) . ' ' . $units[$idx];
    }
}
