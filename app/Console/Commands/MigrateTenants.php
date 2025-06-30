<?php

namespace App\Console\Commands;

use App\Models\Branch;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class MigrateTenants extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:migrate-tenants';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs migrations for all tenants i.e. School branches in this case.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $branches = Branch::with('school')->get();

        foreach ($branches as $branch) {
            DB::purge('tenant');

            config([
                'database.connections.tenant' => [
                    'driver' => 'mysql',
                    'host' => '127.0.0.1',
                    'database' => $branch->db_name,
                    'username' => 'root',
                    'password' => '',
                    'charset' => 'utf8mb4',
                    'collation' => 'utf8mb4_unicode_ci',
                ]
            ]);

            DB::setDefaultConnection('tenant');

            $this->info("Migrating DB: {$branch->db_name}");

            Artisan::call('migrate', [
                '--path' => '/database/migrations/tenant',
                '--database' => 'tenant',
                '--force' => true,
            ]);

            DB::disconnect('tenant');
        }

        $this->info('Tenant migrations complete.');
    }
}
