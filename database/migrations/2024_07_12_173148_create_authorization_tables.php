<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
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
            $table->id();
            $table->string('name', 50)->unique();
            $table->string('slug', 50)->unique();
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
            $table->unsignedBigInteger('menu_id')->default(0);
            $table->string('name', 50)->unique();
            $table->string('method', 50);
            $table->string('uri');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create(config('elegant-utils.authorization.role_permission_relational.table'), function (Blueprint $table) {
            $table->unsignedBigInteger(config('elegant-utils.authorization.role_permission_relational.role_id'))->index();
            $table->unsignedBigInteger(config('elegant-utils.authorization.role_permission_relational.permission_id'))->index();
            $table->timestamps();
        });

        Schema::create(config('elegant-utils.authorization.user_permission_relational.table'), function (Blueprint $table) {
            $table->unsignedBigInteger(config('elegant-utils.authorization.user_permission_relational.user_id'))->index();
            $table->unsignedBigInteger(config('elegant-utils.authorization.user_permission_relational.permission_id'))->index();
            $table->timestamps();
        });

        Schema::create(config('elegant-utils.authorization.role_menu_relational.table'), function (Blueprint $table) {
            $table->unsignedBigInteger(config('elegant-utils.authorization.role_menu_relational.role_id'))->index();
            $table->unsignedBigInteger(config('elegant-utils.authorization.role_menu_relational.menu_id'))->index();
            $table->timestamps();
        });

        Schema::create(config('elegant-utils.authorization.user_menu_relational.table'), function (Blueprint $table) {
            $table->unsignedBigInteger(config('elegant-utils.authorization.user_menu_relational.user_id'))->index();
            $table->unsignedBigInteger(config('elegant-utils.authorization.user_menu_relational.menu_id'))->index();
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
        Schema::dropIfExists(config('elegant-utils.authorization.permissions.table'));
        Schema::dropIfExists(config('elegant-utils.authorization.role_permission_relational.table'));
        Schema::dropIfExists(config('elegant-utils.authorization.user_permission_relational.table'));
        Schema::dropIfExists(config('elegant-utils.authorization.role_menu_relational.table'));
        Schema::dropIfExists(config('elegant-utils.authorization.user_menu_relational.table'));
    }
};
