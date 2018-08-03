<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('results', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->increments('id');
            $table->integer('keyword_id')->unsigned();
            $table->char('search_engine', 16);
            $table->text('title');
            $table->text('description')->nullable();
            $table->text('url');
            $table->text('url_redirected')->nullable();
            $table->string('url_category')->nullable();
            $table->char('url_status', 32)->nullable();
            $table->ipAddress('ip')->nullable();
            $table->char('ip_region', 128)->nullable();
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
        Schema::dropIfExists('results');
    }
}
