<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Refactor pkl_nilai to pkl_softnilai
        if (Schema::hasTable('pkl_nilai') && !Schema::hasTable('pkl_softnilai')) {
            Schema::rename('pkl_nilai', 'pkl_softnilai');
        }

        Schema::table('pkl_softnilai', function (Blueprint $table) {
            // Add pkl_id column if not exists
            if (!Schema::hasColumn('pkl_softnilai', 'pkl_id')) {
                $table->foreignId('pkl_id')->nullable()->after('student_id')->constrained('pkls')->onDelete('cascade');
            }

            // Drop hard skill relation
            // We consciously try both names or check existence because of the half-migration state
            // Explicitly try to drop the old FK name first
            try {
                $table->dropForeign('pkl_nilai_pkl_komphard_id_foreign');
            } catch (\Exception $e) {
                // Ignore if not found, rely on next check or implicit logic
            }

            if (Schema::hasColumn('pkl_softnilai', 'pkl_komphard_id')) {
                // Try implicit drop if explicit failed or if it was renamed (unlikely in this context but good practice)
                // Or just drop the column which will likely error if FK exists.
                // We'll wrap in array which tries to drop FK if exists.
                // BUT Laravel assumes table name in FK unless specified.
                // We already tried dropping the old named FK above.
                $table->dropColumn('pkl_komphard_id');
            }
        });

        // 2. Refactor pkl_komponennlai to pkl_hardnilai
        if (Schema::hasTable('pkl_komponennlai') && !Schema::hasTable('pkl_hardnilai')) {
            Schema::rename('pkl_komponennlai', 'pkl_hardnilai');
        } elseif (!Schema::hasTable('pkl_hardnilai')) {
            // Fallback if not found, create new
            Schema::create('pkl_hardnilai', function (Blueprint $table) {
                $table->id();
                $table->timestamps();
            });
        }

        Schema::table('pkl_hardnilai', function (Blueprint $table) {
            // Drop old columns if they exist
            if (Schema::hasColumn('pkl_hardnilai', 'jurusan_id')) {
                // FK name assumption: pkl_komponennlai_jurusan_id_foreign
                try {
                    $table->dropForeign('pkl_komponennlai_jurusan_id_foreign');
                } catch (\Exception $e) {
                }
                $table->dropColumn('jurusan_id');
            }
            if (Schema::hasColumn('pkl_hardnilai', 'komp_sof')) {
                $table->dropColumn('komp_sof');
            }
            if (Schema::hasColumn('pkl_hardnilai', 'komp_hard')) {
                $table->dropColumn('komp_hard');
            }

            // Add new columns
            if (!Schema::hasColumn('pkl_hardnilai', 'student_id')) {
                $table->foreignId('student_id')->after('id')->constrained('students')->onDelete('cascade');
            }
            if (!Schema::hasColumn('pkl_hardnilai', 'pkl_id')) {
                $table->foreignId('pkl_id')->nullable()->after('student_id')->constrained('pkls')->onDelete('cascade');
            }
            if (!Schema::hasColumn('pkl_hardnilai', 'pkl_komphard_id')) {
                $table->foreignId('pkl_komphard_id')->nullable()->after('pkl_id')->constrained('pkl_komphards')->onDelete('cascade');
            }
            if (!Schema::hasColumn('pkl_hardnilai', 'nilai')) {
                $table->decimal('nilai', 5, 2)->nullable()->after('pkl_komphard_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse pkl_hardnilai to pkl_komponennlai
        if (Schema::hasTable('pkl_hardnilai')) {
            Schema::table('pkl_hardnilai', function (Blueprint $table) {
                if (Schema::hasColumn('pkl_hardnilai', 'student_id')) {
                    $table->dropForeign(['student_id']);
                    $table->dropColumn('student_id');
                }
                if (Schema::hasColumn('pkl_hardnilai', 'pkl_id')) {
                    $table->dropForeign(['pkl_id']);
                    $table->dropColumn('pkl_id');
                }
                if (Schema::hasColumn('pkl_hardnilai', 'pkl_komphard_id')) {
                    $table->dropForeign(['pkl_komphard_id']);
                    $table->dropColumn('pkl_komphard_id');
                }
                if (Schema::hasColumn('pkl_hardnilai', 'nilai')) {
                    $table->dropColumn('nilai');
                }

                // Add back old columns
                $table->unsignedBigInteger('jurusan_id')->nullable();
                $table->text('komp_sof')->nullable();
                $table->text('komp_hard')->nullable();
            });
            Schema::rename('pkl_hardnilai', 'pkl_komponennlai');
        }

        // Reverse pkl_softnilai to pkl_nilai
        if (Schema::hasTable('pkl_softnilai')) {
            Schema::table('pkl_softnilai', function (Blueprint $table) {
                if (Schema::hasColumn('pkl_softnilai', 'pkl_id')) {
                    $table->dropForeign(['pkl_id']);
                    $table->dropColumn('pkl_id');
                }

                $table->foreignId('pkl_komphard_id')->nullable()->constrained('pkl_komphards')->onDelete('cascade');
            });
            Schema::rename('pkl_softnilai', 'pkl_nilai');
        }
    }
};
