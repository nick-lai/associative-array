<?php

namespace NickLai\AssociativeArray\Tests;

use PHPUnit\Framework\TestCase;
use NickLai\AssociativeArray;

class AssociativeArrayTest extends TestCase
{
    public function testSelect()
    {
        $associativeArray = new AssociativeArray([
            ['id' => 1001, 'category' => 'C', 'price' => 30],
            ['id' => 1002, 'category' => 'A', 'price' => 25],
            ['id' => 1003, 'category' => 'B', 'price' => 10],
        ]);

        $this->assertEquals([
            ['id' => 1001, 'price' => 30],
            ['id' => 1002, 'price' => 25],
            ['id' => 1003, 'price' => 10],
        ], $associativeArray->select(['id', 'price'])->toArray());
    }

    public function testWhere()
    {
        $associativeArray = new AssociativeArray([
            ['id' => 1001, 'category' => 'C', 'price' => 30],
            ['id' => 1002, 'category' => 'A', 'price' => 25],
            ['id' => 1003, 'category' => 'B', 'price' => 10],
        ]);

        $this->assertEquals([
            ['id' => 1001, 'category' => 'C', 'price' => 30],
            ['id' => 1002, 'category' => 'A', 'price' => 25],
        ], $associativeArray->where(function ($row) {
            return $row['price'] > 10;
        })->toArray());
    }

    public function testInnerJoin()
    {
        $associativeArray = new AssociativeArray([
            ['id' => 1001, 'category' => 'C', 'price' => 30],
            ['id' => 1002, 'category' => 'A', 'price' => 25],
            ['id' => 1003, 'category' => 'B', 'price' => 10],
            ['id' => 1004, 'category' => 'X', 'price' => 60],
        ]);

        $categories = [
            ['category' => 'A', 'desc' => 'A desc'],
            ['category' => 'B', 'desc' => 'B desc'],
            ['category' => 'C', 'desc' => 'C desc'],
            ['category' => 'D', 'desc' => 'D desc'],
        ];

        $this->assertEquals([
            ['id' => 1001, 'category' => 'C', 'price' => 30, 'desc' => 'C desc'],
            ['id' => 1002, 'category' => 'A', 'price' => 25, 'desc' => 'A desc'],
            ['id' => 1003, 'category' => 'B', 'price' => 10, 'desc' => 'B desc'],
        ], $associativeArray->innerJoin($categories, function ($leftRow, $rightRow) {
            return $leftRow['category'] === $rightRow['category'];
        })->toArray());
    }

    public function testLeftJoin()
    {
        $associativeArray = new AssociativeArray([
            ['id' => 1001, 'category' => 'C', 'price' => 30],
            ['id' => 1002, 'category' => 'A', 'price' => 25],
            ['id' => 1003, 'category' => 'B', 'price' => 10],
            ['id' => 1004, 'category' => 'X', 'price' => 60],
        ]);

        $categories = [
            ['category' => 'A', 'desc' => 'A desc'],
            ['category' => 'B', 'desc' => 'B desc'],
            ['category' => 'C', 'desc' => 'C desc'],
            ['category' => 'D', 'desc' => 'D desc'],
        ];

        $this->assertEquals([
            ['id' => 1001, 'category' => 'C', 'price' => 30, 'desc' => 'C desc'],
            ['id' => 1002, 'category' => 'A', 'price' => 25, 'desc' => 'A desc'],
            ['id' => 1003, 'category' => 'B', 'price' => 10, 'desc' => 'B desc'],
            ['id' => 1004, 'category' => 'X', 'price' => 60, 'desc' => null],
        ], $associativeArray->leftJoin($categories, function ($leftRow, $rightRow) {
            return $leftRow['category'] === $rightRow['category'];
        })->toArray());
    }

    public function testRightJoin()
    {
        $associativeArray = new AssociativeArray([
            ['id' => 1001, 'category' => 'C', 'price' => 30],
            ['id' => 1002, 'category' => 'A', 'price' => 25],
            ['id' => 1003, 'category' => 'B', 'price' => 10],
            ['id' => 1004, 'category' => 'X', 'price' => 60],
        ]);

        $categories = [
            ['category' => 'A', 'desc' => 'A desc'],
            ['category' => 'B', 'desc' => 'B desc'],
            ['category' => 'C', 'desc' => 'C desc'],
            ['category' => 'D', 'desc' => 'D desc'],
        ];

        $this->assertEquals([
            ['category' => 'A', 'desc' => 'A desc', 'id' => 1002, 'price' => 25],
            ['category' => 'B', 'desc' => 'B desc', 'id' => 1003, 'price' => 10],
            ['category' => 'C', 'desc' => 'C desc', 'id' => 1001, 'price' => 30],
            ['category' => 'D', 'desc' => 'D desc', 'id' => null, 'price' => null],
        ], $associativeArray->rightJoin($categories, function ($leftRow, $rightRow) {
            return $leftRow['category'] === $rightRow['category'];
        })->toArray());
    }

    public function testOrderBy()
    {
        $associativeArray = new AssociativeArray([
            ['id' => 1001, 'category' => 'C', 'price' => 10],
            ['id' => 1002, 'category' => 'A', 'price' => 25],
            ['id' => 1003, 'category' => 'B', 'price' => 10],
        ]);

        $this->assertEquals([
            ['id' => 1002, 'category' => 'A', 'price' => 25],
            ['id' => 1003, 'category' => 'B', 'price' => 10],
            ['id' => 1001, 'category' => 'C', 'price' => 10],
        ], $associativeArray->orderBy(['price', 'category'], ['desc', 'asc'])->toArray());
    }

    public function testGroupBy()
    {
        $associativeArray = new AssociativeArray([
            ['id' => 1001, 'category' => 'B', 'price' => 30],
            ['id' => 1002, 'category' => 'A', 'price' => 25],
            ['id' => 1003, 'category' => 'B', 'price' => 30],
            ['id' => 1004, 'category' => 'A', 'price' => 30],
        ]);

        $this->assertEquals([
            ['id' => 1001, 'category' => 'B', 'price' => 30],
            ['id' => 1002, 'category' => 'A', 'price' => 25],
            ['id' => 1004, 'category' => 'A', 'price' => 30],
        ], $associativeArray->groupBy(['category', 'price'])->toArray());
    }

    public function testFirst()
    {
        $associativeArray = new AssociativeArray([
            ['id' => 1001, 'category' => 'C', 'price' => 10],
            ['id' => 1002, 'category' => 'A', 'price' => 25],
            ['id' => 1003, 'category' => 'B', 'price' => 10],
        ]);

        $this->assertEquals(
            ['id' => 1001, 'category' => 'C', 'price' => 10],
            $associativeArray->first()
        );
    }

    public function testLast()
    {
        $associativeArray = new AssociativeArray([
            ['id' => 1001, 'category' => 'C', 'price' => 10],
            ['id' => 1002, 'category' => 'A', 'price' => 25],
            ['id' => 1003, 'category' => 'B', 'price' => 10],
        ]);

        $this->assertEquals(
            ['id' => 1003, 'category' => 'B', 'price' => 10],
            $associativeArray->last()
        );
    }

    public function testCount()
    {
        $associativeArray = new AssociativeArray([
            ['id' => 1001, 'category' => 'C', 'price' => 10],
            ['id' => 1002, 'category' => 'A', 'price' => 25],
            ['id' => 1003, 'category' => 'B', 'price' => 10],
        ]);

        $this->assertEquals(3, $associativeArray->count());
    }

    public function testSum()
    {
        $associativeArray = new AssociativeArray([
            ['id' => 1001, 'category' => 'C', 'price' => 10],
            ['id' => 1002, 'category' => 'A', 'price' => 25],
            ['id' => 1003, 'category' => 'B', 'price' => 10],
        ]);

        $this->assertEquals(45, $associativeArray->sum('price'));
    }

    public function testAvg()
    {
        $associativeArray = new AssociativeArray([
            ['id' => 1001, 'category' => 'C', 'price' => 10],
            ['id' => 1002, 'category' => 'A', 'price' => 25],
            ['id' => 1003, 'category' => 'B', 'price' => 10],
        ]);

        $this->assertEquals(15, $associativeArray->avg('price'));
    }

    public function testToArray()
    {
        $associativeArray = new AssociativeArray([
            ['id' => 1001, 'category' => 'C', 'price' => 10],
            ['id' => 1002, 'category' => 'A', 'price' => 25],
            ['id' => 1003, 'category' => 'B', 'price' => 10],
        ]);

        $this->assertEquals([
            ['id' => 1001, 'category' => 'C', 'price' => 10],
            ['id' => 1002, 'category' => 'A', 'price' => 25],
            ['id' => 1003, 'category' => 'B', 'price' => 10],
        ], $associativeArray->toArray());
    }

    public function testArrayAccess()
    {
        $associativeArray = new AssociativeArray([
            ['id' => 1001, 'category' => 'C', 'price' => 10],
            ['id' => 1002, 'category' => 'A', 'price' => 25],
            ['id' => 1003, 'category' => 'B', 'price' => 10],
        ]);

        $this->assertEquals(
            ['id' => 1001, 'category' => 'C', 'price' => 10],
            $associativeArray[0]
        );
    }

    public function testTraversable()
    {
        $associativeArray = new AssociativeArray([
            ['id' => 1001, 'category' => 'C', 'price' => 10],
            ['id' => 1002, 'category' => 'A', 'price' => 25],
            ['id' => 1003, 'category' => 'B', 'price' => 10],
        ]);

        foreach ($associativeArray as $row) {
            $this->assertEquals(['id' => 1001, 'category' => 'C', 'price' => 10], $row);
            return;
        }

        $this->assertEquals(0, 1);
    }
}
