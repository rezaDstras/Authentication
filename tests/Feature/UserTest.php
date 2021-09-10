<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class UserTest extends TestCase
{
    //for ignoring data in test in database
    use RefreshDatabase;

    private $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user =User::factory()->create();
    }

    public function test_register_should_be_validated()
    {
        $response = $this->postJson(route('register'));

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
    public function test_new_user_can_register()
    {
        $response = $this->postJson(route('register'), [
            'name' => "ehsan",
            'lastName'=>'dastras',
            'email' => "test1@gmail.com",
            'password' => "12345678",
            'gender'=>1
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
    }
    public function test_login_should_be_validated()
    {
        $response = $this->postJson(route('login'));

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
    public function test_user_can_login_with_true_credentials()
    {
        //first create new user with faker -> UserFactory
        $user=User::factory()->create();

        $response = $this->postJson(route('login') , [
            'email'=>$user->email,
            'password'=>'password',
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
    }
    public function test_logged_in_user_show_info()
    {
        $user =$this->user;
        //behavior as a created user
        $response=$this->actingAs($user)->get(route('profile'));

        $response->assertStatus(Response::HTTP_OK);
    }
    public function test_info_should_be_validated_to_update()
    {
        $user =$this->user;

        Sanctum::actingAs($user);

        $response = $this->postJson(route('update'),[]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
    public function test_user_can_update_info()
    {
        $user =$this->user;

        Sanctum::actingAs($user);

        $response = $this->postJson(route('update'),[
            'name'=>'test',
            'lastName'=>'test',
            'gender'=>1,

        ])->assertSuccessful();

        $user->refresh();

        $this->assertSame('test',$user->name);
        $this->assertSame('test',$user->lastName);
        $this->assertSame(1,$user->gender);
    }
    public function test_password_should_be_validated_to_change()
    {
        $user =$this->user;

        Sanctum::actingAs($user);

        $response = $this->putJson(route('ChangePassword'),[]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
    public function test_user_can_change_password()
    {
        $oldPassword = 'password';
        $newPassword = 'newone';

        $this->withoutExceptionHandling();
        $user=User::factory()->create([
           'password' => Hash::make($oldPassword),
        ]);

        Sanctum::actingAs($user);
        $response = $this->putJson(route('ChangePassword'),[
            '_token' => csrf_token(),
            'oldPassword'=>$oldPassword,
            'newPassword'=>$newPassword,
        ]);

        $response->assertStatus(302);
        $user->refresh();
        $this->assertTrue(Hash::check($newPassword,$user->password));
    }
    public function test_logged_in_user_can_logout()
    {
        $user =$this->user;
        //behavior as a created user
        $response=$this->actingAs($user)->postJson(route('logout'));

        $response->assertStatus(Response::HTTP_OK);
    }
}
