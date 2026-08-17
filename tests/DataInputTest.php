<?php

namespace Algorithm\C45\Tests;

use Algorithm\C45\DataInput;
use PHPUnit\Framework\TestCase;

class DataInputTest extends TestCase
{
    use PlayTennisFixture;

    public function testGetAttributesReturnsConfiguredAttributes(): void
    {
        $input = $this->makeDataInput();

        $this->assertSame($this->playTennisAttributes(), $input->getAttributes());
    }

    public function testHasAttributeDetectsKnownAndUnknownAttributes(): void
    {
        $input = $this->makeDataInput();

        $this->assertTrue($input->hasAttribute('OUTLOOK'));
        $this->assertFalse($input->hasAttribute('DOES_NOT_EXIST'));
    }

    public function testGetClassesReturnsUniqueValuesPerAttribute(): void
    {
        $input = $this->makeDataInput();

        $classes = $input->getClasses(['OUTLOOK']);

        sort($classes['OUTLOOK']);
        $this->assertSame(['Cloudy', 'Rainy', 'Sunny'], $classes['OUTLOOK']);
    }

    public function testCountByCriteriaCountsMatchingRows(): void
    {
        $input = $this->makeDataInput();

        // 5 "Sunny" rows in the Play Tennis dataset.
        $this->assertSame(5, $input->countByCriteria(['OUTLOOK' => 'Sunny']));

        // 2 of those 5 are also PLAY = Yes.
        $this->assertSame(2, $input->countByCriteria(['OUTLOOK' => 'Sunny', 'PLAY' => 'Yes']));

        $this->assertSame(0, $input->countByCriteria(['OUTLOOK' => 'DoesNotExist']));
    }

    public function testGetByCriteriaReturnsOnlyMatchingRows(): void
    {
        $input = $this->makeDataInput();

        $rows = $input->getByCriteria(['OUTLOOK' => 'Cloudy']);

        $this->assertCount(4, $rows);

        foreach ($rows as $row) {
            $this->assertSame('Cloudy', $row['OUTLOOK']);
        }
    }

    public function testGetByCriteriaRespectsLengthLimit(): void
    {
        $input = $this->makeDataInput();

        $rows = $input->getByCriteria(['OUTLOOK' => 'Sunny'], 2);

        $this->assertCount(2, $rows);
    }

    public function testGetDataWithoutArgumentsReturnsEverything(): void
    {
        $input = $this->makeDataInput();

        $this->assertCount(14, $input->getData());
    }

    public function testGetDataSliceReturnsSubset(): void
    {
        $input = $this->makeDataInput();

        $this->assertCount(3, $input->getData(0, 3));
    }
}
