<?php

namespace Database\Seeders;

use App\Models\Student;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = [
            // XII RPL 1
            ['finger_id' => '001', 'nis' => '2024001', 'name' => 'Ahmad Fadli', 'class' => 'XII RPL 1', 'major' => 'Rekayasa Perangkat Lunak'],
            ['finger_id' => '002', 'nis' => '2024002', 'name' => 'Siti Nurhaliza', 'class' => 'XII RPL 1', 'major' => 'Rekayasa Perangkat Lunak'],
            ['finger_id' => '003', 'nis' => '2024003', 'name' => 'Budi Santoso', 'class' => 'XII RPL 1', 'major' => 'Rekayasa Perangkat Lunak'],
            ['finger_id' => '004', 'nis' => '2024004', 'name' => 'Dewi Anggraini', 'class' => 'XII RPL 1', 'major' => 'Rekayasa Perangkat Lunak'],
            ['finger_id' => '005', 'nis' => '2024005', 'name' => 'Eko Prasetyo', 'class' => 'XII RPL 1', 'major' => 'Rekayasa Perangkat Lunak'],
            
            // XII RPL 2
            ['finger_id' => '006', 'nis' => '2024006', 'name' => 'Fatimah Zahra', 'class' => 'XII RPL 2', 'major' => 'Rekayasa Perangkat Lunak'],
            ['finger_id' => '007', 'nis' => '2024007', 'name' => 'Galih Pratama', 'class' => 'XII RPL 2', 'major' => 'Rekayasa Perangkat Lunak'],
            ['finger_id' => '008', 'nis' => '2024008', 'name' => 'Hana Pertiwi', 'class' => 'XII RPL 2', 'major' => 'Rekayasa Perangkat Lunak'],
            ['finger_id' => '009', 'nis' => '2024009', 'name' => 'Ivan Gunawan', 'class' => 'XII RPL 2', 'major' => 'Rekayasa Perangkat Lunak'],
            ['finger_id' => '010', 'nis' => '2024010', 'name' => 'Julia Ananda', 'class' => 'XII RPL 2', 'major' => 'Rekayasa Perangkat Lunak'],
            
            // XII TKJ 1
            ['finger_id' => '011', 'nis' => '2024011', 'name' => 'Kevin Wijaya', 'class' => 'XII TKJ 1', 'major' => 'Teknik Komputer dan Jaringan'],
            ['finger_id' => '012', 'nis' => '2024012', 'name' => 'Linda Sari', 'class' => 'XII TKJ 1', 'major' => 'Teknik Komputer dan Jaringan'],
            ['finger_id' => '013', 'nis' => '2024013', 'name' => 'Muhammad Rizki', 'class' => 'XII TKJ 1', 'major' => 'Teknik Komputer dan Jaringan'],
            ['finger_id' => '014', 'nis' => '2024014', 'name' => 'Nadia Kusuma', 'class' => 'XII TKJ 1', 'major' => 'Teknik Komputer dan Jaringan'],
            ['finger_id' => '015', 'nis' => '2024015', 'name' => 'Oscar Putra', 'class' => 'XII TKJ 1', 'major' => 'Teknik Komputer dan Jaringan'],
            
            // XII TKJ 2
            ['finger_id' => '016', 'nis' => '2024016', 'name' => 'Putri Maharani', 'class' => 'XII TKJ 2', 'major' => 'Teknik Komputer dan Jaringan'],
            ['finger_id' => '017', 'nis' => '2024017', 'name' => 'Qori Hidayat', 'class' => 'XII TKJ 2', 'major' => 'Teknik Komputer dan Jaringan'],
            ['finger_id' => '018', 'nis' => '2024018', 'name' => 'Rina Wulandari', 'class' => 'XII TKJ 2', 'major' => 'Teknik Komputer dan Jaringan'],
            ['finger_id' => '019', 'nis' => '2024019', 'name' => 'Surya Darma', 'class' => 'XII TKJ 2', 'major' => 'Teknik Komputer dan Jaringan'],
            ['finger_id' => '020', 'nis' => '2024020', 'name' => 'Tania Dewi', 'class' => 'XII TKJ 2', 'major' => 'Teknik Komputer dan Jaringan'],
            
            // XII MM 1
            ['finger_id' => '021', 'nis' => '2024021', 'name' => 'Umar Faruq', 'class' => 'XII MM 1', 'major' => 'Multimedia'],
            ['finger_id' => '022', 'nis' => '2024022', 'name' => 'Vina Melati', 'class' => 'XII MM 1', 'major' => 'Multimedia'],
            ['finger_id' => '023', 'nis' => '2024023', 'name' => 'Wahyu Adi', 'class' => 'XII MM 1', 'major' => 'Multimedia'],
            ['finger_id' => '024', 'nis' => '2024024', 'name' => 'Xena Paramita', 'class' => 'XII MM 1', 'major' => 'Multimedia'],
            ['finger_id' => '025', 'nis' => '2024025', 'name' => 'Yoga Permana', 'class' => 'XII MM 1', 'major' => 'Multimedia'],
            
            // XII MM 2
            ['finger_id' => '026', 'nis' => '2024026', 'name' => 'Zahra Amelia', 'class' => 'XII MM 2', 'major' => 'Multimedia'],
            ['finger_id' => '027', 'nis' => '2024027', 'name' => 'Aldi Rahman', 'class' => 'XII MM 2', 'major' => 'Multimedia'],
            ['finger_id' => '028', 'nis' => '2024028', 'name' => 'Bella Safitri', 'class' => 'XII MM 2', 'major' => 'Multimedia'],
            ['finger_id' => '029', 'nis' => '2024029', 'name' => 'Candra Wijaya', 'class' => 'XII MM 2', 'major' => 'Multimedia'],
            ['finger_id' => '030', 'nis' => '2024030', 'name' => 'Dinda Puspita', 'class' => 'XII MM 2', 'major' => 'Multimedia'],
        ];

        foreach ($students as $student) {
            Student::create(array_merge($student, ['is_active' => true]));
        }
    }
}
