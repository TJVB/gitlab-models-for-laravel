<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

final class CreateGitlabLabelsTable extends Migration
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
            $table->string('title')->default('');
            $table->string('color')->default('');
            $table->unsignedBigInteger('project_id')->nullable();
            $table->dateTimeTz('label_created_at')->nullable();
            $table->dateTimeTz('label_updated_at')->nullable();
            $table->longText('description')->nullable();
            $table->string('type')->default('');
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
