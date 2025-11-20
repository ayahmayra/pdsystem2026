<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add travel_grade_id column to users table
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('travel_grade_id')->nullable()->after('rank_id');
        });

        // Migrate data from user_travel_grade_maps to users table
        $mappings = DB::table('user_travel_grade_maps')->get();
        foreach ($mappings as $mapping) {
            DB::table('users')
                ->where('id', $mapping->user_id)
                ->update(['travel_grade_id' => $mapping->travel_grade_id]);
        }

        // Add foreign key constraint
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('travel_grade_id')->references('id')->on('travel_grades')->nullOnDelete();
        });

        // Drop the user_travel_grade_maps table
        Schema::dropIfExists('user_travel_grade_maps');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate user_travel_grade_maps table
        Schema::create('user_travel_grade_maps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('travel_grade_id')->constrained('travel_grades')->cascadeOnDelete();
            $table->timestamps();
            
            // Unique constraint untuk mencegah duplikasi mapping
            $table->unique('user_id');
            
            // Indexes untuk performa
            $table->index('user_id');
            $table->index('travel_grade_id');
        });

        // Migrate data back from users to user_travel_grade_maps
        $users = DB::table('users')->whereNotNull('travel_grade_id')->get();
        foreach ($users as $user) {
            DB::table('user_travel_grade_maps')->insert([
                'user_id' => $user->id,
                'travel_grade_id' => $user->travel_grade_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Remove foreign key constraint and drop column
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['travel_grade_id']);
            $table->dropColumn('travel_grade_id');
        });
    }
};
