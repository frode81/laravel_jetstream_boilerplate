<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Category as Model;


class Category extends Component
{
    use WithPagination;

    public $paginate = 10;

    public $name;
   public $description;
   public $owner;


    public $mode = 'create';

    public $showForm = false;

    public $primaryId = null;

    public $search;

    public $showConfirmDeletePopup = false;

    protected $rules = [
'name' => 'required',
'description' => 'required',
'owner' => 'required',

];



    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $model = Model::where('name', 'like', '%'.$this->search.'%')->orWhere('description', 'like', '%'.$this->search.'%')->orWhere('owner', 'like', '%'.$this->search.'%')->latest()->paginate($this->paginate);
        return view('livewire.category', [
            'rows'=> $model
        ]);
    }


    public function create ()
    {
        $this->mode = 'create';
        $this->resetForm();
        $this->owner = auth()->user()->id;
        $this->showForm = true;
    }


    public function edit($primaryId)
    {
        $this->mode = 'update';
        $this->primaryId = $primaryId;
        $model = Model::find($primaryId);
        $this->owner = auth()->user()->id;

        $this->name= $model->name;
        $this->description= $model->description;
        $this->owner= $model->owner;



        $this->showForm = true;
    }

    public function closeForm()
    {
        $this->showForm = false;
    }

    public function store()
    {
          $this->validate();

          $model = new Model();

             $model->name= $this->name;
$model->description= $this->description;
$model->owner= $this->owner;
 $model->save();

          $this->resetForm();
          session()->flash('message', 'Record Saved Successfully');
          $this->showForm = false;
    }

    public function resetForm()
    {
        $this->name= "";
$this->description= "";
$this->owner= "";

    }


    public function update()
    {
          $this->validate();

          $model = Model::find($this->primaryId);

             $model->name= $this->name;
$model->description= $this->description;
$model->owner= $this->owner;
 $model->save();

          $this->resetForm();

          $this->showForm = false;

         session()->flash('message', 'Record Updated Successfully');
    }

    public function confirmDelete($primaryId)
    {
        $this->primaryId = $primaryId;
        $this->showConfirmDeletePopup = true;
    }

    public function destroy()
    {
        Model::find($this->primaryId)->delete();
        $this->showConfirmDeletePopup = false;
        session()->flash('message', 'Record Deleted Successfully');
    }

    public function clearFlash()
    {
        session()->forget('message');
    }

}
