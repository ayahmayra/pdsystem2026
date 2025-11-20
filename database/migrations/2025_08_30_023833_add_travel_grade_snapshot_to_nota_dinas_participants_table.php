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
        Schema::table('nota_dinas_participants', function (Blueprint $table) {
            // Add travel grade snapshot fields
            $table->unsignedBigInteger('user_travel_grade_id_snapshot')->nullable()->after('user_position_echelon_id_snapshot');
            $table->string('user_travel_grade_code_snapshot')->nullable()->after('user_travel_grade_id_snapshot');
            $table->string('user_travel_grade_name_snapshot')->nullable()->after('user_travel_grade_code_snapshot');
        });

        // Add foreign key constraint
        Schema::table('nota_dinas_participants', function (Blueprint $table) {
            $table->foreign('user_travel_grade_id_snapshot')->references('id')->on('travel_grades')->nullOnDelete();
        });

        // Populate snapshot data for existing participants
        $participants = DB::table('nota_dinas_participants')->get();
        foreach ($participants as $participant) {
            $user = DB::table('users')->where('id', $participant->user_id)->first();
            if ($user && $user->travel_grade_id) {
                $travelGrade = DB::table('travel_grades')->where('id', $user->travel_grade_id)->first();
                if ($travelGrade) {
                    DB::table('nota_dinas_participants')
                        ->where('id', $participant->id)
                        ->update([
                            'user_travel_grade_id_snapshot' => $travelGrade->id,
                            'user_travel_grade_code_snapshot' => $travelGrade->code,
                            'user_travel_grade_name_snapshot' => $travelGrade->name,
                        ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nota_dinas_participants', function (Blueprint $table) {
            $table->dropForeign(['user_travel_grade_id_snapshot']);
            $table->dropColumn([
                'user_travel_grade_id_snapshot',
                'user_travel_grade_code_snapshot',
                'user_travel_grade_name_snapshot'
            ]);
        });
    }
};
