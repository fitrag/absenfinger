<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\JurusanController;
use App\Http\Controllers\Admin\MUserController;
use App\Http\Controllers\Admin\GuruController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\MapelController;
use App\Http\Controllers\Admin\GuruAjarController;
use App\Http\Controllers\Admin\WalasController;
use App\Http\Controllers\Admin\TahunPelajaranController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\AuthController;

// Frontend Routes
Route::get('/', [FrontendController::class, 'home'])->name('frontend.home');
Route::get('/presensi', [FrontendController::class, 'presensi'])->name('frontend.presensi');
Route::get('/kesiswaan', [FrontendController::class, 'kesiswaan'])->name('frontend.kesiswaan');
Route::get('/kesiswaan/pelanggaran/{encryptedId}', [FrontendController::class, 'kesiswaanPelanggaranDetail'])->name('frontend.kesiswaan.pelanggaran.detail');
Route::get('/kesiswaan/konseling/{encryptedId}', [FrontendController::class, 'kesiswaanKonselingDetail'])->name('frontend.kesiswaan.konseling.detail');
Route::get('/kesiswaan/terlambat/{encryptedId}', [FrontendController::class, 'kesiswaanTerlambatDetail'])->name('frontend.kesiswaan.terlambat.detail');

// Auth Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin Routes (Protected by admin middleware)
Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Profile Routes
    Route::get('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [\App\Http\Controllers\Admin\ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::put('/profile/guru', [\App\Http\Controllers\Admin\ProfileController::class, 'updateGuru'])->name('profile.guru');
    Route::put('/profile/student', [\App\Http\Controllers\Admin\ProfileController::class, 'updateStudent'])->name('profile.student');

    // Session Monitoring Routes (Admin Only)
    Route::prefix('sessions')->name('sessions.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\SessionMonitorController::class, 'index'])->name('index');
        Route::get('/online', [\App\Http\Controllers\Admin\SessionMonitorController::class, 'getOnlineUsers'])->name('online');
        Route::get('/activities/{userId}', [\App\Http\Controllers\Admin\SessionMonitorController::class, 'getUserActivities'])->name('activities');
        Route::post('/force-logout/{sessionId}', [\App\Http\Controllers\Admin\SessionMonitorController::class, 'forceLogout'])->name('forceLogout');
    });

    // Students Routes
    Route::get('/students/naik-kelas', [StudentController::class, 'naikKelasForm'])->name('students.naikKelas');
    Route::get('/students/naik-kelas/students/{kelas_id}', [StudentController::class, 'getStudentsByKelasForNaikKelas'])->name('students.naikKelas.students');
    Route::post('/students/naik-kelas', [StudentController::class, 'naikKelas'])->name('students.naikKelas.process');
    Route::get('/students-import/template', [StudentController::class, 'downloadTemplate'])->name('students.template');
    Route::get('/students-import/template-detail', [StudentController::class, 'downloadTemplateDetail'])->name('students.templateDetail');
    Route::post('/students-import', [StudentController::class, 'import'])->name('students.import');
    Route::post('/students-import-detail', [StudentController::class, 'importDetail'])->name('students.importDetail');
    Route::get('/students-export', [StudentController::class, 'export'])->name('students.export');
    Route::get('/students-print-absensi', [StudentController::class, 'printAbsensi'])->name('students.printAbsensi');
    Route::post('/students/{student}/toggle-gender', [StudentController::class, 'toggleGender'])->name('students.toggleGender');
    Route::resource('students', StudentController::class);

    // Kelas Routes
    Route::resource('kelas', KelasController::class);

    // Jurusan Routes
    Route::resource('jurusan', JurusanController::class);

    // Users Routes
    Route::get('user-guru', [MUserController::class, 'guru'])->name('users.guru');
    Route::post('user-guru/{user}/toggle-status', [MUserController::class, 'toggleStatusGuru'])->name('users.guru.toggleStatus');
    Route::post('user-guru/{user}/reset-password', [MUserController::class, 'resetPasswordToNip'])->name('users.guru.resetPassword');
    Route::get('user-siswa', [MUserController::class, 'siswa'])->name('users.siswa');
    Route::get('user-siswa/by-kelas/{kelas_id}', [MUserController::class, 'getUsersByKelas'])->name('users.siswa.byKelas');
    Route::post('user-siswa/bulk-status', [MUserController::class, 'bulkUpdateStatusSiswa'])->name('users.siswa.bulkStatus');
    Route::post('user-siswa/{user}/toggle-status', [MUserController::class, 'toggleStatus'])->name('users.siswa.toggleStatus');
    Route::post('user-siswa/{user}/reset-password', [MUserController::class, 'resetPasswordToNisn'])->name('users.siswa.resetPassword');
    Route::resource('users', MUserController::class);

    // Role Routes
    Route::resource('role', RoleController::class)->except(['create', 'show', 'edit']);

    // Mapel Routes
    Route::resource('mapel', MapelController::class)->except(['create', 'show', 'edit']);

    // Guru Routes - SPECIFIC ROUTES FIRST (before wildcard)
    // Guru Jurnal Routes
    Route::prefix('guru/jurnal')->name('guru.jurnal.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\GuruJurnalController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\Admin\GuruJurnalController::class, 'store'])->name('store');
        Route::get('/pdf', [\App\Http\Controllers\Admin\GuruJurnalController::class, 'downloadPdf'])->name('pdf');
        Route::get('/students/{kelas_id}', [\App\Http\Controllers\Admin\GuruJurnalController::class, 'getStudentsByKelas'])->name('students');
        Route::put('/{id}', [\App\Http\Controllers\Admin\GuruJurnalController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\GuruJurnalController::class, 'destroy'])->name('destroy');
    });

    // Guru Nilai Harian Routes
    Route::prefix('guru/nilai')->name('guru.nilai.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\GuruNilaiHarianController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\Admin\GuruNilaiHarianController::class, 'store'])->name('store');
        Route::get('/students-grades', [\App\Http\Controllers\Admin\GuruNilaiHarianController::class, 'getStudentsWithGrades'])->name('students-grades');
        Route::get('/pdf', [\App\Http\Controllers\Admin\GuruNilaiHarianController::class, 'downloadPdfGroup'])->name('pdf');
        Route::put('/{id}', [\App\Http\Controllers\Admin\GuruNilaiHarianController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\GuruNilaiHarianController::class, 'destroy'])->name('destroy');
    });

    // Guru Tugas Siswa Routes
    Route::prefix('guru/tugas')->name('guru.tugas.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\GuruTugasController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\Admin\GuruTugasController::class, 'store'])->name('store');
        Route::get('/kelas-by-mapel', [\App\Http\Controllers\Admin\GuruTugasController::class, 'getKelasByMapel'])->name('kelasByMapel');
        Route::get('/{id}', [\App\Http\Controllers\Admin\GuruTugasController::class, 'show'])->name('show');
        Route::put('/{id}', [\App\Http\Controllers\Admin\GuruTugasController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\GuruTugasController::class, 'destroy'])->name('destroy');
        Route::put('/{id}/submission/{submissionId}/nilai', [\App\Http\Controllers\Admin\GuruTugasController::class, 'updateNilai'])->name('updateNilai');
    });

    // Guru PKL Routes (For Role PKL)
    Route::prefix('guru/pkl')->name('guru.pkl.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\GuruPklController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\Admin\GuruPklController::class, 'store'])->name('store');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\GuruPklController::class, 'destroy'])->name('destroy');
        Route::get('/students/{kelas_id}', [\App\Http\Controllers\Admin\GuruPklController::class, 'getStudentsByKelas'])->name('students');
        Route::get('/absensi', [\App\Http\Controllers\Admin\GuruPklController::class, 'absensi'])->name('absensi');
        Route::post('/absensi/update-status', [\App\Http\Controllers\Admin\GuruPklController::class, 'updateAbsensiStatus'])->name('absensi.update_status');
        Route::get('/absensi/print', [\App\Http\Controllers\Admin\GuruPklController::class, 'printAbsensi'])->name('absensi.print');
        Route::get('/input-nilai/{id}', [\App\Http\Controllers\Admin\GuruPklController::class, 'inputNilai'])->name('input_nilai');
        Route::post('/store-nilai/{id}', [\App\Http\Controllers\Admin\GuruPklController::class, 'storeNilai'])->name('store_nilai');
        Route::get('/set-lokasi', [\App\Http\Controllers\Admin\GuruPklController::class, 'setLokasi'])->name('set_lokasi');
        Route::put('/update-lokasi/{id}', [\App\Http\Controllers\Admin\GuruPklController::class, 'updateLokasi'])->name('update_lokasi');
        Route::get('/surat-izin', [\App\Http\Controllers\Admin\GuruPklController::class, 'suratIzin'])->name('surat_izin');
        Route::post('/surat-izin/{id}/update-status', [\App\Http\Controllers\Admin\GuruPklController::class, 'updateSuratIzinStatus'])->name('surat_izin.update_status');
        Route::get('/surat-izin/{id}/preview', [\App\Http\Controllers\Admin\GuruPklController::class, 'previewSuratIzin'])->name('surat_izin.preview');
    });

    // Guru Mengajar Kelas Routes (For Guru Role managing own classes)
    Route::prefix('guru/kelas-ajar')->name('guru.kelas-ajar.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\GuruKelasAjarController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\Admin\GuruKelasAjarController::class, 'store'])->name('store');
        Route::post('/store-mapel', [\App\Http\Controllers\Admin\GuruKelasAjarController::class, 'storeMapel'])->name('storeMapel');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\GuruKelasAjarController::class, 'destroy'])->name('destroy');
    });

    // Guru Resource Routes (wildcard routes AFTER specific routes)
    Route::resource('guru', GuruController::class)->except(['create', 'show', 'edit']);
    Route::get('/guru/{guru}', [GuruController::class, 'show'])->name('guru.show');
    Route::get('/guru/{guru}/edit', [GuruController::class, 'edit'])->name('guru.edit');
    Route::get('/guru-import/template', [GuruController::class, 'downloadTemplate'])->name('guru.template');
    Route::post('/guru-import', [GuruController::class, 'import'])->name('guru.import');
    Route::get('/guru-export', [GuruController::class, 'export'])->name('guru.export');

    // Guru Riwayat Routes
    Route::prefix('guru/{guru}/riwayat')->name('guru.riwayat.')->group(function () {
        Route::post('/{type}', [GuruController::class, 'storeRiwayat'])->name('store');
        Route::put('/{type}/{id}', [GuruController::class, 'updateRiwayat'])->name('update');
        Route::delete('/{type}/{id}', [GuruController::class, 'destroyRiwayat'])->name('destroy');
    });

    // Guru Piket Data Routes (Data Master)
    Route::resource('guru-piket-data', \App\Http\Controllers\Admin\DataGuruPiketController::class)->except(['create', 'show', 'edit']);

    // Guru Mengajar Routes
    Route::resource('guruajar', GuruAjarController::class)->except(['create', 'show', 'edit']);
    Route::post('guruajar/kelas', [GuruAjarController::class, 'storeKelas'])->name('guruajar.storeKelas');
    Route::delete('guruajar/kelas/{id}', [GuruAjarController::class, 'destroyKelas'])->name('guruajar.destroyKelas');

    // Wali Kelas Routes
    Route::resource('walas', WalasController::class)->except(['create', 'show', 'edit']);

    // Tahun Pelajaran Routes
    Route::resource('tp', TahunPelajaranController::class)->except(['create', 'show', 'edit']);

    // Attendance Routes
    Route::get('/attendance/export', [AttendanceController::class, 'export'])->name('attendance.export');
    Route::get('/attendance/print-pdf', [AttendanceController::class, 'printPdf'])->name('attendance.printPdf');
    Route::get('/attendance/print-bolos', [AttendanceController::class, 'printBolos'])->name('attendance.printBolos');
    Route::get('/attendance/print-belum-masuk', [AttendanceController::class, 'printBelumMasuk'])->name('attendance.printBelumMasuk');
    Route::get('/attendance/students-by-class', [AttendanceController::class, 'getStudentsByClass'])->name('attendance.students-by-class');
    Route::get('/attendance/absent-students', [AttendanceController::class, 'getAbsentStudents'])->name('attendance.absent-students');
    Route::post('/attendance/store-absence', [AttendanceController::class, 'storeAbsence'])->name('attendance.store-absence');
    Route::put('/attendance/update-absence', [AttendanceController::class, 'updateAbsence'])->name('attendance.update-absence');
    Route::post('/attendance/update-single', [AttendanceController::class, 'updateSingleAttendance'])->name('attendance.updateSingleAttendance');
    Route::post('/attendance/bulk-update', [AttendanceController::class, 'bulkUpdateAttendance'])->name('attendance.bulkUpdateAttendance');
    Route::get('/attendance/kelas/{kelasId}', [AttendanceController::class, 'showByKelas'])->name('attendance.showByKelas');
    Route::resource('attendance', AttendanceController::class);

    // Reports Routes
    Route::get('/reports/daily', [\App\Http\Controllers\Admin\ReportController::class, 'daily'])->name('reports.daily');
    Route::get('/reports/daily/pdf', [\App\Http\Controllers\Admin\ReportController::class, 'dailyPdf'])->name('reports.daily.pdf');
    Route::get('/reports/monthly', [\App\Http\Controllers\Admin\ReportController::class, 'monthly'])->name('reports.monthly');
    Route::get('/reports/monthly/pdf', [\App\Http\Controllers\Admin\ReportController::class, 'monthlyPdf'])->name('reports.monthly.pdf');

    // Kesiswaan Routes
    Route::prefix('kesiswaan')->name('kesiswaan.')->group(function () {
        // Siswa Terlambat
        Route::get('/siswa-terlambat/late-students', [\App\Http\Controllers\Admin\SiswaTerlambatController::class, 'getLateStudents'])->name('siswa-terlambat.late-students');
        Route::get('/siswa-terlambat/rekap', [\App\Http\Controllers\Admin\SiswaTerlambatController::class, 'rekapSiswa'])->name('siswa-terlambat.rekap');
        Route::get('/siswa-terlambat/print/{student}', [\App\Http\Controllers\Admin\SiswaTerlambatController::class, 'printByStudent'])->name('siswa-terlambat.print');
        Route::get('/siswa-terlambat/print-by-period', [\App\Http\Controllers\Admin\SiswaTerlambatController::class, 'printByPeriod'])->name('siswa-terlambat.print-by-period');
        Route::delete('/siswa-terlambat/bulk-destroy', [\App\Http\Controllers\Admin\SiswaTerlambatController::class, 'bulkDestroy'])->name('siswa-terlambat.bulk-destroy');
        Route::resource('siswa-terlambat', \App\Http\Controllers\Admin\SiswaTerlambatController::class)->except(['create', 'show', 'edit']);

        // Pelanggaran
        Route::get('/pelanggaran/students', [\App\Http\Controllers\Admin\PelanggaranController::class, 'getStudents'])->name('pelanggaran.students');
        Route::get('/pelanggaran/print/{student}', [\App\Http\Controllers\Admin\PelanggaranController::class, 'printByStudent'])->name('pelanggaran.print');
        Route::resource('pelanggaran', \App\Http\Controllers\Admin\PelanggaranController::class)->except(['create', 'show', 'edit']);

        // Konseling
        Route::get('/konseling/students', [\App\Http\Controllers\Admin\KonselingController::class, 'getStudents'])->name('konseling.students');
        Route::get('/konseling/print/{student}', [\App\Http\Controllers\Admin\KonselingController::class, 'printByStudent'])->name('konseling.print');
        Route::resource('konseling', \App\Http\Controllers\Admin\KonselingController::class)->except(['create', 'show', 'edit']);

        // Rapor PDS
        Route::get('/rapor-pds', [\App\Http\Controllers\Admin\RaporPDSController::class, 'index'])->name('rapor-pds.index');
        Route::get('/rapor-pds/print', [\App\Http\Controllers\Admin\RaporPDSController::class, 'print'])->name('rapor-pds.print');
        Route::get('/rapor-pds/print-student/{student}', [\App\Http\Controllers\Admin\RaporPDSController::class, 'printStudent'])->name('rapor-pds.print-student');
    });

    // Kesiswaan View Routes (Read-only for Kepsek, Guru, Siswa)
    Route::prefix('kesiswaan-view')->name('kesiswaan-view.')->group(function () {
        Route::get('/siswa-terlambat', [\App\Http\Controllers\Admin\KesiswaanViewController::class, 'siswaTerlambat'])->name('siswa-terlambat');
        Route::get('/konseling', [\App\Http\Controllers\Admin\KesiswaanViewController::class, 'konseling'])->name('konseling');
        Route::get('/pelanggaran', [\App\Http\Controllers\Admin\KesiswaanViewController::class, 'pelanggaran'])->name('pelanggaran');
    });

    // Settings Routes
    Route::get('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');
    Route::get('/settings/remove-logo', [\App\Http\Controllers\Admin\SettingController::class, 'removeLogo'])->name('settings.remove-logo');
    Route::get('/settings/remove-kop-image', [\App\Http\Controllers\Admin\SettingController::class, 'removeKopImage'])->name('settings.remove-kop-image');

    // Holiday Routes
    Route::prefix('holidays')->name('holidays.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\HolidayController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\Admin\HolidayController::class, 'store'])->name('store');
        Route::put('/{id}', [\App\Http\Controllers\Admin\HolidayController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\HolidayController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/toggle', [\App\Http\Controllers\Admin\HolidayController::class, 'toggle'])->name('toggle');
    });

    // Guru Piket Routes
    Route::prefix('guru-piket')->name('guru-piket.')->group(function () {
        Route::get('/ketidakhadiran', [\App\Http\Controllers\Admin\GuruPiketController::class, 'ketidakhadiran'])->name('ketidakhadiran');
        Route::post('/ketidakhadiran', [\App\Http\Controllers\Admin\GuruPiketController::class, 'storeKetidakhadiran'])->name('ketidakhadiran.store');
        Route::put('/ketidakhadiran/{id}', [\App\Http\Controllers\Admin\GuruPiketController::class, 'updateKetidakhadiran'])->name('ketidakhadiran.update');
        Route::delete('/ketidakhadiran/{id}', [\App\Http\Controllers\Admin\GuruPiketController::class, 'destroyKetidakhadiran'])->name('ketidakhadiran.destroy');
    });

    // PKL Routes
    Route::prefix('pkl')->name('pkl.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\PklController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\Admin\PklController::class, 'store'])->name('store');
        Route::post('/set-tp', [\App\Http\Controllers\Admin\PklController::class, 'setTp'])->name('setTp');
        Route::post('/update-supervisors', [\App\Http\Controllers\Admin\PklController::class, 'updateSupervisors'])->name('updateSupervisors');
        Route::get('/students/{kelas_id}', [\App\Http\Controllers\Admin\PklController::class, 'getStudentsByKelas'])->name('students');
        Route::get('/print-nametag', [\App\Http\Controllers\Admin\PklController::class, 'printNametag'])->name('printNametag');
        Route::put('/{id}', [\App\Http\Controllers\Admin\PklController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\PklController::class, 'destroy'])->name('destroy');
        Route::get('/export-pdf', [\App\Http\Controllers\Admin\PklController::class, 'exportPdf'])->name('exportPdf');
        Route::get('/export-excel', [\App\Http\Controllers\Admin\PklController::class, 'exportExcel'])->name('exportExcel');
        Route::get('/absensi', [\App\Http\Controllers\Admin\PklController::class, 'absensi'])->name('absensi');
        Route::get('/absensi/print', [\App\Http\Controllers\Admin\PklController::class, 'printAbsensi'])->name('absensi.print');
        Route::post('/absensi/update-status', [\App\Http\Controllers\Admin\PklController::class, 'updateAbsensiStatus'])->name('absensi.update_status');

        // Komponen Penilaian Routes
        Route::prefix('komponen-nilai')->name('komponen-nilai.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\PklKomponenNilaiController::class, 'index'])->name('index');

            // Soft Skill Routes
            Route::post('/soft', [\App\Http\Controllers\Admin\PklKomponenNilaiController::class, 'storeSoft'])->name('storeSoft');
            Route::put('/soft/{id}', [\App\Http\Controllers\Admin\PklKomponenNilaiController::class, 'updateSoft'])->name('updateSoft');
            Route::delete('/soft/{id}', [\App\Http\Controllers\Admin\PklKomponenNilaiController::class, 'destroySoft'])->name('destroySoft');

            // Hard Skill Routes
            Route::post('/hard', [\App\Http\Controllers\Admin\PklKomponenNilaiController::class, 'storeHard'])->name('storeHard');
            Route::put('/hard/{id}', [\App\Http\Controllers\Admin\PklKomponenNilaiController::class, 'updateHard'])->name('updateHard');
            Route::delete('/hard/{id}', [\App\Http\Controllers\Admin\PklKomponenNilaiController::class, 'destroyHard'])->name('destroyHard');

            // Wirausaha Routes
            Route::post('/wirausaha', [\App\Http\Controllers\Admin\PklKomponenNilaiController::class, 'storeWirausaha'])->name('storeWirausaha');
            Route::put('/wirausaha/{id}', [\App\Http\Controllers\Admin\PklKomponenNilaiController::class, 'updateWirausaha'])->name('updateWirausaha');
            Route::delete('/wirausaha/{id}', [\App\Http\Controllers\Admin\PklKomponenNilaiController::class, 'destroyWirausaha'])->name('destroyWirausaha');
        });

        // Nilai PKL Routes
        Route::prefix('nilai')->name('nilai.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\PklNilaiController::class, 'index'])->name('index');
            Route::get('/show/{pklId}/{type}', [\App\Http\Controllers\Admin\PklNilaiController::class, 'show'])->name('show');
            Route::post('/bulk-update', [\App\Http\Controllers\Admin\PklNilaiController::class, 'bulkUpdate'])->name('bulk_update');
            Route::post('/', [\App\Http\Controllers\Admin\PklNilaiController::class, 'store'])->name('store');
            Route::put('/{id}', [\App\Http\Controllers\Admin\PklNilaiController::class, 'update'])->name('update');
            Route::delete('/{id}', [\App\Http\Controllers\Admin\PklNilaiController::class, 'destroy'])->name('destroy');
            Route::get('/students/{kelas_id}', [\App\Http\Controllers\Admin\PklNilaiController::class, 'getStudentsByKelas'])->name('students');
            Route::get('/komponen/{jurusan_id}', [\App\Http\Controllers\Admin\PklNilaiController::class, 'getKomponenByJurusan'])->name('komponen');
        });

        // Suket PKL Routes
        Route::prefix('suket')->name('suket.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\PklController::class, 'suket'])->name('index');
            Route::post('/config', [\App\Http\Controllers\Admin\PklController::class, 'saveSuketConfig'])->name('config');
            Route::get('/print/{id}', [\App\Http\Controllers\Admin\PklController::class, 'printSuket'])->name('print');
        });
    });

    // Dudi Routes
    Route::prefix('dudi')->name('dudi.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\DudiController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\Admin\DudiController::class, 'store'])->name('store');
        Route::get('/template', [\App\Http\Controllers\Admin\DudiController::class, 'template'])->name('template');
        Route::post('/import', [\App\Http\Controllers\Admin\DudiController::class, 'import'])->name('import');
        Route::get('/export', [\App\Http\Controllers\Admin\DudiController::class, 'export'])->name('export');
        Route::get('/export-pdf', [\App\Http\Controllers\Admin\DudiController::class, 'exportPdf'])->name('exportPdf');
        Route::put('/{id}', [\App\Http\Controllers\Admin\DudiController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\DudiController::class, 'destroy'])->name('destroy');
    });

    // Sertifikat Routes
    Route::prefix('sertifikat')->name('sertifikat.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\SertifikatController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\Admin\SertifikatController::class, 'store'])->name('store');
        Route::put('/{id}', [\App\Http\Controllers\Admin\SertifikatController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\SertifikatController::class, 'destroy'])->name('destroy');
    });


    // Siswa PKL Routes (For student level accessing via admin panel)
    Route::prefix('siswa/pkl')->name('siswa.pkl.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\SiswaPklController::class, 'dashboard'])->name('dashboard');
        Route::post('/check-in', [\App\Http\Controllers\Admin\SiswaPklController::class, 'checkIn'])->name('checkIn');
        Route::post('/check-out', [\App\Http\Controllers\Admin\SiswaPklController::class, 'checkOut'])->name('checkOut');
    });

    // Admin Jurnal Routes (For admin to view all teachers' journals)
    Route::prefix('jurnal')->name('jurnal.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AdminJurnalController::class, 'index'])->name('index');
        Route::get('/{guruId}', [\App\Http\Controllers\Admin\AdminJurnalController::class, 'show'])->name('show');
        Route::get('/{guruId}/pdf', [\App\Http\Controllers\Admin\AdminJurnalController::class, 'downloadPdf'])->name('pdf');
    });

    // Manajemen Soal Routes
    Route::prefix('soal')->name('soal.')->group(function () {
        // File Soal Routes
        Route::prefix('file-soal')->name('file-soal.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\FileSoalController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\Admin\FileSoalController::class, 'store'])->name('store');
            Route::get('/download/{id}', [\App\Http\Controllers\Admin\FileSoalController::class, 'download'])->name('download');
            Route::delete('/{id}', [\App\Http\Controllers\Admin\FileSoalController::class, 'destroy'])->name('destroy');
        });

        // Soal MID Routes
        Route::prefix('soal-mid')->name('soal-mid.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\SoalMidController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\Admin\SoalMidController::class, 'store'])->name('store');
            Route::put('/{id}', [\App\Http\Controllers\Admin\SoalMidController::class, 'update'])->name('update');
            Route::delete('/{id}', [\App\Http\Controllers\Admin\SoalMidController::class, 'destroy'])->name('destroy');
        });

        // Soal US Routes
        Route::prefix('soal-us')->name('soal-us.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\SoalUsController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\Admin\SoalUsController::class, 'store'])->name('store');
            Route::put('/{id}', [\App\Http\Controllers\Admin\SoalUsController::class, 'update'])->name('update');
            Route::delete('/{id}', [\App\Http\Controllers\Admin\SoalUsController::class, 'destroy'])->name('destroy');
        });

        // Ujian MID Routes
        Route::prefix('ujian-mid')->name('ujian-mid.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\UjianMidController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\Admin\UjianMidController::class, 'store'])->name('store');
            Route::put('/{id}', [\App\Http\Controllers\Admin\UjianMidController::class, 'update'])->name('update');
            Route::delete('/{id}', [\App\Http\Controllers\Admin\UjianMidController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/status', [\App\Http\Controllers\Admin\UjianMidController::class, 'updateStatus'])->name('updateStatus');
        });

        // Ujian US Routes
        Route::prefix('ujian-us')->name('ujian-us.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\UjianUsController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\Admin\UjianUsController::class, 'store'])->name('store');
            Route::put('/{id}', [\App\Http\Controllers\Admin\UjianUsController::class, 'update'])->name('update');
            Route::delete('/{id}', [\App\Http\Controllers\Admin\UjianUsController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/status', [\App\Http\Controllers\Admin\UjianUsController::class, 'updateStatus'])->name('updateStatus');
        });

        // Hasil MID Routes
        Route::prefix('hasil-mid')->name('hasil-mid.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\HasilMidController::class, 'index'])->name('index');
            Route::get('/{ujianId}', [\App\Http\Controllers\Admin\HasilMidController::class, 'show'])->name('show');
            Route::post('/', [\App\Http\Controllers\Admin\HasilMidController::class, 'store'])->name('store');
            Route::post('/bulk', [\App\Http\Controllers\Admin\HasilMidController::class, 'bulkStore'])->name('bulkStore');
            Route::delete('/{id}', [\App\Http\Controllers\Admin\HasilMidController::class, 'destroy'])->name('destroy');
            Route::get('/students/{kelas_id}', [\App\Http\Controllers\Admin\HasilMidController::class, 'getStudentsByKelas'])->name('students');
        });

        // Hasil US Routes
        Route::prefix('hasil-us')->name('hasil-us.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\HasilUsController::class, 'index'])->name('index');
            Route::get('/{ujianId}', [\App\Http\Controllers\Admin\HasilUsController::class, 'show'])->name('show');
            Route::post('/', [\App\Http\Controllers\Admin\HasilUsController::class, 'store'])->name('store');
            Route::post('/bulk', [\App\Http\Controllers\Admin\HasilUsController::class, 'bulkStore'])->name('bulkStore');
            Route::delete('/{id}', [\App\Http\Controllers\Admin\HasilUsController::class, 'destroy'])->name('destroy');
            Route::get('/students/{kelas_id}', [\App\Http\Controllers\Admin\HasilUsController::class, 'getStudentsByKelas'])->name('students');
        });
    });
});

// Siswa Routes (Protected by siswa middleware)
Route::prefix('siswa')->name('siswa.')->middleware('siswa')->group(function () {
    // PKL Routes
    Route::get('/pkl', [\App\Http\Controllers\Siswa\SiswaPklController::class, 'dashboard'])->name('pkl.dashboard');
    Route::post('/pkl/check-in', [\App\Http\Controllers\Siswa\SiswaPklController::class, 'checkIn'])->name('pkl.checkIn');
    Route::post('/pkl/check-out', [\App\Http\Controllers\Siswa\SiswaPklController::class, 'checkOut'])->name('pkl.checkOut');

    // Pelanggaran Routes
    Route::get('/pelanggaran', [\App\Http\Controllers\Siswa\SiswaPelanggaranController::class, 'index'])->name('pelanggaran.index');

    // Konseling Routes
    Route::get('/konseling', [\App\Http\Controllers\Siswa\SiswaKonselingController::class, 'index'])->name('konseling.index');

    // Keterlambatan Routes
    Route::get('/keterlambatan', [\App\Http\Controllers\Siswa\SiswaKeterlambatanController::class, 'index'])->name('keterlambatan.index');

    // Presensi Routes
    Route::get('/presensi', [\App\Http\Controllers\Siswa\SiswaPresensiController::class, 'index'])->name('presensi.index');
    Route::get('/presensi/grafik', [\App\Http\Controllers\Siswa\SiswaPresensiController::class, 'grafik'])->name('presensi.grafik');

    // Surat Izin Routes
    Route::prefix('surat-izin')->name('surat-izin.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Siswa\SuratIzinController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Siswa\SuratIzinController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Siswa\SuratIzinController::class, 'store'])->name('store');
        Route::get('/{id}', [\App\Http\Controllers\Siswa\SuratIzinController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [\App\Http\Controllers\Siswa\SuratIzinController::class, 'edit'])->name('edit');
        Route::put('/{id}', [\App\Http\Controllers\Siswa\SuratIzinController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Siswa\SuratIzinController::class, 'destroy'])->name('destroy');
        Route::get('/{id}/download', [\App\Http\Controllers\Siswa\SuratIzinController::class, 'download'])->name('download');
    });

    // Tugas Routes
    Route::prefix('tugas')->name('tugas.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Siswa\SiswaTugasController::class, 'index'])->name('index');
        Route::post('/{id}/submit', [\App\Http\Controllers\Siswa\SiswaTugasController::class, 'submit'])->name('submit');
    });
});
