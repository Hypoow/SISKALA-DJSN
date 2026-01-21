<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Topic;

class ManageTopics extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $name, $color, $topic_id;
    public $search = '';
    public $isEditing = false;
    
    // Reset page on search
    public function updatingSearch()
    {
        $this->resetPage();
    }

    protected $rules = [
        'name' => 'required|min:2|unique:topics,name',
        'color' => 'required',
    ];

    public function render()
    {
        $topics = Topic::orderBy('name', 'asc')
            ->when($this->search, function($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->paginate(10);

        return view('livewire.manage-topics', [
            'topics' => $topics,
        ]);
    }

    public function resetInput()
    {
        $this->name = '';
        $this->color = '#007bff';
        $this->topic_id = null;
        $this->isEditing = false;
        $this->resetErrorBag();
    }

    public function create()
    {
        $this->resetInput();
        $this->dispatch('open-modal');
    }

    public function store()
    {
        $validationRules = $this->rules;
        if ($this->isEditing) {
            $validationRules['name'] = 'required|min:2|unique:topics,name,' . $this->topic_id;
        }

        $this->validate($validationRules);

        Topic::updateOrCreate(['id' => $this->topic_id], [
            'name' => $this->name,
            'color' => $this->color,
        ]);

        session()->flash('message', $this->isEditing ? 'Topik berhasil diperbarui.' : 'Topik baru berhasil ditambahkan.');

        $this->dispatch('close-modal');
        $this->resetInput();
    }

    public function edit($id)
    {
        $topic = Topic::findOrFail($id);
        $this->topic_id = $id;
        $this->name = $topic->name;
        $this->color = $topic->color;
        $this->isEditing = true;
        $this->resetErrorBag();
        $this->dispatch('open-modal');
    }

    public function delete($id)
    {
        Topic::find($id)->delete();
        session()->flash('message', 'Topik berhasil dihapus.');
    }
}
