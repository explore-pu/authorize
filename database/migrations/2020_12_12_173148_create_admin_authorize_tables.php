<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminAuthorizeTables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function getConnection()
    {
        return config('elegant-utils.admin.database.connection') ?: config('database.default');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('elegant-utils.authorization.roles_table'), function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50)->unique();
            $table->string('slug', 50)->unique();
            $table->text('permissions')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create(config('elegant-utils.authorization.role_users_table'), function (Blueprint $table) {
            $table->integer('user_id');
            $table->integer('role_id');
            $table->index(['user_id', 'role_id']);
            $table->timestamps();
        });

        Schema::table(config('elegant-utils.admin.database.users_table'), function (Blueprint $table) {
            $table->text('permissions')->nullable()->after('avatar');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('elegant-utils.authorization.roles_table'));
        Schema::dropIfExists(config('elegant-utils.authorization.role_users_table'));
    }
}
