@php $profile = isset($student) ? $student->studentProfile : null; @endphp

<div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
    <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100"><i class="fas fa-id-card mr-2 text-blue-500"></i>Account Information</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Student ID <span class="text-red-500">*</span></label>
            <input type="text" name="student_id" value="{{ old('student_id', isset($student) ? $student->student_id : '') }}"
                   class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 @error('student_id') border-red-400 @enderror"
                   placeholder="e.g. 2024-0001" required>
            @error('student_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Full Name (Display) <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ old('name', isset($student) ? $student->name : '') }}"
                   class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Password {{ isset($student) ? '(leave blank to keep)' : '' }} <span class="text-red-500">*</span></label>
            <input type="password" name="password"
                   class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
                   placeholder="Min. 8 characters">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Confirm Password</label>
            <input type="password" name="password_confirmation"
                   class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
    </div>
</div>

<div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
    <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100"><i class="fas fa-user mr-2 text-indigo-500"></i>Personal Information</h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div><label class="block text-sm font-medium text-slate-700 mb-1">First Name <span class="text-red-500">*</span></label>
            <input type="text" name="first_name" value="{{ old('first_name', $profile?->first_name) }}" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400" required></div>
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Middle Name</label>
            <input type="text" name="middle_name" value="{{ old('middle_name', $profile?->middle_name) }}" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"></div>
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Last Name <span class="text-red-500">*</span></label>
            <input type="text" name="last_name" value="{{ old('last_name', $profile?->last_name) }}" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400" required></div>
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Suffix</label>
            <input type="text" name="suffix" value="{{ old('suffix', $profile?->suffix) }}" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm" placeholder="Jr., Sr., III"></div>
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Sex <span class="text-red-500">*</span></label>
            <select name="sex" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                <option value="">Select</option>
                <option value="Male" {{ old('sex',$profile?->sex)==='Male'?'selected':'' }}>Male</option>
                <option value="Female" {{ old('sex',$profile?->sex)==='Female'?'selected':'' }}>Female</option>
            </select></div>
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Date of Birth <span class="text-red-500">*</span></label>
            <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $profile?->date_of_birth?->format('Y-m-d')) }}" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400" required></div>
        <div class="md:col-span-2"><label class="block text-sm font-medium text-slate-700 mb-1">Address <span class="text-red-500">*</span></label>
            <input type="text" name="address" value="{{ old('address', $profile?->address) }}" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400" required></div>
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Contact Number</label>
            <input type="text" name="contact_number" value="{{ old('contact_number', $profile?->contact_number) }}" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm"></div>
    </div>
</div>

<div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
    <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100"><i class="fas fa-users mr-2 text-green-500"></i>Guardian & Academic Info</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Guardian Name</label>
            <input type="text" name="guardian_name" value="{{ old('guardian_name', $profile?->guardian_name) }}" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm"></div>
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Guardian Contact</label>
            <input type="text" name="guardian_contact_number" value="{{ old('guardian_contact_number', $profile?->guardian_contact_number) }}" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm"></div>
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Course <span class="text-red-500">*</span></label>
            <input type="text" name="course" value="{{ old('course', $profile?->course) }}" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400" required placeholder="e.g. BS Information Technology"></div>
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Year Level <span class="text-red-500">*</span></label>
            <select name="year_level" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                <option value="">Select Year Level</option>
                @foreach(['1st Year','2nd Year','3rd Year','4th Year'] as $y)
                <option value="{{ $y }}" {{ old('year_level',$profile?->year_level)===$y?'selected':'' }}>{{ $y }}</option>
                @endforeach
            </select></div>
    </div>
</div>
