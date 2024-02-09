<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

final class CreateGitlabPipelinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gitlab_pipelines', static function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('duration');
            $table->dateTimeTz('pipeline_created_at');
            $table->dateTimeTz('pipeline_finished_at')->nullable();
            $table->unsignedBigInteger('pipeline_id');
            $table->unsignedBigInteger('project_id')->nullable();
            $table->string('ref')->default('');
            $table->string('sha')->default('');
            $table->string('source')->default('');
            $table->json('stages');
            $table->string('status')->default('');
            $table->boolean('tag')->default(false);
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
        Schema::dropIfExists('gitlab_pipelines');
    }
}
