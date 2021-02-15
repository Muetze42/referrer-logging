<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReferrersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('referrer_hosts', function (Blueprint $table) {
            $table->id();
            $table->string('host');
            $table->unsignedInteger('count')->default(0);
            $table->timestamps();
        });

        Schema::create('referrers', function (Blueprint $table) {
            $table->id();
            $table->string('referrer');
            $table->string('target');
            $table->foreignId('host_id')->constrained('referrer_hosts', 'id')->onDelete('cascade');
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
        Schema::dropIfExists('referrers');
    }
}
