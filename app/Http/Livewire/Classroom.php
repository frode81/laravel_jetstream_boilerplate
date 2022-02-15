<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Classroom as Model;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;


class Classroom extends Component
{
    use WithPagination;

    public $paginate = 10;

    public $name;
   public $description;
   public $classid;
   public $owner;
   public $courses = [];
   public $category;


    public $mode = 'create';

    public $showForm = false;

    public $primaryId = null;

    public $search;

    public $showConfirmDeletePopup = false;

    protected $rules = [
'name' => 'required',
'description' => 'required',
'classid' => 'required',
'owner' => 'required',
'courses.*' => 'required',
'category' => 'required',

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
        $model = Model::where('name', 'like', '%'.$this->search.'%')->orWhere('description', 'like', '%'.$this->search.'%')->orWhere('classid', 'like', '%'.$this->search.'%')->orWhere('owner', 'like', '%'.$this->search.'%')->orWhere('courses', 'like', '%'.$this->search.'%')->orWhere('category', 'like', '%'.$this->search.'%')->latest()->paginate($this->paginate);
        return view('livewire.classroom', [
            'rows'=> $model
        ]);
    }


    public function create ()
    {
        $authid = auth()->user()->id;
        $this->mode = 'create';
        $this->resetForm();
        $this->Courses = Course::where('owner', '=', $authid,)->get();
        $this->owner = auth()->user()->id;

        $this->showForm = true;
    }


    public function edit($primaryId)
    {

        $this->mode = 'update';
        $this->primaryId = $primaryId;
        $model = Model::find($primaryId);

        $this->name= $model->name;
$this->description= $model->description;
$this->classid= $model->classid;
$this->owner= $model->owner;

$this->courses= $model->courses;


$this->courses= $model->courses;
$this->category= $model->category;



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
$model->classid= $this->classid;
$model->owner= $this->owner;
$model->courses= implode(' , ', (array)$this->courses);

$model->category= $this->category;
 $model->save();

          $this->resetForm();
          session()->flash('message', 'Record Saved Successfully');
          $this->showForm = false;
    }

    public function resetForm()
    {
        $this->name= "";
$this->description= "";
$this->classid= "";
$this->owner= "";
$this->courses= "";
$this->category= "";

    }


    public function update()
    {
          $this->validate();

          $model = Model::find($this->primaryId);

             $model->name= $this->name;
$model->description= $this->description;
$model->classid= $this->classid;
$model->owner= $this->owner;
$model->courses= $this->courses;
$model->category= $this->category;
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
