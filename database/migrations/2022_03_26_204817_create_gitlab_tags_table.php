<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGitlabTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gitlab_tags', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->string('ref');
            $table->string('checkout_sha')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['project_id', 'ref', 'deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gitlab_issues');
    }
}
