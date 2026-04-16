<?php

namespace Tests\Unit\Actions;

use App\Actions\GenerateAndSendPayeData;
use PHPUnit\Framework\TestCase;

/**
 * Exposes protected helpers on GenerateAndSendPayeData so they can be unit-tested
 * without touching the filesystem, HTTP layer, or a database.
 */
class ExposedGenerateAndSendPayeData extends GenerateAndSendPayeData
{
    public function callFormatValue($value): string
    {
        return $this->formatValue($value);
    }

    public function callFormatContent($data): string
    {
        return $this->formatContent($data);
    }

    public function callSplitName(string $name): array
    {
        return $this->splitName($name);
    }

    public function callGetValue(array $keys, array $deductions): string
    {
        return $this->getValue($keys, $deductions);
    }
}

class GenerateAndSendPayeDataTest extends TestCase
{
    private ExposedGenerateAndSendPayeData $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new ExposedGenerateAndSendPayeData();
    }

    // ── formatValue ────────────────────────────────────────────────────────

    /** @test */
    public function format_value_formats_integers_to_two_decimal_places()
    {
        $this->assertSame('1234.00', $this->action->callFormatValue(1234));
    }

    /** @test */
    public function format_value_formats_floats_to_two_decimal_places()
    {
        $this->assertSame('1234.50', $this->action->callFormatValue(1234.5));
    }

    /** @test */
    public function format_value_does_not_add_thousands_separator()
    {
        $this->assertSame('1000000.00', $this->action->callFormatValue(1000000));
    }

    // ── monthName ──────────────────────────────────────────────────────────

    /** @test */
    public function month_name_returns_correct_month_for_each_number()
    {
        $expected = [
            1  => 'JANUARY',
            2  => 'FEBRUARY',
            3  => 'MARCH',
            4  => 'APRIL',
            5  => 'MAY',
            6  => 'JUNE',
            7  => 'JULY',
            8  => 'AUGUST',
            9  => 'SEPTEMBER',
            10 => 'OCTOBER',
            11 => 'NOVEMBER',
            12 => 'DECEMBER',
        ];

        foreach ($expected as $number => $name) {
            $this->assertSame($name, $this->action->monthName($number), "Failed for month $number");
        }
    }

    // ── formatContent ──────────────────────────────────────────────────────

    /** @test */
    public function format_content_joins_array_values_with_commas()
    {
        $this->assertSame('a,b,c', $this->action->callFormatContent(['a', 'b', 'c']));
    }

    /** @test */
    public function format_content_handles_associative_arrays()
    {
        $this->assertSame('JOHN,DOE', $this->action->callFormatContent(['first' => 'JOHN', 'last' => 'DOE']));
    }

    // ── splitName ──────────────────────────────────────────────────────────

    /** @test */
    public function split_name_returns_last_word_as_surname_and_first_word_as_first_name()
    {
        [$surname, $firstName, $middleName] = $this->action->callSplitName('JOHN DOE');

        $this->assertSame('DOE', $surname);
        $this->assertSame('JOHN', $firstName);
    }

    /** @test */
    public function split_name_captures_middle_names()
    {
        [$surname, $firstName, $middleName] = $this->action->callSplitName('JOHN JAMES DOE');

        $this->assertSame('DOE', $surname);
        $this->assertSame('JOHN', $firstName);
        $this->assertStringContainsString('JAMES', (string) $middleName);
    }

    /** @test */
    public function split_name_handles_single_word_name_gracefully()
    {
        // With a single token, first and last are the same word; middle is empty
        [$surname, $firstName] = $this->action->callSplitName('JOHN');

        $this->assertSame('JOHN', $surname);
        $this->assertSame('JOHN', $firstName);
    }

    // ── getValue ───────────────────────────────────────────────────────────

    /** @test */
    public function get_value_returns_formatted_value_when_key_is_present()
    {
        $result = $this->action->callGetValue(['tax'], ['tax' => 5000]);

        $this->assertSame('5000.00', $result);
    }

    /** @test */
    public function get_value_returns_first_matching_key_when_multiple_keys_given()
    {
        $result = $this->action->callGetValue(['nhf', 'tax'], ['tax' => 300, 'nhf' => 150]);

        $this->assertSame('150.00', $result);
    }

    /** @test */
    public function get_value_returns_empty_string_when_no_key_matches()
    {
        $result = $this->action->callGetValue(['nhf'], ['tax' => 5000]);

        $this->assertSame('', $result);
    }

    /** @test */
    public function get_value_falls_through_to_next_key_when_first_is_absent()
    {
        $result = $this->action->callGetValue(['nhf', 'pension'], ['pension' => 2000]);

        $this->assertSame('2000.00', $result);
    }
}
