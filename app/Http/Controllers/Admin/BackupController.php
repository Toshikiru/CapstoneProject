<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ActivityLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BackupController extends Controller
{
    public function index(): View
    {
        $backups = collect(Storage::files('backups'))
            ->map(fn($file) => [
                'name'    => basename($file),
                'size' => round(Storage::size($file) / 1024 / 1024, 2) . ' MB',
                'created' => Storage::lastModified($file),
                'path'    => $file,
            ])
            ->sortByDesc('created')
            ->values();

        return view('admin.settings.backup', compact('backups'));
    }

    public function create(): RedirectResponse
    {
        try {
            $filename  = 'backup_' . now()->format('Ymd_His') . '.sql';
            $dbName    = config('database.connections.mysql.database');
            $dbUser    = config('database.connections.mysql.username');
            $dbPass    = config('database.connections.mysql.password');
            $dbHost    = config('database.connections.mysql.host');

            $tables = DB::select('SHOW TABLES');
            $sql    = "-- TPC Entrance Exam System Backup\n-- Generated: " . now() . "\n\n";
            $sql   .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

            foreach ($tables as $table) {
                $tableName = array_values((array) $table)[0];
                $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
                $sql .= array_values((array) $createTable[0])[1] . ";\n\n";

                $rows = DB::table($tableName)->get();
                foreach ($rows as $row) {
                    $values = array_map(fn($v) => is_null($v) ? 'NULL' : "'" . addslashes((string)$v) . "'", (array)$row);
                    $sql   .= "INSERT INTO `{$tableName}` VALUES (" . implode(', ', $values) . ");\n";
                }
                $sql .= "\n";
            }

            $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

            Storage::put("backups/{$filename}", $sql);

            ActivityLogService::log('backup_created', "Database backup created: {$filename}");

            return back()->with('success', "Backup created: {$filename}");
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Backup failed: ' . $e->getMessage()]);
        }
    }

    public function download(string $filename): StreamedResponse
    {
        $path = "backups/{$filename}";
        abort_unless(Storage::exists($path), 404);

        ActivityLogService::log('backup_downloaded', "Downloaded backup: {$filename}");

        return Storage::download($path, $filename);
    }

    public function destroy(string $filename): RedirectResponse
    {
        Storage::delete("backups/{$filename}");
        ActivityLogService::log('backup_deleted', "Deleted backup: {$filename}");

        return back()->with('success', 'Backup deleted.');
    }
}
