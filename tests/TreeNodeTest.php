<?php

namespace Algorithm\C45\Tests;

use PHPUnit\Framework\TestCase;

class TreeNodeTest extends TestCase
{
    use PlayTennisFixture;

    public function testToArrayContainsRootAttributeAndBranches(): void
    {
        $c45 = $this->makeC45();
        $tree = $c45->buildTree();

        $array = $tree->toArray();

        $this->assertSame('OUTLOOK', $array['attribute']);
        $this->assertArrayHasKey('Sunny', $array['values']);
        $this->assertArrayHasKey('Cloudy', $array['values']);
        $this->assertArrayHasKey('Rainy', $array['values']);
    }

    public function testToJsonProducesValidDecodableJson(): void
    {
        $c45 = $this->makeC45();
        $tree = $c45->buildTree();

        $json = $tree->toJson();
        $decoded = json_decode($json, true);

        $this->assertJson($json);
        $this->assertSame('OUTLOOK', $decoded['attribute']);
    }

    public function testHasValueReturnsFalseForUnknownValueOnFreshNode(): void
    {
        $node = new \Algorithm\C45\TreeNode();

        $this->assertFalse($node->hasValue('anything'));
    }
}
