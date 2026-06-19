@extends('layouts.admin')
@section('title', 'Create Exam')
@section('content')

<x-ui.page-header
    title="Create New Exam"
    subtitle="Set up a new entrance examination"
    :back="route('admin.exams.index')"
    :breadcrumbs="[['label'=>'Examinations','url'=>route('admin.exams.index')],['label'=>'Create']]"
/>

<form method="POST" action="{{ route('admin.exams.store') }}" class="max-w-3xl">
    @csrf
    @include('admin.exams._form')
    <div class="flex gap-3 mt-6">
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl font-medium transition-colors">
            <i class="fas fa-save mr-2"></i>Create Exam
        </button>
        <a href="{{ route('admin.exams.index') }}" class="px-6 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 font-medium transition-colors">Cancel</a>
    </div>
</form>
@endsection
