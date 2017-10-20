<?php

use Elimuswift\Tenancy\Abstracts\AbstractMigration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenancyTables extends AbstractMigration
{
    protected $system = true;

    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->string('name', 255);
            $table->string('email', 255);
            $table->string('slug', 255)->nullable();
            $table->string('city', 255)->nullable();
            $table->string('mobile', 255)->nullable();
            $table->string('secondary_email', 255)->nullable();
            $table->integer('currency_id')->nullable()->unsigned();
            $table->string('tax', 255)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->softDeletes();
        });
        Schema::create('websites', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('uuid');
            $table->bigInteger('customer_id')->unsigned()->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('customer_id')->references('id')->on('customers');
        });
        Schema::create('hostnames', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('fqdn')->unique();
            $table->string('redirect_to')->nullable();
            $table->boolean('force_https')->default(false);
            $table->timestamp('under_maintenance_since')->nullable();
            $table->bigInteger('website_id')->unsigned()->nullable();
            $table->bigInteger('customer_id')->unsigned()->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('website_id')->references('id')->on('websites')->onDelete('set null');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('hostnames');
        Schema::dropIfExists('websites');
        Schema::dropIfExists('customers');
    }
}
