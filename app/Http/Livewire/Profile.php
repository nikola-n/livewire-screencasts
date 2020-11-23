<?php

namespace App\Http\Livewire;

use App\Models\User;
use Livewire\Component;
use Livewire\WithFileUploads;

class Profile extends Component
{
    use WithFileUploads;

    public User $user;

    //
    //public $username = '';
    //
    //public $about = '';
    //
    //public $birthday = null;
    //
    //public $newAvatar;
    //
    //public $newAvatars = [];
    //
    public $files = [];

    protected $rules = [
        'user.username' => 'max:24',
        'user.about'    => 'max:140',
        'user.birthday' => 'sometimes',
        'files.*'       => 'nullable|image|max:10000',
    ];

    public function mount()
    {
        $this->user = auth()->user();
        //$this->username = auth()->user()->username;
        //$this->about    = auth()->user()->about;
        //$this->birthday = optional(auth()->user()->birthday)->format('m/d/Y');
    }

    //public function updating()
    //{
    //    $this->saved = false;
    //}
    public function updatedNewAvatar()
    {
        $this->validate(['newAvatar' => 'image|max:1000']);
    }

    public function save()
    {
        $this->validate();
        //
        $this->user->save();

        foreach ($this->files as $file) {
            $this->user->update([
                'avatar' => $file->store('/', 'avatars'),
            ]);
        }
        //$filename = $this->newAvatar->store('/', 'avatars');
        //    auth()->user()->update([
        //        'username' => $this->username,
        //        'about'    => $this->about,
        //        'birthday' => $this->birthday,
        //        'avatar'   => $filenames,
        //    ]);
        //}

        //$this->dispatchBrowserEvent('notify', 'Profile saved!');
        $this->emitSelf('notify-saved');
    }

    public function render()
    {
        return view('livewire.profile')->layout('layouts.app');
    }
}
