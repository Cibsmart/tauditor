<?php

namespace Tests\Unit\Actions;

use App\Actions\GenerateAndSendPayeData;

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

beforeEach(function () {
    $this->action = new ExposedGenerateAndSendPayeData;
});

// ── formatValue ────────────────────────────────────────────────────────

it('formats integers to two decimal places', function () {
    expect($this->action->callFormatValue(1234))->toBe('1234.00');
});

it('formats floats to two decimal places', function () {
    expect($this->action->callFormatValue(1234.5))->toBe('1234.50');
});

it('does not add thousands separator', function () {
    expect($this->action->callFormatValue(1000000))->toBe('1000000.00');
});

// ── monthName ──────────────────────────────────────────────────────────

it('returns correct month name for each number', function () {
    $expected = [
        1 => 'JANUARY',
        2 => 'FEBRUARY',
        3 => 'MARCH',
        4 => 'APRIL',
        5 => 'MAY',
        6 => 'JUNE',
        7 => 'JULY',
        8 => 'AUGUST',
        9 => 'SEPTEMBER',
        10 => 'OCTOBER',
        11 => 'NOVEMBER',
        12 => 'DECEMBER',
    ];

    foreach ($expected as $number => $name) {
        expect($this->action->monthName($number))->toBe($name, "Failed for month $number");
    }
});

// ── formatContent ──────────────────────────────────────────────────────

it('joins array values with commas', function () {
    expect($this->action->callFormatContent(['a', 'b', 'c']))->toBe('a,b,c');
});

it('handles associative arrays', function () {
    expect($this->action->callFormatContent(['first' => 'JOHN', 'last' => 'DOE']))->toBe('JOHN,DOE');
});

// ── splitName ──────────────────────────────────────────────────────────

it('returns last word as surname and first word as first name', function () {
    [$surname, $firstName] = $this->action->callSplitName('JOHN DOE');

    expect($surname)->toBe('DOE');
    expect($firstName)->toBe('JOHN');
});

it('captures middle names', function () {
    [$surname, $firstName, $middleName] = $this->action->callSplitName('JOHN JAMES DOE');

    expect($surname)->toBe('DOE');
    expect($firstName)->toBe('JOHN');
    expect((string) $middleName)->toContain('JAMES');
});

it('handles single word name gracefully', function () {
    [$surname, $firstName] = $this->action->callSplitName('JOHN');

    expect($surname)->toBe('JOHN');
    expect($firstName)->toBe('JOHN');
});

// ── getValue ───────────────────────────────────────────────────────────

it('returns formatted value when key is present', function () {
    expect($this->action->callGetValue(['tax'], ['tax' => 5000]))->toBe('5000.00');
});

it('returns first matching key when multiple keys given', function () {
    expect($this->action->callGetValue(['nhf', 'tax'], ['tax' => 300, 'nhf' => 150]))->toBe('150.00');
});

it('returns empty string when no key matches', function () {
    expect($this->action->callGetValue(['nhf'], ['tax' => 5000]))->toBe('');
});

it('falls through to next key when first is absent', function () {
    expect($this->action->callGetValue(['nhf', 'pension'], ['pension' => 2000]))->toBe('2000.00');
});
