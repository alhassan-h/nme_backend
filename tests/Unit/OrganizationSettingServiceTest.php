<?php

namespace Tests\Unit;

use App\Models\OrganizationSetting;
use App\Services\OrganizationSettingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrganizationSettingServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_returns_setting_value()
    {
        OrganizationSetting::create([
            'key' => 'test_setting',
            'value' => 'test_value',
            'type' => 'string'
        ]);

        $value = OrganizationSettingService::get('test_setting');
        $this->assertEquals('test_value', $value);
    }

    public function test_get_returns_default_when_setting_not_found()
    {
        $value = OrganizationSettingService::get('nonexistent_setting', 'default_value');
        $this->assertEquals('default_value', $value);
    }

    public function test_is_enabled_returns_true_for_true_value()
    {
        OrganizationSetting::create([
            'key' => 'enabled_setting',
            'value' => 'true',
            'type' => 'boolean'
        ]);

        $this->assertTrue(OrganizationSettingService::isEnabled('enabled_setting'));
    }

    public function test_is_enabled_returns_false_for_false_value()
    {
        OrganizationSetting::create([
            'key' => 'disabled_setting',
            'value' => 'false',
            'type' => 'boolean'
        ]);

        $this->assertFalse(OrganizationSettingService::isEnabled('disabled_setting'));
    }

    public function test_is_enabled_returns_false_for_nonexistent_setting()
    {
        $this->assertFalse(OrganizationSettingService::isEnabled('nonexistent_setting'));
    }

    public function test_check_access_throws_exception_when_setting_disabled()
    {
        OrganizationSetting::create([
            'key' => 'disabled_feature',
            'value' => 'false',
            'type' => 'boolean'
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('The disabled feature feature is currently disabled');

        OrganizationSettingService::checkAccess('disabled_feature', 'disabled feature');
    }

    public function test_check_access_does_not_throw_when_setting_enabled()
    {
        OrganizationSetting::create([
            'key' => 'enabled_feature',
            'value' => 'true',
            'type' => 'boolean'
        ]);

        // Should not throw
        OrganizationSettingService::checkAccess('enabled_feature', 'enabled feature');
        $this->assertTrue(true);
    }
}