<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing posts to have a status
        DB::table('posts')
            ->whereNotNull('published_at')
            ->update(['status' => 'published']);
        
        // Update existing comments to have a status
        DB::table('comments')->whereNotNull('created_at')->update(['status' => 'approved']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
