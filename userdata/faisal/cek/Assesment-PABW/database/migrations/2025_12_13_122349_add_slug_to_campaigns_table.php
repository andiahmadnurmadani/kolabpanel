<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // PERBAIKAN: Cek dulu apakah kolom 'slug' sudah ada
        if (!Schema::hasColumn('campaigns', 'slug')) {
            Schema::table('campaigns', function (Blueprint $table) {
                $table->string('slug')->unique()->nullable()->after('id');
            });
        }
    }

    public function down()
    {
        Schema::table('campaigns', function (Blueprint $table) {
            if (Schema::hasColumn('campaigns', 'slug')) {
                $table->dropColumn('slug');
            }
        });
    }
};