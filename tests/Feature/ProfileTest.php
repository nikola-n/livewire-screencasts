<?php

namespace Tests\Feature;

use App\Http\Livewire\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_see_profile_component_on_profile_page()
    {
        $this->actingAs(User::factory()->create())
            ->withoutExceptionHandling()
            ->get('/profile')
            ->assertSuccessful()
            ->assertSeeLivewire('profile');
    }

    /** @test */
    public function can_update_profile()
    {

        Livewire::actingAs($user = User::factory()->create())
            ->test(Profile::class)
            ->set('user.username', 'foo')
            ->set('user.about', 'bar')
            ->call('save');

        $user->refresh();

        $this->assertEquals('foo', $user->username);
        $this->assertEquals('bar', $user->about);
    }

    /** @test */
    public function username_must_be_less_than_24_characters()
    {
        Livewire::actingAs($user = User::factory()->create())
            ->test(Profile::class)
            ->set('user.username', str_repeat('a', 25))
            ->set('user.about', 'bar')
            ->call('save')
            ->assertHasErrors(['user.username' => 'max']);

    }

    /** @test */
    public function about_must_be_less_than_140_characters()
    {
        Livewire::actingAs($user = User::factory()->create())
            ->test(Profile::class)
            ->set('user.about', str_repeat('a', 141))
            ->set('user.username', 'bar')
            ->call('save')
            ->assertHasErrors(['user.about' => 'max']);
    }

    /** @test */
    function profile_info_is_pre_populated()
    {
        $user = User::factory()->create([
            'username' => 'foo',
            'about'    => 'bar',
        ]);

        Livewire::actingAs($user)
            ->test('profile')
            ->assertSet('user.username', 'foo')
            ->assertSet('user.about', 'bar');
    }

    /** @test */
    function message_is_shown_on_save()
    {
        $user = User::factory()->create([
            'username' => 'foo',
            'about'    => 'bar',
        ]);

        Livewire::actingAs($user)
            ->test('profile')
            ->call('save')
            //->assertDispatchedBrowserEvent('notify');
            ->assertEmitted('notify-saved');
    }

    /** @test */
    public function can_upload_avatar()
    {
        $user = User::factory()->create();

        $file = UploadedFile::fake()->image('avatar.png');

        Storage::fake('avatars');

        Livewire::actingAs($user)
            ->test('profile')
            ->set('newAvatar', $file)
            ->call('save');

        $user->refresh();

        $this->assertNotNull($user->avatar);
        Storage::disk('avatars')->assertExists($user->avatar);
    }
}
