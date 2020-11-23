<?php

namespace Tests\Feature;

use App\Http\Livewire\Auth\Register;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function registration_page_contains_livewire_component()
    {
        $this->get('/register')
            ->assertSeeLivewire('auth.register');
    }

    /** @test */
    function can_register()
    {
        Livewire::test(Register::class)
            ->set('email', 'nikola@gmail.com')
            ->set('password', 'secret')
            ->set('passwordConfirmation', 'secret')
            ->call('register')
            ->assertRedirect('/');

        $this->assertTrue(User::whereEmail('nikola@gmail.com')->exists());
        $this->assertEquals('nikola@gmail.com', auth()->user()->email);
    }

    /** @test */
    function email_is_required()
    {
        Livewire::test(Register::class)
            ->set('email', '')
            ->set('password', 'secret')
            ->set('passwordConfirmation', 'secret')
            ->call('register')
            ->assertHasErrors(['email' => 'required']);
    }

    /** @test */
    function email_is_valid_email()
    {
        Livewire::test(Register::class)
            ->set('email', 'nikola')
            ->set('password', 'secret')
            ->set('passwordConfirmation', 'secret')
            ->call('register')
            ->assertHasErrors(['email' => 'email']);
    }

    /** @test */
    function email_hasnt_been_taken_already()
    {
        User::create([
            'email'    => 'nikola@thecodeconnectors.nl',
            'password' => Hash::make('password'),
        ]);

        Livewire::test(Register::class)
            ->set('email', 'nikola@thecodeconnectors.nl')
            ->set('password', 'secret')
            ->set('passwordConfirmation', 'secret')
            ->call('register')
            ->assertHasErrors(['email' => 'unique']);
    }

    /** @test */
    function password_is_required()
    {
        Livewire::test(Register::class)
            ->set('email', 'nikola@thecodeconnectors.nl')
            ->set('password', '')
            ->set('passwordConfirmation', 'secret')
            ->call('register')
            ->assertHasErrors(['password' => 'required']);
    }

    /** @test */
    function password_is_minimum_of_six_characters()
    {
        Livewire::test(Register::class)
            ->set('email', 'nikola@thecodeconnectors.nl')
            ->set('password', 'secre')
            ->set('passwordConfirmation', 'secret')
            ->call('register')
            ->assertHasErrors(['password' => 'min']);
    }

    /** @test */
    function password_matches_password_confirmation()
    {
        Livewire::test(Register::class)
            ->set('email', 'nikola@thecodeconnectors.nl')
            ->set('password', 'secre')
            ->set('passwordConfirmation', 'secret')
            ->call('register')
            ->assertHasErrors(['password' => 'same']);
    }

    /** @test */
    function see_email_hasnt_already_been_taken_validation_message_as_user_types()
    {
        User::create([
            'email'                => 'nikola@thecodeconnectors.nl',
            'password'             => Hash::make('password'),
        ]);

        Livewire::test(Register::class)
            ->set('email', 'nikol@thecodeconnectors.nl')
            ->assertHasNoErrors()
            ->set('email', 'nikola@thecodeconnectors.nl')
            ->assertHasErrors(['email' => 'unique']);
    }
}
