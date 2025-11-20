<?php

namespace App\Livewire\Users;

use App\Models\User;
use App\Helpers\PermissionHelper;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app', ['title' => 'Data Pegawai'])]

class Index extends Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function delete(User $user)
    {
        // Check if user has permission to delete users
        if (!PermissionHelper::can('users.delete')) {
            session()->flash('error', 'Anda tidak memiliki izin untuk menghapus user.');
            return;
        }

        // Check if user is superadmin and current user is not superadmin
        if ($user->isSuperAdmin() && !\Illuminate\Support\Facades\Auth::user()->isSuperAdmin()) {
            session()->flash('error', 'User dengan role superadmin hanya dapat dihapus oleh superadmin lainnya.');
            return;
        }
        
        // Check if user is used in any active documents (only non-deleted documents)
        if ($user->isUsedInDocuments()) {
            $allInvolvements = $user->getAllDocumentInvolvements();
            $involvementTexts = [];
            
            // Process nota dinas involvements
            if (isset($allInvolvements['nota_dinas'])) {
                $notaDinasText = collect($allInvolvements['nota_dinas'])->map(function ($involvement) {
                    return $involvement['type'] . ' (' . $involvement['count'] . ' dokumen)';
                })->join(', ');
                $involvementTexts[] = 'dokumen nota dinas sebagai: ' . $notaDinasText;
            }
            
            // Process sub kegiatan involvements
            if (isset($allInvolvements['sub_kegiatan'])) {
                $subKegiatanText = collect($allInvolvements['sub_kegiatan'])->map(function ($involvement) {
                    return $involvement['type'] . ' (' . $involvement['count'] . ' sub kegiatan)';
                })->join(', ');
                $involvementTexts[] = 'sub kegiatan sebagai: ' . $subKegiatanText;
            }
            
            // Process receipts involvements
            if (isset($allInvolvements['receipts'])) {
                $receiptsText = collect($allInvolvements['receipts'])->map(function ($involvement) {
                    return $involvement['type'] . ' (' . $involvement['count'] . ' kwitansi)';
                })->join(', ');
                $involvementTexts[] = 'kwitansi sebagai: ' . $receiptsText;
            }
            
            $fullInvolvementText = implode(' dan ', $involvementTexts);
            session()->flash('error', 'Data pegawai tidak dapat dihapus karena masih digunakan dalam ' . $fullInvolvementText . '.');
            return;
        }
        
        // Check for foreign key constraint violation and clean up soft deleted references
        if ($user->willCauseForeignKeyViolation()) {
            // Try to clean up soft deleted references
            $cleanedCount = $user->cleanupSoftDeletedReferences();
            
            if ($cleanedCount > 0) {
                session()->flash('message', "Berhasil membersihkan {$cleanedCount} referensi dari dokumen yang sudah dihapus. Mencoba menghapus user...");
            }
            
            // Check again after cleanup
            if ($user->willCauseForeignKeyViolation()) {
                session()->flash('error', 'Data pegawai tidak dapat dihapus karena masih memiliki referensi dalam dokumen aktif. Silakan hapus atau edit dokumen yang masih menggunakan user ini terlebih dahulu.');
                return;
            }
        }
        
        try {
            $user->delete();
            session()->flash('message', 'Data pegawai berhasil dihapus.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus data pegawai. ' . $e->getMessage());
        }
    }

    public function render()
    {
        $users = User::with(['unit', 'position.echelon', 'rank', 'travelGrade', 'roles'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    // Search in basic user fields
                    $q->where('users.name', 'like', '%' . $this->search . '%')
                      ->orWhere('users.email', 'like', '%' . $this->search . '%')
                      ->orWhere('users.nip', 'like', '%' . $this->search . '%')
                      ->orWhere('users.nik', 'like', '%' . $this->search . '%')
                      ->orWhere('users.gelar_depan', 'like', '%' . $this->search . '%')
                      ->orWhere('users.gelar_belakang', 'like', '%' . $this->search . '%')
                      ->orWhere('users.position_desc', 'like', '%' . $this->search . '%')
                      ->orWhere('users.phone', 'like', '%' . $this->search . '%')
                      ->orWhere('users.whatsapp', 'like', '%' . $this->search . '%')
                      // Search in unit relationship
                      ->orWhereHas('unit', function ($unitQuery) {
                          $unitQuery->where('name', 'like', '%' . $this->search . '%')
                                   ->orWhere('code', 'like', '%' . $this->search . '%');
                      })
                      // Search in position relationship
                      ->orWhereHas('position', function ($positionQuery) {
                          $positionQuery->where('name', 'like', '%' . $this->search . '%')
                                       ->orWhere('type', 'like', '%' . $this->search . '%')
                                       // Search in echelon through position
                                       ->orWhereHas('echelon', function ($echelonQuery) {
                                           $echelonQuery->where('name', 'like', '%' . $this->search . '%')
                                                       ->orWhere('code', 'like', '%' . $this->search . '%');
                                       });
                      })
                      // Search in rank relationship
                      ->orWhereHas('rank', function ($rankQuery) {
                          $rankQuery->where('name', 'like', '%' . $this->search . '%')
                                   ->orWhere('code', 'like', '%' . $this->search . '%');
                      })
                      // Search in travel grade relationship
                      ->orWhereHas('travelGrade', function ($travelGradeQuery) {
                          $travelGradeQuery->where('name', 'like', '%' . $this->search . '%');
                      })
                      // Search by status
                      ->orWhere(function ($statusQuery) {
                          if (stripos($this->search, 'staff') !== false || stripos($this->search, 'pegawai') !== false) {
                              $statusQuery->where('users.is_non_staff', false);
                          }
                          if (stripos($this->search, 'non-staff') !== false || stripos($this->search, 'non staff') !== false) {
                              $statusQuery->orWhere('users.is_non_staff', true);
                          }
                      });
                });
            })
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('echelons', 'positions.echelon_id', '=', 'echelons.id')
            ->leftJoin('ranks', 'users.rank_id', '=', 'ranks.id')
            // 1. Sort by eselon (lower number = higher eselon)
            ->orderByRaw('CASE WHEN echelons.id IS NULL THEN 999999 ELSE echelons.id END ASC')
            // 2. Sort by rank (higher number = higher rank)
            ->orderByRaw('CASE WHEN ranks.id IS NULL THEN 0 ELSE ranks.id END ASC')
            // 3. Sort by NIP (alphabetical)
            ->orderBy('users.nip', 'ASC')
            ->select('users.*')
            ->paginate(10);

        return view('livewire.users.index', compact('users'));
    }
}