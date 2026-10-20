<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengembalian', function (Blueprint $table) {
            $table->foreignId('petugas_id')->nullable()->change();
            $table->string('status')->default('diproses')->after('petugas_id');
        });
    }

    public function down(): void
    {
        Schema::table('pengembalian', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
