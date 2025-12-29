<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Activity;
use App\Models\ActivityFollowup;
use Illuminate\Support\Facades\Auth;

class FollowUpManager extends Component
{
    public Activity $activity;
    
    // Form Properties
    public $inputs = []; // Array to hold inputs for each suggested PIC: ['Komisi PME' => 'instruction...']
    public $deadlines = []; // Array to hold deadlines: ['Komisi PME' => '2023-12-01']
    
    // Single Edit Properties
    public $editTopic;
    public $editInstruction;
    public $editPic;
    public $editDeadline;
    public $editStatus;

    // UI State
    public $isEditing = false;
    public $showForm = false;
    public $editingId = null;

    protected $rules = [
        'inputs.*' => 'nullable|string',
        'deadlines.*' => 'nullable|date',
        'editInstruction' => 'required_if:isEditing,true|string',
        'editPic' => 'required_if:isEditing,true|string',
    ];

    public function mount(Activity $activity)
    {
        $this->activity = $activity;
        $this->prepareInputs();
    }

    public function prepareInputs()
    {
        $dispositions = $this->activity->disposition_to ?? [];
        $foundCommissions = [];

        foreach ($dispositions as $person) {
            $commission = $this->getCommissionForPerson($person);
            if ($commission) {
                $foundCommissions[] = $commission;
            } else {
                // If the person/group itself is a key (e.g. Sekretariat DJSN if selected directly)
                $foundCommissions[] = $person;
            }
        }

        // Always include Sekretariat if 'Sekretariat DJSN' is in dispo or simply add it if appropriate?
        // User request: "menyesuaikan yang di dispo".
        // Also ensure we have unique keys
        $uniqueCommissions = array_unique($foundCommissions);
        
        // Setup inputs
        foreach($uniqueCommissions as $comm) {
            // Check if valid commission key or just a name
            $this->inputs[$comm] = ''; 
            $this->deadlines[$comm] = null;
        }

        // Fallback: If no dispositions, maybe provide a generic blank?
        if(empty($this->inputs)) {
            $this->inputs['Tindak Lanjut Umum'] = '';
            $this->deadlines['Tindak Lanjut Umum'] = null;
        }
    }

    private function getCommissionForPerson($name)
    {
        foreach (Activity::COUNCIL_STRUCTURE as $commission => $members) {
            // Check if name is the commission itself
            if ($name === $commission) return $commission;
            // Check if name is in members
            if (in_array($name, $members)) return $commission;
        }
        
        // If not found in Council Structure (Dewan), group into Sekretariat DJSN
        // This handles explicit "Sekretariat DJSN" selection AND individual staff names
        return 'Sekretariat DJSN'; 
    }

    public function save()
    {
        $count = 0;
        foreach ($this->inputs as $pic => $instruction) {
            if (!empty($instruction)) {
                ActivityFollowup::create([
                    'activity_id' => $this->activity->id,
                    'topic' => null, // Optional in batch
                    'instruction' => $instruction,
                    'pic' => $pic,
                    'deadline' => $this->deadlines[$pic] ?? null,
                    'status' => ActivityFollowup::STATUS_PENDING,
                ]);
                $count++;
            }
        }

        if ($count > 0) {
            $this->resetInputs();
            $this->showForm = false;
            $this->dispatch('followup-saved');
        } else {
            // Maybe show error or just close?
             $this->addError('inputs', 'Setidaknya isi satu tindak lanjut.');
        }
    }

    public function resetInputs()
    {
        $this->inputs = [];
        $this->deadlines = [];
        $this->prepareInputs(); // Reset to suggested
    }

    public function edit($id)
    {
        $item = ActivityFollowup::findOrFail($id);
        $this->editingId = $id;
        $this->isEditing = true;
        
        $this->editTopic = $item->topic;
        $this->editInstruction = $item->instruction;
        $this->editPic = $item->pic;
        $this->editDeadline = $item->deadline ? $item->deadline->format('Y-m-d') : null;
        $this->editStatus = $item->status;
    }

    public function update()
    {
        $this->validate();
        
        $item = ActivityFollowup::findOrFail($this->editingId);
        $item->update([
            'topic' => $this->editTopic,
            'instruction' => $this->editInstruction,
            'pic' => $this->editPic,
            'deadline' => $this->editDeadline,
        ]);

        $this->cancelEdit();
        $this->dispatch('followup-updated');
    }

    public function cancelEdit()
    {
        $this->isEditing = false;
        $this->showForm = false;
        $this->editingId = null;
        $this->reset(['editTopic', 'editInstruction', 'editPic', 'editDeadline', 'editStatus']);
    }

    public function delete($id)
    {
        ActivityFollowup::destroy($id);
    }

    public function render()
    {
        return view('livewire.follow-up-manager', [
            'followups' => $this->activity->followups()->orderBy('created_at', 'desc')->get()
        ]);
    }
}
