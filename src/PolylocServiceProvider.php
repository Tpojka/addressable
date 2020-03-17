<?php
/**
 * Project polyloc
 * File: PolylocServiceProvider.php
 * Created by: tpojka
 * On: 14/03/2020
 */

namespace Tpojka\Polyloc;

use Carbon\Carbon;
use DirectoryIterator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class PolylocServiceProvider extends ServiceProvider
{
    private CONST STUB_DIR = '/../stub/';
    private CONST MIGRATION_BASENAME_STUB = 'create_addresses_table_tpojka_polyloc.php';
    private CONST LARAVEL_MIGRATIONS_LOCATION = '/../../../../database/migrations/';

    public function register()
    {
        //
    }

    public function boot()
    {
        $this->publishMigrations();
    }

    /**
     * Copying migration file to project directory
     */
    private function publishMigrations()
    {
        if (!is_file(__DIR__ . self::STUB_DIR . self::MIGRATION_BASENAME_STUB)) {
            Log::debug(realpath(__DIR__ . self::STUB_DIR . self::MIGRATION_BASENAME_STUB) . ' file is missing.');
            return false;
        }

        if (true === $this->previousInstallation()) {
            return false;
        }

        $stubFile = __DIR__ . self::STUB_DIR . self::MIGRATION_BASENAME_STUB;
        $laravelMigLoc = __DIR__ . self::LARAVEL_MIGRATIONS_LOCATION . self::MIGRATION_BASENAME_STUB;

        copy(
            $stubFile,
            $laravelMigLoc
        );

        $now = Carbon::now();
        $packageMigrationFullDate = str_replace(self::MIGRATION_BASENAME_STUB, $now->format('Y_m_d_His') . '_' . self::MIGRATION_BASENAME_STUB, $laravelMigLoc);

        rename($laravelMigLoc, $packageMigrationFullDate);
        Log::info('tpojka/polyloc migration file provided.');
    }

    /**
     * List all /database/migrations/* files
     * And check if we already made this through vendor discovering
     *
     * @return bool
     */
    private function previousInstallation(): bool
    {
        foreach (new DirectoryIterator(__DIR__ . self::LARAVEL_MIGRATIONS_LOCATION) as $fileInfo) {
            if ($fileInfo->isDot()) {
                continue;
            }

            $length = strlen(self::MIGRATION_BASENAME_STUB);
            if ($length == 0) {
                return true;
            }

            return (substr($fileInfo->getFilename(), -$length) === self::MIGRATION_BASENAME_STUB);
        }
    }
}