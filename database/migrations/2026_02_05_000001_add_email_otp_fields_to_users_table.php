<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * AC Chill - Email OTP Fields Migration
 * Adds OTP-based email verification and password reset fields to users table
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Email verification OTP fields
            $table->string('email_otp', 6)->nullable()->after('email');
            $table->timestamp('email_otp_expires_at')->nullable()->after('email_otp');
            $table->boolean('is_email_verified')->default(0)->after('email_otp_expires_at');

            // Password reset OTP fields
            $table->string('reset_otp', 6)->nullable()->after('is_email_verified');
            $table->timestamp('reset_otp_expires_at')->nullable()->after('reset_otp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'email_otp',
                'email_otp_expires_at',
                'is_email_verified',
                'reset_otp',
                'reset_otp_expires_at'
            ]);
        });
    }
};
