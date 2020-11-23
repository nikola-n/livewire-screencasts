<?php

namespace App\Http\Livewire\Auth;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Hash;

class Register extends Component
{

    public $email = '';
    public $password = '';
    public $passwordConfirmation = '';

    protected $rules = [
        'email'    => 'required|email|unique:users',
        'password' => 'required|min:6|same:passwordConfirmation',
    ];

    public function updatedEmail()
    {
        //we wont use validateOnly('email') cause we dont want to be required on update
        $this->validate(['email' => 'unique:users']);
    }

    public function register()
    {
        $this->validate();

        $user = User::create([
            'email'    => $this->email,
            'password' => Hash::make($this->password),
        ]);

        auth()->login($user);

        return redirect('/');
    }

    public function render()
    {
        return view('livewire.auth.register')
            ->layout('layouts.auth');
    }
}
