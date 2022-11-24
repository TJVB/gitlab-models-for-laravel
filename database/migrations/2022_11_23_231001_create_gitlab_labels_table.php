<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGitlabLabelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gitlab_labels', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('label_id');
            $table->string('title');
            $table->string('color');
            $table->unsignedBigInteger('project_id')->nullable();
            $table->dateTimeTz('label_created_at');
            $table->dateTimeTz('label_updated_at');
            $table->longText('description')->nullable();
            $table->string('type');
            $table->unsignedBigInteger('group_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gitlab_labels');
    }
}
