<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('konten_statis', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('text');
            $table->timestamps();
        });

        // contoh seed awal
        DB::table('konten_statis')->insert([
            ['key' => 'site_logo', 'value' => '/storage/logo.png', 'type' => 'image', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'hero_title', 'value' => 'Selamat datang di NotezQue', 'type' => 'text', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'hero_sub', 'value' => 'Solusi pengelolaan tugas & aktivitas mahasiswa', 'type' => 'text', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
    public function down()
    {
        Schema::dropIfExists('konten_statis');
    }
};
