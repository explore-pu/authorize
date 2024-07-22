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
        Schema::create(config('elegant-utils.authorization.roles.table'), function (Blueprint $table) {
            $table->id('id');
            $table->string('name', 50)->unique();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create(config('elegant-utils.authorization.user_role_relational.table'), function (Blueprint $table) {
            $table->unsignedBigInteger(config('elegant-utils.authorization.user_role_relational.user_id'))->index();
            $table->unsignedBigInteger(config('elegant-utils.authorization.user_role_relational.role_id'))->index();
            $table->timestamps();
        });

        Schema::create(config('elegant-utils.authorization.permissions.table'), function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
            $table->string('method', 10);
            $table->string('uri');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create(config('elegant-utils.authorization.user_permission_relational.table'), function (Blueprint $table) {
            $table->unsignedBigInteger(config('elegant-utils.authorization.user_permission_relational.user_id'))->index();
            $table->unsignedBigInteger(config('elegant-utils.authorization.user_permission_relational.permission_id'))->index();
            $table->timestamps();
        });

        Schema::create(config('elegant-utils.authorization.role_permission_relational.table'), function (Blueprint $table) {
            $table->unsignedBigInteger(config('elegant-utils.authorization.role_permission_relational.role_id'))->index();
            $table->unsignedBigInteger(config('elegant-utils.authorization.role_permission_relational.permission_id'))->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('elegant-utils.authorization.roles.table'));
        Schema::dropIfExists(config('elegant-utils.authorization.user_role_relational.table'));
        Schema::dropIfExists(config('elegant-utils.authorization.user_permission_relational.table'));
        Schema::dropIfExists(config('elegant-utils.authorization.role_permission_relational.table'));
    }
}
