<?php

namespace Algorithm\C45\Tests;

use Algorithm\C45\TreeNode;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class C45Test extends TestCase
{
    use PlayTennisFixture;

    public function testSetTargetAttributeFallsBackToLastAttributeWhenInvalid(): void
    {
        $c45 = new \Algorithm\C45();
        $c45->c45 = $this->makeDataInput();
        $c45->setTargetAttribute('NOT_AN_ATTRIBUTE');

        $this->assertSame('PLAY', $c45->target_attribute);
    }

    public function testBuildTreeRootSplitsOnOutlook(): void
    {
        $c45 = $this->makeC45();

        $tree = $c45->buildTree();

        $this->assertInstanceOf(TreeNode::class, $tree);
        $this->assertSame('OUTLOOK', $tree->getAttributeName());
    }

    public function testBuildTreeCloudyBranchIsAPureLeaf(): void
    {
        // Every "Cloudy" row in the dataset has PLAY = Yes, so that
        // branch should terminate immediately in a leaf node.
        $c45 = $this->makeC45();
        $tree = $c45->buildTree();

        $cloudyBranch = $tree->getChild('Cloudy');

        $this->assertTrue($cloudyBranch->getIsLeaf());
        $this->assertSame('Yes', $cloudyBranch->getChild('result'));
    }

    #[DataProvider('classificationProvider')]
    public function testClassifyMatchesExpectedLabel(array $newRow, string $expected): void
    {
        $c45 = $this->makeC45();
        $tree = $c45->buildTree();

        $this->assertSame($expected, $tree->classify($newRow));
    }

    public function classificationProvider(): array
    {
        return [
            'sunny, high humidity -> No' => [
                ['OUTLOOK' => 'Sunny', 'TEMPERATURE' => 'Hot', 'HUMIDITY' => 'High', 'WINDY' => 'False'],
                'No',
            ],
            'sunny, normal humidity -> Yes' => [
                ['OUTLOOK' => 'Sunny', 'TEMPERATURE' => 'Cool', 'HUMIDITY' => 'Normal', 'WINDY' => 'False'],
                'Yes',
            ],
            'cloudy -> always Yes' => [
                ['OUTLOOK' => 'Cloudy', 'TEMPERATURE' => 'Hot', 'HUMIDITY' => 'High', 'WINDY' => 'True'],
                'Yes',
            ],
            'rainy, windy -> No' => [
                ['OUTLOOK' => 'Rainy', 'TEMPERATURE' => 'Mild', 'HUMIDITY' => 'Normal', 'WINDY' => 'True'],
                'No',
            ],
            'rainy, calm -> Yes' => [
                ['OUTLOOK' => 'Rainy', 'TEMPERATURE' => 'Mild', 'HUMIDITY' => 'Normal', 'WINDY' => 'False'],
                'Yes',
            ],
        ];
    }

    public function testClassifyReturnsUnclassifiedForUnseenAttributeValue(): void
    {
        $c45 = $this->makeC45();
        $tree = $c45->buildTree();

        $result = $tree->classify([
            'OUTLOOK' => 'Snowy', // never seen during training
            'TEMPERATURE' => 'Hot',
            'HUMIDITY' => 'High',
            'WINDY' => 'False',
        ]);

        $this->assertSame('unclassified', $result);
    }

    public function testEvaluateReportsPerfectAccuracyOnTrainingData(): void
    {
        // A fully-grown, unpruned tree should perfectly fit the data
        // it was built from.
        $c45 = $this->makeC45();
        $tree = $c45->buildTree();

        $result = $c45->evaluate($tree, $this->playTennisData());

        $this->assertSame(1.0, $result['accuracy']);
        $this->assertSame(14, $result['total']);
        $this->assertSame(14, $result['correct']);
        $this->assertCount(0, $result['misclassified']);
    }

    public function testEvaluateReportsMisclassifiedRows(): void
    {
        $c45 = $this->makeC45();
        $tree = $c45->buildTree();

        $testData = $this->playTennisData();
        // Deliberately mislabel one row to check evaluate() catches it.
        $testData[0]['PLAY'] = 'Yes';

        $result = $c45->evaluate($tree, $testData);

        $this->assertSame(13, $result['correct']);
        $this->assertCount(1, $result['misclassified']);
        $this->assertSame(0, $result['misclassified'][0]['index']);
    }

    public function testBuildTreeToStringContainsRootAttribute(): void
    {
        $c45 = $this->makeC45();
        $tree = $c45->buildTree();

        $this->assertStringContainsString('OUTLOOK', $tree->toString());
    }

    public function testBuildTreeToDotProducesValidDigraph(): void
    {
        $c45 = $this->makeC45();
        $tree = $c45->buildTree();

        $dot = $tree->toDot();

        $this->assertStringStartsWith('digraph C45Tree {', $dot);
        $this->assertStringEndsWith('}', $dot);
        $this->assertStringContainsString('OUTLOOK', $dot);
    }
}