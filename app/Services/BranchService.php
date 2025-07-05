<?php

namespace App\Services;

use App\Models\Branch;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class BranchService
{
    /**
     * Create a new branch, its database, and run tenant migrations.
     *
     * @param array $data
     * @return Branch
     * @throws \Exception
     */
    public function createBranch(array $data): Branch
    {
        // Generate UUID for db_name
        $dbName = 'tenant_' . Str::uuid()->toString();
        $dbUsername = env('DB_USERNAME', 'root');
        $dbPassword = env('DB_PASSWORD', '');

        // Add db_name, db_username, db_password to data
        $data['db_name'] = $dbName;
        $data['db_username'] = $dbUsername;
        $data['db_password'] = $dbPassword;

        DB::beginTransaction();
        try {
            // Create the branch
            $branch = Branch::create($data);

            // Create the database if it does not exist
            DB::statement("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

            // Configure the tenant connection
            config([
                'database.connections.tenant' => [
                    'driver' => 'mysql',
                    'host' => env('DB_HOST', '127.0.0.1'),
                    'database' => $dbName,
                    'username' => $dbUsername,
                    'password' => $dbPassword,
                    'charset' => 'utf8mb4',
                    'collation' => 'utf8mb4_unicode_ci',
                ]
            ]);

            // Run tenant migrations
            Artisan::call('migrate', [
                '--path' => '/database/migrations/tenant',
                '--database' => 'tenant',
                '--force' => true,
            ]);

            DB::commit();
            return $branch;
        } catch (\Exception $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            throw $e;
        }
    }
} 