<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGitlabMergeRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gitlab_merge_requests', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('author_id');
            $table->boolean('blocking_discussions_resolved')->default(true);
            $table->longText('description');
            $table->dateTimeTz('merge_request_created_at');
            $table->unsignedBigInteger('merge_request_id');
            $table->unsignedBigInteger('merge_request_iid');
            $table->string('merge_status');
            $table->dateTimeTz('merge_request_updated_at');
            $table->string('state');
            $table->unsignedBigInteger('source_project_id');
            $table->string('source_branch');
            $table->unsignedBigInteger('target_project_id');
            $table->string('target_branch');
            $table->string('title');
            $table->string('url');
            $table->boolean('work_in_progress')->default(false);
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
        Schema::dropIfExists('gitlab_builds');
    }
}
