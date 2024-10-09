<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('content');
            $table->string('image_path')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('last_update')->useCurrent()->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('posts');
    }
}
