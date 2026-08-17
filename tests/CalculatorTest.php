<?php

namespace Algorithm\C45\Tests;

use Algorithm\C45\Calculator\GainCalculator;
use Algorithm\C45\Calculator\GainRatioCalculator;
use Algorithm\C45\Calculator\SplitInfoCalculator;
use PHPUnit\Framework\TestCase;

/**
 * Expected values below are the well-known textbook figures for the
 * "Play Tennis" dataset (Quinlan), rounded to 4 decimal places.
 */
class CalculatorTest extends TestCase
{
    use PlayTennisFixture;

    public function testGainOfEachAttributeMatchesKnownValues(): void
    {
        $input = $this->makeDataInput();
        $gainCalculator = new GainCalculator($input, 'PLAY');

        $this->assertEqualsWithDelta(0.2467, $gainCalculator->calculateGainOfAttribute('OUTLOOK'), 0.001);
        $this->assertEqualsWithDelta(0.1518, $gainCalculator->calculateGainOfAttribute('HUMIDITY'), 0.001);
        $this->assertEqualsWithDelta(0.0481, $gainCalculator->calculateGainOfAttribute('WINDY'), 0.001);
        $this->assertEqualsWithDelta(0.0292, $gainCalculator->calculateGainOfAttribute('TEMPERATURE'), 0.001);
    }

    public function testGainOfAllAttributesExcludesTargetAttribute(): void
    {
        $input = $this->makeDataInput();
        $gainCalculator = new GainCalculator($input, 'PLAY');

        $gain = $gainCalculator->calculateGainAllAttributes();

        $this->assertArrayNotHasKey('PLAY', $gain);
        $this->assertArrayHasKey('OUTLOOK', $gain);
        $this->assertArrayHasKey('HUMIDITY', $gain);
        $this->assertArrayHasKey('WINDY', $gain);
        $this->assertArrayHasKey('TEMPERATURE', $gain);
    }

    public function testOutlookHasHighestGainAmongAllAttributes(): void
    {
        $input = $this->makeDataInput();
        $gainCalculator = new GainCalculator($input, 'PLAY');

        $gain = $gainCalculator->calculateGainAllAttributes();
        arsort($gain);

        $this->assertSame('OUTLOOK', array_key_first($gain));
    }

    public function testSplitInfoIsPositiveForAttributeWithMultipleValues(): void
    {
        $input = $this->makeDataInput();
        $splitInfoCalculator = new SplitInfoCalculator($input, 'PLAY');

        // OUTLOOK splits 14 rows into 5/4/5 -> split info > 0
        $splitInfo = $splitInfoCalculator->calculateSplitInfoOfAttribute('OUTLOOK');

        $this->assertGreaterThan(0, $splitInfo);
        $this->assertEqualsWithDelta(1.5774, $splitInfo, 0.001);
    }

    public function testGainRatioIsZeroWhenSplitInfoIsZero(): void
    {
        $input = $this->makeDataInput();
        $gainRatioCalculator = new GainRatioCalculator($input, 'PLAY');

        $gainRatio = $gainRatioCalculator->calculateGainRatio(
            ['OUTLOOK' => 0.5],
            ['OUTLOOK' => 0]
        );

        $this->assertSame(0, $gainRatio['OUTLOOK']);
    }

    public function testGainRatioDividesGainBySplitInfo(): void
    {
        $input = $this->makeDataInput();
        $gainRatioCalculator = new GainRatioCalculator($input, 'PLAY');

        $gainRatio = $gainRatioCalculator->calculateGainRatio(
            ['OUTLOOK' => 0.246],
            ['OUTLOOK' => 1.577]
        );

        $this->assertEqualsWithDelta(0.156, $gainRatio['OUTLOOK'], 0.001);
    }
}
