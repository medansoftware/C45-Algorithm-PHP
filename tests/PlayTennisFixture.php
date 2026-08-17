<?php

namespace Algorithm\C45\Tests;

/**
 * The classic "Play Tennis" dataset (Quinlan), used throughout the
 * test suite because its correct Gain / Gain Ratio / tree shape values
 * are well documented and easy to verify independently.
 */
trait PlayTennisFixture
{
    protected function playTennisData(): array
    {
        return [
            ['OUTLOOK' => 'Sunny', 'TEMPERATURE' => 'Hot', 'HUMIDITY' => 'High', 'WINDY' => 'False', 'PLAY' => 'No'],
            ['OUTLOOK' => 'Sunny', 'TEMPERATURE' => 'Hot', 'HUMIDITY' => 'High', 'WINDY' => 'True', 'PLAY' => 'No'],
            ['OUTLOOK' => 'Cloudy', 'TEMPERATURE' => 'Hot', 'HUMIDITY' => 'High', 'WINDY' => 'False', 'PLAY' => 'Yes'],
            ['OUTLOOK' => 'Rainy', 'TEMPERATURE' => 'Mild', 'HUMIDITY' => 'High', 'WINDY' => 'False', 'PLAY' => 'Yes'],
            ['OUTLOOK' => 'Rainy', 'TEMPERATURE' => 'Cool', 'HUMIDITY' => 'Normal', 'WINDY' => 'False', 'PLAY' => 'Yes'],
            ['OUTLOOK' => 'Rainy', 'TEMPERATURE' => 'Cool', 'HUMIDITY' => 'Normal', 'WINDY' => 'True', 'PLAY' => 'No'],
            ['OUTLOOK' => 'Cloudy', 'TEMPERATURE' => 'Cool', 'HUMIDITY' => 'Normal', 'WINDY' => 'True', 'PLAY' => 'Yes'],
            ['OUTLOOK' => 'Sunny', 'TEMPERATURE' => 'Mild', 'HUMIDITY' => 'High', 'WINDY' => 'False', 'PLAY' => 'No'],
            ['OUTLOOK' => 'Sunny', 'TEMPERATURE' => 'Cool', 'HUMIDITY' => 'Normal', 'WINDY' => 'False', 'PLAY' => 'Yes'],
            ['OUTLOOK' => 'Rainy', 'TEMPERATURE' => 'Mild', 'HUMIDITY' => 'Normal', 'WINDY' => 'False', 'PLAY' => 'Yes'],
            ['OUTLOOK' => 'Sunny', 'TEMPERATURE' => 'Mild', 'HUMIDITY' => 'Normal', 'WINDY' => 'True', 'PLAY' => 'Yes'],
            ['OUTLOOK' => 'Cloudy', 'TEMPERATURE' => 'Mild', 'HUMIDITY' => 'High', 'WINDY' => 'True', 'PLAY' => 'Yes'],
            ['OUTLOOK' => 'Cloudy', 'TEMPERATURE' => 'Hot', 'HUMIDITY' => 'Normal', 'WINDY' => 'False', 'PLAY' => 'Yes'],
            ['OUTLOOK' => 'Rainy', 'TEMPERATURE' => 'Mild', 'HUMIDITY' => 'High', 'WINDY' => 'True', 'PLAY' => 'No'],
        ];
    }

    protected function playTennisAttributes(): array
    {
        return ['OUTLOOK', 'TEMPERATURE', 'HUMIDITY', 'WINDY', 'PLAY'];
    }

    protected function makeDataInput(): \Algorithm\C45\DataInput
    {
        $input = new \Algorithm\C45\DataInput();
        $input->setAttributes($this->playTennisAttributes());
        $input->setData($this->playTennisData());

        return $input;
    }

    protected function makeC45(): \Algorithm\C45
    {
        $c45 = new \Algorithm\C45();
        $c45->c45 = $this->makeDataInput();
        $c45->setTargetAttribute('PLAY');
        $c45->initialize();

        return $c45;
    }
}
