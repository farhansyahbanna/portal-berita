<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing posts to have a status
        DB::table('posts')->whereNull('status')->update(['status' => 'published']);
        
        // Update existing comments to have a status
        DB::table('comments')->whereNull('status')->update(['status' => 'approved']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
