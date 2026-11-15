<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Menambahkan kolom role dengan default 'user'
            $table->string('role', 20)->default('user')->after('password');
        });

        // Sinkronisasi data user lama: jika role_id adalah 2 (admin), set role = 'admin'
        DB::table('users')
            ->where('role_id', 2)
            ->orWhere('name', 'admin')
            ->update(['role' => 'admin']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Menghapus kolom role jika rollback
            $table->dropColumn('role');
        });
    }
};
