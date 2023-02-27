<?php

namespace Tests\Feature\API;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Testing\Fluent\AssertableJson;
use App\Models\User;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void {
        parent::setUp();

        $this->seed([
            'ProductCategorySeeder',
            'MerchantSeeder',
            'ProductSeeder',
        ]);
    }

    protected function tearDown(): void {
        parent::tearDown();
    }

    private $userResponseHas = [
        'data',
        'data.id',
        'data.name',
        'data.email',
        'data.createdAt',
        'data.updatedAt',
    ];

    private $userResponseHasType = [
        'data' => 'array',
        'data.id' => 'integer',
        'data.name' => 'string',
        'data.email' => 'string',
        'data.createdAt' => 'string',
        'data.updatedAt' => 'string',
    ];

    /**
     * A basic feature test example.
     */
    public function test_user_can_register(): void {
        $data = [
            'name' => 'fahrul',
            'email' => 'fahrul@example.com',
            'password' => 'pass7890',
            'password_confirmation' => 'pass7890',
        ];

        $response = $this->post('/api/v1/auth/register', $data);

        $response->assertStatus(201);
        $response->assertJson(fn (AssertableJson $json) => $json
            ->hasAll($this->userResponseHas)
            ->whereAllType($this->userResponseHasType)
        );
    }

    public function test_email_address_is_registered(): void {
        $data = [
            'name' => 'fahrul',
            'email' => 'fahrul@example.com',
            'password' => 'pass7890',
            'password_confirmation' => 'pass7890',
        ];

        $response = $this->post('/api/v1/auth/register', $data);
        $response = $this->post('/api/v1/auth/register', $data);

        $response->assertStatus(302);
        $response->assertInvalid([
            'email' => 'The email has already been taken.',
        ]);
    }

    public function test_invalid_request_the_field_is_required(): void {
        $data = [
            'name' => '',
            'email' => '',
            'password' => '',
            'password_confirmation' => '',
        ];

        $response = $this->post('/api/v1/auth/register', $data);

        $response->assertStatus(302);
        $response->assertInvalid([
            'name' => 'The name field is required.',
            'email' => 'The email field is required.',
            'password' => 'The password field is required.',
        ]);
    }

    public function test_invalid_email_address(): void {
        $data = [
            'name' => 'fahrul',
            'email' => 'fahrulexample.com',
            'password' => 'pass7890',
            'password_confirmation' => 'pass7890',
        ];

        $response = $this->post('/api/v1/auth/register', $data);

        $response->assertStatus(302);
        $response->assertInvalid([
            'email' => 'The email field must be a valid email address.',
        ]);
    }

    public function test_confirm_password_must_be_same(): void {
        $data = [
            'name' => 'fahrul',
            'email' => 'fahrul@example.com',
            'password' => 'pass7890',
            'password_confirmation' => '7890pass',
        ];

        $response = $this->post('/api/v1/auth/register', $data);

        $response->assertStatus(302);
        $response->assertInvalid([
            'password' => 'The password field confirmation does not match.',
        ]);
    }

    public function test_user_can_login(): void {
        $data = [
            'name' => 'fahrul',
            'email' => 'fahrul@example.com',
            'password' => 'pass7890',
            'password_confirmation' => 'pass7890',
        ];

        $response = $this->post('/api/v1/auth/register', $data);
        $response->assertStatus(201);

        $data = [
            "email" => "fahrul@example.com",
            "password" => "pass7890"
        ];

        $response = $this->post('/api/v1/auth/login', $data);

        $response->assertStatus(200);
        $response->assertJson(fn (AssertableJson $json) => $json
            ->hasAll([
                'access_token',
                'token_type',
                'expires_in',
                'user',])
            ->whereAllType([
                'access_token' => 'string',
                'token_type' => 'string',
                'expires_in' => 'integer',
                'user' => 'array',])
            );
        $response->assertJson([
            'token_type' => 'Bearer',
        ]);
    }

    public function test_login_wrong_email_or_password(): void {
        $data = [
            'name' => 'fahrul',
            'email' => 'fahrul@example.com',
            'password' => 'pass7890',
            'password_confirmation' => 'pass7890',
        ];

        $response = $this->post('/api/v1/auth/register', $data);
        $response->assertStatus(201);

        $data = [
            "email" => "fahrul@example.co",
            "password" => "pass789000"
        ];

        $response = $this->post('/api/v1/auth/login', $data);

        $response->assertStatus(401);
        $response->assertJson([
            'errors' => 'Unauthorized',
        ]);
    }

    public function test_login_email_or_password_is_required(): void {
        $data = [
            'name' => 'fahrul',
            'email' => 'fahrul@example.com',
            'password' => 'pass7890',
            'password_confirmation' => 'pass7890',
        ];

        $response = $this->post('/api/v1/auth/register', $data);
        $response->assertStatus(201);

        $data = [
            'email' => '',
            'password' => ''
        ];

        $response = $this->post('/api/v1/auth/login', $data);

        $response->assertStatus(302);
        $response->assertInvalid([
            'email' => 'The email field is required.',
            'password' => 'The password field is required.',
        ]);
    }

    public function test_can_get_user_profile(): void {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
                         ->get('/api/v1/auth/user-profile');

        $response->assertStatus(200);
        $response->assertJson(fn (AssertableJson $json) => $json
            ->hasAll($this->userResponseHas)
            ->whereAllType($this->userResponseHasType)
        );
    }

    public function test_invalid_get_user_profile_without_login(): void {
        $response = $this->get('/api/v1/auth/user-profile', [
            'Accept'=>'application/json'
        ]);
        $response
        ->assertStatus(401)
        ->assertJson([
            'message' => 'Unauthenticated.',
        ]);
    }

    public function test_invalid_get_user_profile_because_token_is_wrong(): void {
        $user = User::first();
        $token = 'abc';

        $response = $this->get('/api/v1/auth/user-profile', [
            'authorization' => 'Bearer $token',
            'Accept'=>'application/json'
        ]);
        $response
        ->assertStatus(401)
        ->assertJson([
            'message' => 'Unauthenticated.',
        ]);
    }
}
