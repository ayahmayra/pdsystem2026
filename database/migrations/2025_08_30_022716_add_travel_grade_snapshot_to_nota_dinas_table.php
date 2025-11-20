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
        Schema::table('nota_dinas', function (Blueprint $table) {
            // Add travel grade snapshot fields
            $table->unsignedBigInteger('travel_grade_id_snapshot')->nullable()->after('custom_signer_title');
            $table->string('travel_grade_code_snapshot')->nullable()->after('travel_grade_id_snapshot');
            $table->string('travel_grade_name_snapshot')->nullable()->after('travel_grade_code_snapshot');
        });

        // Add foreign key constraint
        Schema::table('nota_dinas', function (Blueprint $table) {
            $table->foreign('travel_grade_id_snapshot')->references('id')->on('travel_grades')->nullOnDelete();
        });

        // Populate snapshot data for existing nota dinas records
        $notaDinas = DB::table('nota_dinas')->get();
        foreach ($notaDinas as $notaDina) {
            // Get travel grade from the first participant (assuming all participants have same travel grade)
            $firstParticipant = DB::table('nota_dinas_participants')
                ->where('nota_dinas_id', $notaDina->id)
                ->first();

            if ($firstParticipant) {
                $user = DB::table('users')->where('id', $firstParticipant->user_id)->first();
                if ($user && $user->travel_grade_id) {
                    $travelGrade = DB::table('travel_grades')->where('id', $user->travel_grade_id)->first();
                    if ($travelGrade) {
                        DB::table('nota_dinas')
                            ->where('id', $notaDina->id)
                            ->update([
                                'travel_grade_id_snapshot' => $travelGrade->id,
                                'travel_grade_code_snapshot' => $travelGrade->code,
                                'travel_grade_name_snapshot' => $travelGrade->name,
                            ]);
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nota_dinas', function (Blueprint $table) {
            $table->dropForeign(['travel_grade_id_snapshot']);
            $table->dropColumn([
                'travel_grade_id_snapshot',
                'travel_grade_code_snapshot',
                'travel_grade_name_snapshot'
            ]);
        });
    }
};
