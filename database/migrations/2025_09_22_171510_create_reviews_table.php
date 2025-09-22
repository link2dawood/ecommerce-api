<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
             $table->unsignedBigInteger('product_id');
        $table->unsignedBigInteger('user_id');
         $table->integer('rating'); // 1–5
        $table->string('title')->nullable();
        $table->text('comment')->nullable();
        $table->boolean('is_approved')->default(false);
            $table->timestamps();
            $table->foreign('product_id')
              ->references('id')
              ->on('products')
              ->onDelete('cascade');

        $table->foreign('user_id')
              ->references('id')
              ->on('users')
              ->onDelete('cascade');
               $table->unique(['user_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reviews');
    }
}
