<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Document;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Models\Image;


use App\Models\course as Model;


class Course extends Component
{
    use WithPagination;
    use WithFileUploads;


    public $paginate = 10;

    public $name;
   public $description;
   public $url;
   public $category;
   public $owner;
   public $photo;


    public $mode = 'create';

    public $showForm = false;

    public $primaryId = null;

    public $search;

    public $showConfirmDeletePopup = false;

    protected $rules = [
      'name' => 'required',
      'description' => 'required',
      'url' => 'required',
      'category' => 'required',
      'owner' => 'required',
      'photo' => 'image|max:1024', // 1MB Max

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
        $model = Model::where('name', 'like', '%'.$this->search.'%')->orWhere('description', 'like', '%'.$this->search.'%')->orWhere('url', 'like', '%'.$this->search.'%')->orWhere('category', 'like', '%'.$this->search.'%')->orWhere('owner', 'like', '%'.$this->search.'%')->orWhere('photo', 'like', '%'.$this->search.'%')->latest()->paginate($this->paginate);
        return view('livewire.course', [
            'rows'=> $model
        ]);
    }


    public function create ()
    {
        $this->mode = 'create';
        $this->resetForm();
        $this->showForm = true;
    }


    public function edit($primaryId)
    {
        $this->mode = 'update';
        $this->primaryId = $primaryId;
        $model = Model::find($primaryId);

        $this->name= $model->name;
        $this->description= $model->description;
        $this->url= $model->url;
        $this->category= $model->category;
        $this->owner= $model->owner;
        $this->photo= $model->photo;



        $this->showForm = true;
    }

    public function closeForm()
    {
        $this->showForm = false;
    }

    public function store()
    {
          $this->validate();

          $this->photo->store('photos');


          $model = new Model();

             $model->name= $this->name;
              $model->description= $this->description;
              $model->url= $this->url;
              $model->category= $this->category;
              $model->owner= $this->owner;
              $model->photo= $this->photo->store('photos');
              $model->save();

          $this->resetForm();
          session()->flash('message', 'Record Saved Successfully');
          $this->showForm = false;
    }

    public function resetForm()
    {
        $this->name= "";
        $this->description= "";
        $this->url= "";
        $this->category= "";
        $this->owner= "";
        $this->photo= "";

    }


    public function update()
    {
          $this->validate();

          $model = Model::find($this->primaryId);

            $model->name= $this->name;
            $model->description= $this->description;
            $model->url= $this->url;
            $model->category= $this->category;
            $model->owner= $this->owner;
            $model->photo= $this->photo;
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
