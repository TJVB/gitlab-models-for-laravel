<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

final class UpdateGitlabMergeRequestTableForOptionalFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('gitlab_merge_requests', static function (Blueprint $table) {
            $table->unsignedBigInteger('author_id')->nullable()->change();
            $table->dateTimeTz('merge_request_created_at')->nullable()->change();
            $table->dateTimeTz('merge_request_updated_at')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('gitlab_merge_requests', static function (Blueprint $table) {
            $table->unsignedBigInteger('author_id')->change();
            $table->dateTimeTz('merge_request_created_at')->change();
            $table->dateTimeTz('merge_request_updated_at')->change();
        });
    }
}
