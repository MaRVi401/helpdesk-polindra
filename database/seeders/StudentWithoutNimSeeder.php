<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class StudentWithoutNimSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data akun student dengan email format student tapi tanpa NIM
        $testing_students = [
            [
                'name' => 'Fadil Student', 
                'email' => 'fadil@student.polindra.ac.id', 
                'password' => 'password123',
            ],
            [
                'name' => 'Budi Student', 
                'email' => 'budi@student.polindra.ac.id', 
                'password' => 'password123',
            ],
            [
                'name' => 'Siti Student', 
                'email' => 'siti@student.polindra.ac.id', 
                'password' => 'password123',
            ],
            // dengan domain student
            [
                'name' => 'Cristiano Ronaldo', 
                'email' => '2309010@student.polindra.ac.id', 
                'password' => '2309010',
            ],
            [
                'name' => 'Lionel Messi', 
                'email' => '2309020@student.polindra.ac.id', 
                'password' => '2309020',
            ],
        ];

        foreach ($testing_students as $user_data) {
            // Cek jika user sudah ada
            $existingUser = User::where('email', $user_data['email'])->first();
            
            if (!$existingUser) {
                // Buat user baru TANPA data mahasiswa
                $user = User::create([
                    'name' => $user_data['name'],
                    'email' => $user_data['email'],
                    'password' => bcrypt($user_data['password']),
                    'role' => 'mahasiswa',
                ]);

                // JANGAN buat data Mahasiswa -> biarkan kosong untuk testing

                $this->command->info("Student without NIM created: {$user_data['email']}");
            } else {
                $this->command->warn("User {$user_data['email']} already exists.");
            }
        }
    }
}