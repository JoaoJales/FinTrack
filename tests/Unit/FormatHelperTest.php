<?php

namespace Tests\Unit;

use App\Helpers\FormatHelper;
use PHPUnit\Framework\TestCase;

class FormatHelperTest extends TestCase
{
    public function test_br_to_us_converts_brazilian_decimal_format(): void
    {
        $this->assertEqualsWithDelta(1234.56, FormatHelper::brToUS('1.234,56'), 0.001);
    }

    public function test_br_to_us_handles_values_without_thousands_separator(): void
    {
        $this->assertEqualsWithDelta(50.0, FormatHelper::brToUS('50,00'), 0.001);
        $this->assertEqualsWithDelta(0.5, FormatHelper::brToUS('0,50'), 0.001);
    }

    public function test_br_to_us_integer_with_comma_decimals(): void
    {
        $this->assertEqualsWithDelta(100.0, FormatHelper::brToUS('100,00'), 0.001);
    }
}
