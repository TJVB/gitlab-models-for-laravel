<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGitlabNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gitlab_notes', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('author_id');
            $table->string('commit_id')->nullable();
            $table->string('line_code')->nullable();
            $table->longText('note');
            $table->dateTimeTz('note_created_at');
            $table->dateTimeTz('note_updated_at');
            $table->unsignedBigInteger('note_id');
            $table->unsignedBigInteger('noteable_id')->nullable();
            $table->string('noteable_type');
            $table->unsignedBigInteger('project_id')->nullable();
            $table->string('url');
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
        Schema::dropIfExists('gitlab_notes');
    }
}
