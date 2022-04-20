<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
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
            $table->string('name',30);
            $table->string('email',100);
            $table->string('password',100)->nullable();
            $table->enum('role',['admin','users','freelancer'])->default('users');
            $table->enum('verified',['verified','unverified'])->default('unverified');
            $table->string('mobilenumber',100)->nullable();
            $table->string('img',100)->nullable();
            $table->string('adress',100)->nullable();
            $table->string('id_card',100)->nullable();
            $table->boolean('record_deleted')->default(0);
            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id')->references('id')->on('categories');
            
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
        Schema::dropIfExists('users');
    }
}
