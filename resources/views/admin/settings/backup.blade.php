@extends('layouts.admin')
@section('title', 'Backup & Restore')
@section('content')

<x-ui.page-header title="Backup &amp; Restore" subtitle="Manage database backups for data safety" :breadcrumbs="[['label'=>'Backup']]"/>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Create Backup --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-6">
        <h3 class="font-semibold text-slate-700 dark:text-slate-200 mb-2"><i class="fas fa-plus-circle mr-2 text-blue-500"></i>Create Backup</h3>
        <p class="text-sm text-slate-500 dark:text-slate-400 mb-5">Generate a new backup of the current database. Backups are stored on the server.</p>
        <form method="POST" action="{{ route('admin.backup.create') }}">
            @csrf
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl font-medium transition-colors">
                <i class="fas fa-database mr-2"></i>Create Backup Now
            </button>
        </form>
    </div>

    {{-- Restore --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-6">
        <h3 class="font-semibold text-slate-700 dark:text-slate-200 mb-2"><i class="fas fa-upload mr-2 text-amber-500"></i>Restore from File</h3>
        <p class="text-sm text-slate-500 dark:text-slate-400 mb-5">Upload a <code class="bg-slate-100 dark:bg-slate-700 px-1 rounded">.sql</code> file to restore the database. <strong class="text-red-500">This will overwrite all current data.</strong></p>
        <form method="POST" {{-- Restore feature not implemented yet --}} enctype="multipart/form-data">
            @csrf
            <div class="flex gap-2">
                <input type="file" name="backup_file" accept=".sql" class="flex-1 text-sm text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-600 rounded-xl px-3 py-2 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-sm file:bg-blue-50 file:text-blue-700 dark:file:bg-blue-500/10 dark:file:text-blue-400">
                <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white px-4 py-2.5 rounded-xl text-sm font-medium transition-colors flex-shrink-0">Restore</button>
            </div>
        </form>
    </div>
</div>

{{-- Backup Files --}}
<div class="mt-6 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700">
        <h3 class="font-semibold text-slate-700 dark:text-slate-200"><i class="fas fa-folder-open mr-2 text-slate-400"></i>Backup Files</h3>
    </div>
    @if($backups->count())
    <div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-700">
            <tr>
                <th class="text-left px-4 py-3 font-semibold text-slate-600 dark:text-slate-300">Filename</th>
                <th class="text-left px-4 py-3 font-semibold text-slate-600 dark:text-slate-300">Size</th>
                <th class="text-left px-4 py-3 font-semibold text-slate-600 dark:text-slate-300">Created</th>
                <th class="text-right px-4 py-3 font-semibold text-slate-600 dark:text-slate-300">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
           @foreach($backups as $file)
            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/40">
                <td class="px-4 py-3 font-mono text-sm text-slate-700 dark:text-slate-300">{{ $file['name'] }}</td>
                <td class="px-4 py-3 text-slate-600 dark:text-slate-400">{{ $file['size'] }}</td>
                <td class="px-4 py-3 text-slate-600 dark:text-slate-400">{{ $file['created'] }}</td>
                <td class="px-4 py-3 text-right">
                    <div class="flex items-center justify-end gap-2">
                        <a href="{{ route('admin.backup.download', $file['name']) }}" class="text-blue-600 dark:text-blue-400 hover:underline text-xs font-medium">
                            <i class="fas fa-download mr-1"></i>Download
                        </a>
                        <x-ui.confirm-modal
                           :action="route('admin.backup.destroy', $file['name'])"
                            method="DELETE"
                            title="Delete backup?"
                            :message="'Delete '.$file['name'].'? This cannot be undone.'"
                            confirm-label="Delete"
                            trigger-class="text-red-500 dark:text-red-400 hover:underline text-xs font-medium">
                            <i class="fas fa-trash mr-1"></i>Delete
                        </x-ui.confirm-modal>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    </div>
    @else
        <x-ui.empty-state icon="fa-database" title="No backups yet" subtitle="Create your first backup using the button above." />
    @endif
</div>
@endsection
