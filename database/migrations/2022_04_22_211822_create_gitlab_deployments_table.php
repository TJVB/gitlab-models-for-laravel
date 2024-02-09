<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

final class CreateGitlabDeploymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gitlab_deployments', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('deployment_id');
            $table->unsignedBigInteger('deployable_id');
            $table->string('deployable_url')->default('');
            $table->string('environment')->default('');
            $table->string('status')->default('');
            $table->dateTimeTz('status_changed_at');
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
