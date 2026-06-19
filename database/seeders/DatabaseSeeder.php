<?php

namespace Database\Seeders;

use App\Models\Exam;
use App\Models\Question;
use App\Models\ScoreInterpretation;
use App\Models\Section;
use App\Models\StudentProfile;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Admin Account ────────────────────────────────────────────────────
        $admin = User::create([
            'student_id' => 'ADMIN-001',
            'name'       => 'Guidance Counselor',
            'email'      => 'admin@tpc.edu.ph',
            'password'   => 'Admin@1234',
            'role'       => 'admin',
            'is_active'  => true,
        ]);

        // ── Demo Student ─────────────────────────────────────────────────────
        $student = User::create([
            'student_id' => '2024-0001',
            'name'       => 'Juan Dela Cruz',
            'password'   => 'Student@1234',
            'role'       => 'student',
            'is_active'  => true,
        ]);

        StudentProfile::create([
            'user_id'                 => $student->id,
            'student_id_number'       => '2024-0001',
            'first_name'              => 'Juan',
            'middle_name'             => 'Santos',
            'last_name'               => 'Dela Cruz',
            'sex'                     => 'Male',
            'date_of_birth'           => '2005-03-15',
            'address'                 => 'Talibon, Bohol, Philippines',
            'contact_number'          => '09123456789',
            'guardian_name'           => 'Maria Dela Cruz',
            'guardian_contact_number' => '09987654321',
            'course'                  => 'Bachelor of Science in Information Technology',
            'year_level'              => '1st Year',
            'admission_status'        => 'Pending',
        ]);

        // ── Demo Exam ─────────────────────────────────────────────────────────
        $exam = Exam::create([
            'created_by'  => $admin->id,
            'title'       => 'Talibon Polytechnic College Entrance Examination 2024',
            'description' => 'Official entrance examination for incoming freshmen of Talibon Polytechnic College.',
            'instructions'=> 'Read each question carefully. Choose the best answer. You have 60 minutes to complete this exam.',
            'time_limit'  => 60,
            'passing_score'=> 75,
            'access_code' => 'TPC2024A',
            'is_active'   => true,
            'max_attempts'=> 1,
        ]);

        // Score interpretations
        ScoreInterpretation::insert([
            ['exam_id' => $exam->id, 'min_score' => 90, 'max_score' => 100, 'label' => 'Passed',      'description' => 'Excellent performance.', 'admission_status' => 'Passed',      'created_at' => now(), 'updated_at' => now()],
            ['exam_id' => $exam->id, 'min_score' => 75, 'max_score' => 89,  'label' => 'Conditional', 'description' => 'Subject for further evaluation.', 'admission_status' => 'Conditional', 'created_at' => now(), 'updated_at' => now()],
            ['exam_id' => $exam->id, 'min_score' => 0,  'max_score' => 74,  'label' => 'Failed',      'description' => 'Did not meet the minimum requirement.', 'admission_status' => 'Failed',      'created_at' => now(), 'updated_at' => now()],
        ]);

        // Section 1: English
        $sec1 = Section::create(['exam_id' => $exam->id, 'title' => 'English Comprehension', 'order' => 1]);
        Question::insert([
            ['exam_id' => $exam->id, 'section_id' => $sec1->id, 'type' => 'multiple_choice', 'question_text' => 'Which of the following is the correct synonym of "diligent"?', 'options' => json_encode(['Lazy','Hardworking','Careless','Reckless']), 'correct_answer' => 'Hardworking', 'points' => 1, 'order' => 1, 'is_required' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['exam_id' => $exam->id, 'section_id' => $sec1->id, 'type' => 'true_or_false',   'question_text' => 'A pronoun replaces a noun in a sentence.', 'options' => json_encode(['True','False']), 'correct_answer' => 'True', 'points' => 1, 'order' => 2, 'is_required' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Section 2: Mathematics
        $sec2 = Section::create(['exam_id' => $exam->id, 'title' => 'Mathematics', 'order' => 2]);
        Question::insert([
            ['exam_id' => $exam->id, 'section_id' => $sec2->id, 'type' => 'multiple_choice', 'question_text' => 'What is 15% of 200?', 'options' => json_encode(['25','30','35','40']), 'correct_answer' => '30', 'points' => 1, 'order' => 3, 'is_required' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['exam_id' => $exam->id, 'section_id' => $sec2->id, 'type' => 'true_or_false',   'question_text' => 'The square root of 144 is 12.', 'options' => json_encode(['True','False']), 'correct_answer' => 'True', 'points' => 1, 'order' => 4, 'is_required' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Section 3: Self-Assessment (Likert)
        $sec3 = Section::create(['exam_id' => $exam->id, 'title' => 'Personal Assessment', 'instructions' => 'Rate yourself from 1 (Strongly Disagree) to 5 (Strongly Agree).', 'order' => 3]);
        Question::insert([
            ['exam_id' => $exam->id, 'section_id' => $sec3->id, 'type' => 'likert_scale', 'question_text' => 'I am motivated to pursue higher education.', 'options' => json_encode(['1 - Strongly Disagree','2 - Disagree','3 - Neutral','4 - Agree','5 - Strongly Agree']), 'correct_answer' => null, 'points' => 5, 'order' => 5, 'is_required' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Section 4: Short Answer
        $sec4 = Section::create(['exam_id' => $exam->id, 'title' => 'Short Answer', 'order' => 4]);
        Question::create([
            'exam_id'       => $exam->id,
            'section_id'    => $sec4->id,
            'type'          => 'short_answer',
            'question_text' => 'In 2-3 sentences, explain why you chose your course.',
            'options'       => null,
            'correct_answer'=> null,
            'points'        => 5,
            'order'         => 6,
            'is_required'   => true,
        ]);

        // ── System Settings ───────────────────────────────────────────────────
        SystemSetting::insert([
            ['key' => 'institution_name',   'value' => 'Talibon Polytechnic College', 'type' => 'string', 'group' => 'general', 'label' => 'Institution Name',   'created_at' => now(), 'updated_at' => now()],
            ['key' => 'institution_address','value' => 'Talibon, Bohol, Philippines', 'type' => 'string', 'group' => 'general', 'label' => 'Institution Address','created_at' => now(), 'updated_at' => now()],
            ['key' => 'system_name',        'value' => 'Entrance Examination & Student Profile System', 'type' => 'string', 'group' => 'general', 'label' => 'System Name', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'academic_year',      'value' => '2024-2025',                  'type' => 'string', 'group' => 'general', 'label' => 'Academic Year',      'created_at' => now(), 'updated_at' => now()],
        ]);

        $this->command->info('✔ Database seeded successfully!');
        $this->command->info('── Admin Login ─────────────────');
        $this->command->info('  Student ID: ADMIN-001');
        $this->command->info('  Password:   Admin@1234');
        $this->command->info('── Demo Student ─────────────────');
        $this->command->info('  Student ID: 2024-0001');
        $this->command->info('  Password:   Student@1234');
        $this->command->info('── Demo Exam ────────────────────');
        $this->command->info('  Access Code: TPC2024A');
    }
}
