@extends('layouts.admin')
@section('title', 'Add Student')
@section('content')

<x-ui.page-header
    title="Add New Student"
    subtitle="Create a student account and profile"
    :back="route('admin.students.index')"
    :breadcrumbs="[['label'=>'Students','url'=>route('admin.students.index')],['label'=>'Add Student']]"
/>

<form method="POST" action="{{ route('admin.students.store') }}" class="space-y-6 max-w-4xl">
    @csrf
    @include('admin.students._form')
    <div class="flex gap-3">
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl font-medium transition-colors">
            <i class="fas fa-save mr-2"></i>Create Student
        </button>
        <a href="{{ route('admin.students.index') }}" class="px-6 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 font-medium transition-colors">Cancel</a>
    </div>
</form>
@endsection
