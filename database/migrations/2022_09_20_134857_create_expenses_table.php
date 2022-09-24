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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('merchant');
            $table->double('total_amount', 20, 2);
            $table->timestamp('date');
            $table->text('comment')->nullable();
            $table->text('receipt_url')->nullable();
            $table->enum('status', ['Reimburse', 'New', 'In Progress'])->default('New');
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
        Schema::dropIfExists('expenses');
    }
};
