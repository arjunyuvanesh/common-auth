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
        // Safety check: If the users table doesn't exist at all, create an empty one first
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {});
        }

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'id')) {
                $table->id();
            }
            if (!Schema::hasColumn('users', 'name')) {
                $table->string('name');
            }
            if (!Schema::hasColumn('users', 'username')) {
                $table->string('username')->nullable()->unique();
            }
            if (!Schema::hasColumn('users', 'email')) {
                $table->string('email')->nullable()->unique();
            } else {
                // If it exists, ensure it is nullable
                $table->string('email')->nullable()->change();
            }
            if (!Schema::hasColumn('users', 'mobile')) {
                $table->string('mobile')->nullable()->unique();
            }
            if (!Schema::hasColumn('users', 'email_verified_at')) {
                $table->timestamp('email_verified_at')->nullable();
            }
            if (!Schema::hasColumn('users', 'password')) {
                $table->string('password');
            }
            if (!Schema::hasColumn('users', 'remember_token')) {
                $table->rememberToken();
            }
            if (!Schema::hasColumn('users', 'created_at') && !Schema::hasColumn('users', 'updated_at')) {
                $table->timestamps();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Best Practice: We ONLY drop the columns that are exclusive to our package.
            // Dropping standard columns like 'name' or 'password' would destroy the host application's data.
            
            if (Schema::hasColumn('users', 'username')) {
                $table->dropColumn('username');
            }
            
            if (Schema::hasColumn('users', 'mobile')) {
                $table->dropColumn('mobile');
            }

            if (Schema::hasColumn('users', 'email')) {
                // Revert email back to required (not nullable)
                $table->string('email')->nullable(false)->change();
            }
        });
    }
};
