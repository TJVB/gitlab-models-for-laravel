<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGitlabIssueGitlabLabelTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('gitlab_issue_gitlab_label', static function (Blueprint $table) {
            $table->unsignedBigInteger('issue_id');
            $table->unsignedBigInteger('label_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('gitlab_issue_gitlab_label');
    }
}
