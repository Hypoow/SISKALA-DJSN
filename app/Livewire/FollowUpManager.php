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
    public $topic;
    public $existingTopic = null; // To lock topic
    
    public $selectedPic = null;
    public $instructionRows = [
        ['text' => '', 'deadline' => '']
    ];

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



    public function mount(Activity $activity)
    {
        $this->activity = $activity;
        $this->checkExistingTopic();
    }

    public function checkExistingTopic()
    {
        $first = $this->activity->followups()->first();
        if ($first) {
            $this->existingTopic = $first->topic;
            $this->topic = $first->topic;
        }
    }

    public function addInstructionRow()
    {
        $this->instructionRows[] = ['text' => '', 'deadline' => ''];
    }

    public function removeInstructionRow($index)
    {
        unset($this->instructionRows[$index]);
        $this->instructionRows = array_values($this->instructionRows);
    }

    public function resetInputs()
    {
        $this->selectedPic = null;
        $this->instructionRows = [
            ['text' => '', 'deadline' => '']
        ];
        $this->checkExistingTopic();
    }

    public function save()
    {
        $this->validate([
            'topic' => 'required|string',
            'selectedPic' => 'required|string',
            'instructionRows.*.text' => 'required|string',
        ]);

        $count = 0;
        foreach ($this->instructionRows as $row) {
            if (!empty($row['text'])) {
                ActivityFollowup::create([
                    'activity_id' => $this->activity->id,
                    'topic' => $this->topic, 
                    'instruction' => $row['text'],
                    'pic' => $this->selectedPic,
                    'deadline' => !empty($row['deadline']) ? $row['deadline'] : null,
                    'status' => ActivityFollowup::STATUS_PENDING,
                    'checklist' => null, 
                ]);
                $count++;
            }
        }

        if ($count > 0) {
            $this->resetInputs();
            $this->showForm = false;
            $this->dispatch('followup-saved');
        } else {
             $this->addError('instructionRows', 'Setidaknya isi satu poin tindak lanjut.');
        }
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
        
        // Don't trigger select2 change immediately which might overwrite with null if not careful
        // Use dispatch only if needed, logic in view handles init value
        $this->dispatch('edit-mode-toggled', topicName: $this->editTopic);
    }

    public function update()
    {
        $this->validate([
            'editTopic' => 'required|string',
            'editPic' => 'required|string',
            'editInstruction' => 'required|string',
            'editDeadline' => 'nullable|date',
        ]);
        
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
            'followups' => $this->activity->followups()
                ->orderByRaw('deadline IS NULL, deadline ASC')
                ->orderBy('created_at', 'desc')
                ->get()
        ]);
    }
}
