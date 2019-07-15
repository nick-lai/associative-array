<?php

namespace NickLai\AssociativeArray\Tests;

use PHPUnit\Framework\TestCase;
use NickLai\AssociativeArray;

use ArrayAccess;
use ArrayIterator;
use Countable;
use ReflectionClass;
use Traversable;

class AssociativeArrayTest extends TestCase
{
    public function testMakeMethod()
    {
        $data = [
            ['id' => 1001, 'category' => 'C', 'price' => 30],
            ['id' => 1002, 'category' => 'A', 'price' => 25],
            ['id' => 1003, 'category' => 'B', 'price' => 10],
        ];

        $this->assertEquals($data, AssociativeArray::make($data)->toArray());
    }

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

        $this->assertEquals([
            ['category' => 'C'],
            ['category' => 'A'],
            ['category' => 'B'],
        ], $associativeArray->select('category')->toArray());
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
            ['id' => 1004, 'category' => 'C', 'price' => 10],
        ]);

        $this->assertEquals([
            ['id' => 1002, 'category' => 'A', 'price' => 25],
            ['id' => 1003, 'category' => 'B', 'price' => 10],
            ['id' => 1001, 'category' => 'C', 'price' => 10],
            ['id' => 1004, 'category' => 'C', 'price' => 10],
        ], $associativeArray->orderBy(['price', 'category'], ['desc', 'asc'])->toArray());

        $this->assertEquals([
            ['id' => 1004, 'category' => 'C', 'price' => 10],
            ['id' => 1003, 'category' => 'B', 'price' => 10],
            ['id' => 1002, 'category' => 'A', 'price' => 25],
            ['id' => 1001, 'category' => 'C', 'price' => 10],
        ], $associativeArray->orderBy('id', 'desc')->toArray());
    }

    public function testGroupBy()
    {
        $data = [
            ['id' => 1001, 'category' => 'B', 'price' => 30],
            ['id' => 1002, 'category' => 'A', 'price' => 25],
            ['id' => 1003, 'category' => 'B', 'price' => 30],
            ['id' => 1004, 'category' => 'A', 'price' => 30],
        ];

        $associativeArray = new AssociativeArray($data);

        $this->assertEquals([
            'B' => [
                30 => [
                    ['id' => 1001, 'category' => 'B', 'price' => 30],
                    ['id' => 1003, 'category' => 'B', 'price' => 30],
                ],
            ],
            'A' => [
                25 => [
                    ['id' => 1002, 'category' => 'A', 'price' => 25],
                ],
                30 => [
                    ['id' => 1004, 'category' => 'A', 'price' => 30],
                ],
            ],
        ], $associativeArray->groupBy(['category', 'price']));

        $this->assertEquals([
            'B' => [
                ['id' => 1001, 'category' => 'B', 'price' => 30],
                ['id' => 1003, 'category' => 'B', 'price' => 30],
            ],
            'A' => [
                ['id' => 1002, 'category' => 'A', 'price' => 25],
                ['id' => 1004, 'category' => 'A', 'price' => 30],
            ],
        ], $associativeArray->groupBy('category'));
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

        $this->assertNull((new AssociativeArray())->first());
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

        $this->assertNull((new AssociativeArray())->last());
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

        $this->assertInstanceOf(ArrayAccess::class, $associativeArray);

        $this->assertEquals(
            ['id' => 1001, 'category' => 'C', 'price' => 10],
            $associativeArray[0]
        );

        $associativeArray[] = ['id' => 1004, 'category' => 'D', 'price' => 50];

        $this->assertTrue(isset($associativeArray[3]));
        $this->assertEquals(4, count($associativeArray));
        $this->assertEquals(1004, $associativeArray[3]['id']);

        unset($associativeArray[3]);

        $this->assertFalse(isset($associativeArray[3]));
        $this->assertEquals(3, count($associativeArray));
    }

    public function testCountable()
    {
        $associativeArray = new AssociativeArray([
            ['id' => 1001, 'category' => 'C', 'price' => 10],
            ['id' => 1002, 'category' => 'A', 'price' => 25],
            ['id' => 1003, 'category' => 'B', 'price' => 10],
        ]);

        $this->assertInstanceOf(Countable::class, $associativeArray);
        $this->assertCount(3, $associativeArray);
    }

    public function testTraversable()
    {
        $data = [
            ['id' => 1001, 'category' => 'C', 'price' => 10],
            ['id' => 1002, 'category' => 'A', 'price' => 25],
        ];

        $associativeArray = new AssociativeArray($data);

        $this->assertInstanceOf(Traversable::class, $associativeArray);

        foreach ($associativeArray as $index => $row) {
            $this->assertEquals($data[$index], $row);
        }
    }

    public function testIterable()
    {
        $data = [
            ['id' => 1001, 'category' => 'C', 'price' => 10],
            ['id' => 1002, 'category' => 'A', 'price' => 25],
            ['id' => 1003, 'category' => 'B', 'price' => 10],
        ];

        $associativeArray = new AssociativeArray($data);

        $this->assertInstanceOf(ArrayIterator::class, $associativeArray->getIterator());
        $this->assertEquals($data, $associativeArray->getIterator()->getArrayCopy());
    }

    public function testArrayAccessOffsetExists()
    {
        $associativeArray = new AssociativeArray([
            ['id' => 1001, 'category' => 'C', 'price' => 10],
            ['id' => 1002, 'category' => 'A', 'price' => 25],
            ['id' => 1003, 'category' => 'B', 'price' => 10],
        ]);

        $this->assertTrue($associativeArray->offsetExists(0));
        $this->assertTrue($associativeArray->offsetExists(1));
        $this->assertFalse($associativeArray->offsetExists(1000));
    }

    public function testArrayAccessOffsetGet()
    {
        $data = [
            ['id' => 1001, 'category' => 'C', 'price' => 10],
            ['id' => 1002, 'category' => 'A', 'price' => 25],
            ['id' => 1003, 'category' => 'B', 'price' => 10],
        ];

        $associativeArray = new AssociativeArray($data);

        $this->assertEquals($data[0], $associativeArray->offsetGet(0));
        $this->assertEquals($data[1], $associativeArray->offsetGet(1));
        $this->assertEquals($data[2], $associativeArray->offsetGet(2));
    }

    public function testArrayAccessOffsetSet()
    {
        $data = [
            ['id' => 1001, 'category' => 'C', 'price' => 10],
            ['id' => 1002, 'category' => 'A', 'price' => 25],
            ['id' => 1003, 'category' => 'B', 'price' => 10],
        ];

        $associativeArray = new AssociativeArray($data);

        $row = ['id' => 1004, 'category' => 'D', 'price' => 50];

        $associativeArray->offsetSet(0, $row);
        $this->assertEquals($row, $associativeArray->first());

        $associativeArray->offsetSet(null, $row);
        $this->assertEquals($row, $associativeArray->last());
    }

    public function testArrayAccessOffsetUnset()
    {
        $associativeArray = new AssociativeArray([
            ['id' => 1001, 'category' => 'C', 'price' => 10],
            ['id' => 1002, 'category' => 'A', 'price' => 25],
            ['id' => 1003, 'category' => 'B', 'price' => 10],
        ]);

        $associativeArray->offsetUnset(2);
        $this->assertFalse(isset($associativeArray[2]));
    }

    public function testGetAssociativeRows()
    {
        $associativeArray = new AssociativeArray;
        $class = new ReflectionClass($associativeArray);
        $method = $class->getMethod('getAssociativeRows');
        $method->setAccessible(true);

        $data = [
            ['id' => 1001, 'category' => 'C', 'price' => 10],
            ['id' => 1002, 'category' => 'A', 'price' => 25],
            ['id' => 1003, 'category' => 'B', 'price' => 10],
        ];

        $this->assertSame($data, $method->invokeArgs($associativeArray, [$data]));
        $this->assertSame($data, $method->invokeArgs($associativeArray, [new AssociativeArray($data)]));
        $this->assertSame($data, $method->invokeArgs($associativeArray, [(new AssociativeArray($data))->getIterator()]));
        $this->assertSame($data, $method->invokeArgs($associativeArray, [(object) $data]));
    }
}
