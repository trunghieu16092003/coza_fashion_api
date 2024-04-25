<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->integer('role')->default(0); // Sử dụng phương thức integer() để định nghĩa kiểu dữ liệu số nguyên
            $table->integer('is_active')->default(0); // Sử dụng phương thức integer() để định nghĩa kiểu dữ liệu số nguyên
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'address', 'role', 'is_active']);
        });
    }
};
