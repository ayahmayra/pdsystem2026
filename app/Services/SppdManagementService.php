<?php

namespace App\Services;

use App\Models\NotaDinas;
use App\Models\Spt;
use App\Models\Sppd;
use App\Models\NotaDinasParticipant;
use App\Services\DocumentNumberService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SppdManagementService
{
    protected $documentNumberService;

    public function __construct(DocumentNumberService $documentNumberService)
    {
        $this->documentNumberService = $documentNumberService;
    }

    /**
     * Sync SPPD documents when Nota Dinas participants change
     */
    public function syncSppdForNotaDinas(NotaDinas $notaDinas)
    {
        // Only proceed if Nota Dinas has an SPT
        if (!$notaDinas->spt) {
            return;
        }

        $spt = $notaDinas->spt;
        $currentParticipants = $notaDinas->participants;
        $existingSppds = $spt->sppds;

        // Get existing SPPD user IDs
        $existingSppdUserIds = $existingSppds->pluck('user_id')->toArray();
        
        // Get current participant user IDs
        $currentParticipantUserIds = $currentParticipants->pluck('user_id')->toArray();

        // Find participants that need SPPD (new participants)
        $participantsNeedingSppd = $currentParticipants->whereNotIn('user_id', $existingSppdUserIds);

        // Find SPPDs that need to be removed (participants no longer in Nota Dinas)
        $sppdsToRemove = $existingSppds->whereNotIn('user_id', $currentParticipantUserIds);

        DB::beginTransaction();
        try {
            // Remove SPPDs for participants no longer in Nota Dinas
            foreach ($sppdsToRemove as $sppd) {
                $this->removeSppd($sppd);
            }

            // Create SPPDs for new participants
            foreach ($participantsNeedingSppd as $participant) {
                $this->createSppdForParticipant($spt, $participant, $notaDinas);
            }

            DB::commit();
            
            Log::info("SPPD sync completed for Nota Dinas ID: {$notaDinas->id}", [
                'created' => $participantsNeedingSppd->count(),
                'removed' => $sppdsToRemove->count()
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to sync SPPD for Nota Dinas ID: {$notaDinas->id}", [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Create SPPD for a specific participant
     */
    protected function createSppdForParticipant(Spt $spt, NotaDinasParticipant $participant, NotaDinas $notaDinas)
    {
        // Generate document number using the correct method
        $docNumberResult = DocumentNumberService::generate('SPPD', $spt->unit_id, now(), [
            'doc_id' => null, // Will be updated after SPPD creation
            'nota_dinas_id' => $notaDinas->id,
            'spt_id' => $spt->id,
            'user_id' => $participant->user_id
        ]);

        $sppd = Sppd::create([
            'doc_no' => $docNumberResult['number'],
            'sppd_date' => now(),
            'spt_id' => $spt->id,
            'user_id' => $participant->user_id,
            'origin_place_id' => $notaDinas->requestingUnit->orgPlace->id ?? null,
            'destination_city_id' => $notaDinas->destination_city_id,

            'funding_source' => 'APBD', // Default value, can be made configurable
            'status' => 'ACTIVE',
            'number_format_id' => $docNumberResult['format']->id,
            'number_sequence_id' => $docNumberResult['sequence']->id,
            'number_scope_unit_id' => $spt->unit_id,
        ]);

        // Update the audit trail with the actual SPPD ID (if exists)
        if ($docNumberResult['audit']) {
            $docNumberResult['audit']->update(['doc_id' => $sppd->id]);
        }

        Log::info("SPPD created for participant", [
            'sppd_id' => $sppd->id,
            'user_id' => $participant->user_id,
            'spt_id' => $spt->id,
            'doc_number' => $docNumberResult['number']
        ]);

        return $sppd;
    }

    /**
     * Remove SPPD and related data
     */
    protected function removeSppd(Sppd $sppd)
    {
        // Check if SPPD can be safely removed (no related data)
        if ($sppd->receipt || $sppd->itineraries->count() > 0) {
            Log::warning("Cannot remove SPPD due to existing related data", [
                'sppd_id' => $sppd->id,
                'has_receipt' => (bool) $sppd->receipt,
                'itinerary_count' => $sppd->itineraries->count()
            ]);
            return false;
        }

        $sppd->delete();
        
        Log::info("SPPD removed", [
            'sppd_id' => $sppd->id,
            'user_id' => $sppd->user_id
        ]);

        return true;
    }



    /**
     * Check if SPPD sync is needed for a Nota Dinas
     */
    public function needsSppdSync(NotaDinas $notaDinas): bool
    {
        if (!$notaDinas->spt) {
            return false;
        }

        $currentParticipantUserIds = $notaDinas->participants->pluck('user_id')->toArray();
        $existingSppdUserIds = $notaDinas->spt->sppds->pluck('user_id')->toArray();

        // Check if there are new participants without SPPD
        $newParticipants = array_diff($currentParticipantUserIds, $existingSppdUserIds);
        
        // Check if there are SPPDs for participants no longer in Nota Dinas
        $removedParticipants = array_diff($existingSppdUserIds, $currentParticipantUserIds);

        return !empty($newParticipants) || !empty($removedParticipants);
    }

    /**
     * Get sync status for a Nota Dinas
     */
    public function getSyncStatus(NotaDinas $notaDinas): array
    {
        if (!$notaDinas->spt) {
            return [
                'needs_sync' => false,
                'message' => 'No SPT found for this Nota Dinas'
            ];
        }

        $currentParticipantUserIds = $notaDinas->participants->pluck('user_id')->toArray();
        $existingSppdUserIds = $notaDinas->spt->sppds->pluck('user_id')->toArray();

        $newParticipants = array_diff($currentParticipantUserIds, $existingSppdUserIds);
        $removedParticipants = array_diff($existingSppdUserIds, $currentParticipantUserIds);

        $needsSync = !empty($newParticipants) || !empty($removedParticipants);

        return [
            'needs_sync' => $needsSync,
            'new_participants_count' => count($newParticipants),
            'removed_participants_count' => count($removedParticipants),
            'message' => $needsSync 
                ? "SPPD sync needed: " . count($newParticipants) . " new, " . count($removedParticipants) . " removed"
                : "SPPD documents are in sync"
        ];
    }
}
