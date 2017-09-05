<?php
use Elimuswift\Tenancy\Abstracts\AbstractMigration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TenancyCustomers extends AbstractMigration
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
    }

    public function down()
    {
        Schema::dropIfExists('customers');
    }
}
