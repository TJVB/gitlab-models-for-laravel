<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('gitlab_merge_requests', static function (Blueprint $table): void {
            $table->unsignedBigInteger('merge_request_id')->index()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gitlab_merge_requests', static function (Blueprint $table): void {
            $table->dropIndex(['merge_request_id']);
        });
    }
};
