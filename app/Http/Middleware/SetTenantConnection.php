<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Branch;
use Illuminate\Support\Facades\DB;

class SetTenantConnection
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $branchId = $request->route('branch_id');
        if ($branchId) {
            // Cache the branch lookup for 1 minute to reduce DB hits
            $branch = cache()->remember("branch_{$branchId}", 60, function () use ($branchId) {
                return Branch::find($branchId);
            });
            
            if (!$branch) {
                return response()->json(
                    [
                        'message' => 'Branch not found',
                    ],
                    404
                );
            }

            // Only reconfigure if the DB name is different
            $currentDb = config('database.connections.tenant.database');
            if ($currentDb !== $branch->db_name) {
                config([
                    'database.connections.tenant' => [
                        'driver' => 'mysql',
                        'host' => '127.0.0.1',
                        'database' => $branch->db_name,
                        'username' => env('DB_USERNAME', 'root'),
                        'password' => env('DB_PASSWORD', ''),
                        'charset' => 'utf8mb4',
                        'collation' => 'utf8mb4_unicode_ci',
                    ]
                ]);
                DB::purge('tenant');
            }
            DB::setDefaultConnection('tenant');
        }
        return $next($request);
    }
}
