<?php

namespace Tests\Feature\API;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Testing\Fluent\AssertableJson;
use App\Models\Merchant;

class MerchantTest extends TestCase
{
    use RefreshDatabase;

    private $merchantPropertiesOnLoop = [
        'data',
        'data.0.merchantID',
        'data.0.name',
        'data.0.merchantCategory',
        'data.0.address',
        'data.0.operationalTimeInOneDay',
        'data.0.isOpen',
        'data.0.logo',
        'data.0.description',
        'data.0.createdAt',
        'data.0.updatedAt'
    ];

    private $merchantPropertiesTypeOnLoop = [
        'data' => 'array',
        'data.0.merchantID' => 'string',
        'data.0.name' => 'string',
        'data.0.merchantCategory' => 'string',
        'data.0.address' => 'string',
        'data.0.operationalTimeInOneDay' => 'string',
        'data.0.isOpen' => 'integer',
        'data.0.logo' => 'string',
        'data.0.description' => 'string',
        'data.0.createdAt' => 'string',
        'data.0.updatedAt' => 'string',
    ];

    private $merchantProperties = [
        'data',
        'data.merchantID',
        'data.name',
        'data.merchantCategory',
        'data.address',
        'data.operationalTimeInOneDay',
        'data.isOpen',
        'data.logo',
        'data.description',
        'data.createdAt',
        'data.updatedAt'
    ];

    private $merchantPropertiesType = [
        'data' => 'array',
        'data.merchantID' => 'string',
        'data.name' => 'string',
        'data.merchantCategory' => 'string',
        'data.address' => 'string',
        'data.operationalTimeInOneDay' => 'string',
        'data.isOpen' => 'integer',
        'data.logo' => 'string',
        'data.description' => 'string',
        'data.createdAt' => 'string',
        'data.updatedAt' => 'string',
    ];

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

    /**
     * A basic feature test example.
     */
    public function test_get_merchants(): void {
        $response = $this->get('/api/v1/merchants');

        $response->assertStatus(200);
        $response->assertJson(fn (AssertableJson $json) => $json
            ->hasAll($this->merchantPropertiesOnLoop)
            ->whereAllType($this->merchantPropertiesTypeOnLoop)
        );
    }

    public function test_get_merchant_info_detail(): void {
        $response = $this->get('/api/v1/merchants');

        $response->assertStatus(200);

        $merchantID = $response->decodeResponseJson()['data'][0]['merchantID'];
        $response = $this->get('/api/v1/merchants/' . $merchantID);

        $response->assertStatus(200);
        $response->assertJson(fn (AssertableJson $json) => $json
            ->hasAll($this->merchantProperties)
            ->has('data.products')
            ->whereAllType($this->merchantPropertiesType)
            ->whereType('data.products', 'array')
        );
    }
}
