<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Student;
use App\Models\Walas;
use App\Models\Kelas;

$name = 'AYU SETYAWATI';
$student = Student::with('kelas')->where('name', 'LIKE', "%$name%")->first();

if (!$student) {
    echo "Student '$name' not found.\n";
    exit;
}

echo "Student: " . $student->name . "\n";
echo "Kelas ID: " . $student->kelas_id . "\n";
echo "Kelas Name: " . ($student->kelas->nm_kls ?? 'N/A') . "\n";

echo "Checking Walas for Class ID " . $student->kelas_id . "...\n";
$walas = Walas::where('kelas_id', $student->kelas_id)->get();

if ($walas->count() > 0) {
    foreach ($walas as $w) {
        echo "Found Walas ID: " . $w->id . ", Is Active: " . $w->is_active . ", Guru ID: " . $w->guru_id . "\n";
    }
} else {
    echo "No Walas record found for this class ID.\n";
}

// Check if maybe there is a legacy relation or data?
echo "Checking all active walas...\n";
$activeWalas = Walas::where('is_active', true)->count();
echo "Total active walas in DB: $activeWalas\n";
