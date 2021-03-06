<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserSecuritySettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_security_settings', function (Blueprint $table) {
          $table->unsignedInteger('id');
          $table->unsignedInteger('email_verified')->default(0);
          $table->string('email_verification_code')->nullable();
          $table->unsignedInteger('otp_verified')->default(0);
          $table->unsignedInteger('phone_verified')->default(0);
          $table->timestamps();

          $table->unique('id');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_security_settings');
    }
}
