<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

class ServiceHealthTest extends TestCase
{
    public function test_mysql_connection_is_working(): void
    {
        $result = DB::select('SELECT 1 + 1 AS sum');
        $this->assertNotEmpty($result);
        $this->assertEquals(2, $result[0]->sum);
    }

    public function test_redis_connection_is_working(): void
    {
        Redis::set('test_connection', 'laravel_ok');
        $value = Redis::get('test_connection');
        $this->assertEquals('laravel_ok', $value);
        Redis::del('test_connection');
    }
}
