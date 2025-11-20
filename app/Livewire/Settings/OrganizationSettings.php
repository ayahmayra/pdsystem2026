<?php

namespace App\Livewire\Settings;

use App\Models\OrgSettings;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.app', ['title' => 'Konfigurasi Organisasi'])]
class OrganizationSettings extends Component
{
    use WithFileUploads;

    public OrgSettings $orgSettings;

    // Basic Info
    public $name = '';
    public $short_name = '';
    public $address = '';
    public $city = '';
    public $province = '';
    public $phone = '';
    public $email = '';
    public $website = '';

    // Head Info
    public $head_user_id = '';
    public $head_title = '';

    // Files
    public $signature_file = null;
    public $stamp_file = null;
    public $logo_file = null;
    public $current_signature_path = '';
    public $current_stamp_path = '';
    public $current_logo_path = '';

    // Settings
    public $ym_separator = '/';
    public $qr_footer_text = '';
    public $show_left_logo = true;
    public $show_right_logo = false;

    // Mutators for foreign key
    public function setHeadUserIdProperty($value)
    {
        $this->head_user_id = ($value === '' || $value === null) ? null : $value;
    }

    public function mount()
    {
        $this->orgSettings = OrgSettings::getInstance();
        
        // Basic Info
        $this->name = $this->orgSettings->name;
        $this->short_name = $this->orgSettings->short_name;
        $this->address = $this->orgSettings->address;
        $this->city = $this->orgSettings->city;
        $this->province = $this->orgSettings->province;
        $this->phone = $this->orgSettings->phone;
        $this->email = $this->orgSettings->email;
        $this->website = $this->orgSettings->website;

        // Head Info
        $this->head_user_id = $this->orgSettings->head_user_id;
        $this->head_title = $this->orgSettings->head_title ?? 'Kepala Dinas';

        // Current file paths
        $this->current_signature_path = $this->orgSettings->signature_path;
        $this->current_stamp_path = $this->orgSettings->stamp_path;
        $this->current_logo_path = $this->orgSettings->logo_path;

        // Settings
        $this->ym_separator = $this->orgSettings->getSetting('ym_separator', '/');
        $this->qr_footer_text = $this->orgSettings->getSetting('qr_footer_text', 'Verifikasi keaslian dokumen via QR.');
        $this->show_left_logo = $this->orgSettings->getSetting('letterhead.show_left_logo', true);
        $this->show_right_logo = $this->orgSettings->getSetting('letterhead.show_right_logo', false);
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'short_name' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'head_user_id' => 'nullable|exists:users,id',
            'head_title' => 'required|string|max:100',
            'signature_file' => 'nullable|image|max:2048', // 2MB max
            'stamp_file' => 'nullable|image|max:2048', // 2MB max
            'logo_file' => 'nullable|image|max:2048', // 2MB max
            'ym_separator' => 'required|string|max:5',
            'qr_footer_text' => 'nullable|string|max:255',
            'show_left_logo' => 'boolean',
            'show_right_logo' => 'boolean',
        ];
    }

    public function save()
    {
        $validated = $this->validate();

        try {
            // Handle file uploads
            if ($this->signature_file) {
                // Delete old file if exists
                if ($this->current_signature_path && \Storage::disk('public')->exists($this->current_signature_path)) {
                    \Storage::disk('public')->delete($this->current_signature_path);
                }
                $validated['signature_path'] = $this->signature_file->store('signatures', 'public');
            } else {
                $validated['signature_path'] = $this->current_signature_path;
            }

            if ($this->stamp_file) {
                // Delete old file if exists
                if ($this->current_stamp_path && \Storage::disk('public')->exists($this->current_stamp_path)) {
                    \Storage::disk('public')->delete($this->current_stamp_path);
                }
                $validated['stamp_path'] = $this->stamp_file->store('stamps', 'public');
            } else {
                $validated['stamp_path'] = $this->current_stamp_path;
            }

            if ($this->logo_file) {
                // Delete old file if exists
                if ($this->current_logo_path && \Storage::disk('public')->exists($this->current_logo_path)) {
                    \Storage::disk('public')->delete($this->current_logo_path);
                }
                $validated['logo_path'] = $this->logo_file->store('logos', 'public');
            } else {
                $validated['logo_path'] = $this->current_logo_path;
            }

            // Convert empty string to null for head_user_id
            if (isset($validated['head_user_id']) && $validated['head_user_id'] === '') {
                $validated['head_user_id'] = null;
            }

            // Prepare settings
            $settings = [
                'ym_separator' => $this->ym_separator,
                'qr_footer_text' => $this->qr_footer_text,
                'letterhead' => [
                    'show_left_logo' => $this->show_left_logo,
                    'show_right_logo' => $this->show_right_logo,
                ],
            ];

            $validated['settings'] = $settings;
            $validated['singleton'] = true;

            // Remove file inputs from validated data
            unset($validated['signature_file'], $validated['stamp_file'], $validated['logo_file'], $validated['show_left_logo'], $validated['show_right_logo']);

            $this->orgSettings->update($validated);

            // Update current paths
            $this->current_signature_path = $validated['signature_path'];
            $this->current_stamp_path = $validated['stamp_path'];
            $this->current_logo_path = $validated['logo_path'];

            // Reset file inputs
            $this->signature_file = null;
            $this->stamp_file = null;
            $this->logo_file = null;

            session()->flash('message', 'Konfigurasi organisasi berhasil disimpan.');

        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menyimpan konfigurasi. ' . $e->getMessage());
        }
    }

    public function removeSignature()
    {
        if ($this->current_signature_path && \Storage::disk('public')->exists($this->current_signature_path)) {
            \Storage::disk('public')->delete($this->current_signature_path);
        }
        
        $this->orgSettings->update(['signature_path' => null]);
        $this->current_signature_path = null;
        
        session()->flash('message', 'Tanda tangan berhasil dihapus.');
    }

    public function removeStamp()
    {
        if ($this->current_stamp_path && \Storage::disk('public')->exists($this->current_stamp_path)) {
            \Storage::disk('public')->delete($this->current_stamp_path);
        }
        
        $this->orgSettings->update(['stamp_path' => null]);
        $this->current_stamp_path = null;
        
        session()->flash('message', 'Stempel berhasil dihapus.');
    }

    public function removeLogo()
    {
        if ($this->current_logo_path && \Storage::disk('public')->exists($this->current_logo_path)) {
            \Storage::disk('public')->delete($this->current_logo_path);
        }
        
        $this->orgSettings->update(['logo_path' => null]);
        $this->current_logo_path = null;
        
        session()->flash('message', 'Logo berhasil dihapus.');
    }

    public function render()
    {
        $users = User::with(['position.echelon', 'rank'])
            ->whereNotNull('position_id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('echelons', 'positions.echelon_id', '=', 'echelons.id')
            ->orderByRaw('CASE WHEN echelons.code IS NULL THEN 2 ELSE 0 END')
            ->orderBy('echelons.code', 'asc')
            ->select('users.*')
            ->get();

        return view('livewire.settings.organization-settings', compact('users'));
    }
}
