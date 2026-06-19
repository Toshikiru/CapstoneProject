@extends('layouts.admin')
@section('title', 'Edit Student')
@section('content')

<x-ui.page-header
    :title="'Edit: '.$student->name"
    :subtitle="$student->student_id"
    :back="route('admin.students.show', $student)"
    :breadcrumbs="[['label'=>'Students','url'=>route('admin.students.index')],['label'=>$student->name,'url'=>route('admin.students.show',$student)],['label'=>'Edit']]"
/>

<form method="POST" action="{{ route('admin.students.update', $student) }}" class="space-y-6 max-w-4xl">
    @csrf @method('PUT')
    @include('admin.students._form')
    <div class="flex gap-3">
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl font-medium transition-colors">
            <i class="fas fa-save mr-2"></i>Update Student
        </button>
        <a href="{{ route('admin.students.show', $student) }}" class="px-6 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 font-medium transition-colors">Cancel</a>
    </div>
</form>
@endsection
