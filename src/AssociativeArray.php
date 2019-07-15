<?php

/**
 * Associative array class.
 *
 * @author  Nick Lai <resxc13579@gmail.com>
 * @license https://opensource.org/licenses/MIT MIT
 * @link    https://github.com/nick-lai/associative-array
 */

namespace NickLai;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

class AssociativeArray implements ArrayAccess, Countable, IteratorAggregate
{
    /**
     * The rows contained in the associative array.
     *
     * @var array
     */
    protected $rows = [];

    /**
     * Create a new associative array.
     *
     * @param mixed $rows
     * @return void
     */
    public function __construct($rows = [])
    {
        $this->rows = $this->getAssociativeRows($rows);
    }

    /**
     * Create a new associative array instance.
     *
     * @param mixed $rows
     * @return static
     */
    public static function make($rows = [])
    {
        return new static($rows);
    }

    /**
     * Get rows of selected columns.
     *
     * @param string|array $keys
     * @return static
     */
    public function select($keys)
    {
        if (!is_array($keys)) {
            $keys = (array)$keys;
        }

        $keys = array_flip($keys);

        return new static(array_map(function($row) use ($keys) {
            return array_intersect_key($row, $keys);
        }, $this->rows));
    }

    /**
     * Filter the rows using the given callback.
     *
     * @param callable $callback
     * @return static
     */
    public function where(callable $callback)
    {
        return new static(array_filter($this->rows, $callback, ARRAY_FILTER_USE_BOTH));
    }

    /**
     * Inner join rows
     *
     * @param array $rows
     * @param callable $on
     * @return static
     */
    public function innerJoin($rows, callable $on)
    {
        $result = [];

        foreach ($this->rows as $leftRow) {
            foreach ($rows as $rightRow) {
                if ($on($leftRow, $rightRow)) {
                    $result[] = $leftRow + $rightRow;
                    break;
                }
            }
        }

        return new static($result);
    }

    /**
     * Left join rows
     *
     * @param array $rows
     * @param callable $on
     * @return static
     */
    public function leftJoin($rows, callable $on)
    {
        $nullRightRow = [];

        foreach ((new static($rows))->first() as $key => $value) {
            $nullRightRow[$key] = null;
        }

        $result = [];

        foreach ($this->rows as $leftRow) {
            $row = $leftRow + $nullRightRow;
            foreach ($rows as $rightRow) {
                if ($on($leftRow, $rightRow)) {
                    $row = $leftRow + $rightRow;
                    break;
                }
            }
            $result[] = $row;
        }

        return new static($result);
    }

    /**
     * Right join rows
     *
     * @param array $rows
     * @param callable $on
     * @return static
     */
    public function rightJoin($rows, callable $on)
    {
        return (new static($rows))->leftJoin($this->rows, $on);
    }

    /**
     * Order by keys
     *
     * @param string|array $keys
     * @param string|array $directions
     * @return static
     */
    public function orderBy($keys, $directions = 'asc')
    {
        if (!is_array($keys)) {
            $keys = (array)$keys;
        }

        $key2IsDesc = [];

        if (is_string($directions)) {
            $isDesc = $directions === 'desc';
            foreach ($keys as $key) {
                $key2IsDesc[$key] = $isDesc;
            }
        } else {
            $i = 0;
            foreach ($keys as $key) {
                $key2IsDesc[$key] = (($directions[$i++] ?? 'asc') === 'desc');
            }
        }

        $result = $this->rows;

        usort($result, function($a, $b) use ($keys, $key2IsDesc) {
            foreach ($keys as $key) {
                if ($comparedResult = $key2IsDesc[$key]
                        ? $b[$key] <=> $a[$key]
                        : $a[$key] <=> $b[$key]) {
                    return $comparedResult;
                }
            }
            return 0;
        });

        return new static($result);
    }

    /**
     * Groups an associative array by keys.
     *
     * @param array|string $keys
     * @return array
     */
    public function groupBy($keys)
    {
        if (!is_array($keys)) {
            $keys = (array)$keys;
        }

        return self::quickGroup($this->rows, array_reverse($keys));
    }

    /**
     * Return the first row
     *
     * @param mixed $default
     * @return mixed
     */
    public function first($default = null)
    {
        foreach ($this->rows as $row) {
            return $row;
        }

        return $default;
    }

    /**
     * Return the last row
     *
     * @param mixed $default
     * @return mixed
     */
    public function last($default = null)
    {
        foreach (array_reverse($this->rows) as $row) {
            return $row;
        }

        return $default;
    }

    /**
     * Count the number of rows in the associative array.
     *
     * @return int
     */
    public function count()
    {
        return count($this->rows);
    }

    /**
     * Get the sum of a given key.
     *
     * @param string $key
     * @return mixed
     */
    public function sum($key)
    {
        return array_sum(array_column($this->rows, $key));
    }

    /**
     * Get the average value of a given key.
     *
     * @param string $key
     * @return mixed
     */
    public function avg($key)
    {
        $sum = $this->sum($key);
        return $sum ? ($sum / $this->count()) : $sum;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return array_map(function($row) {
            return $row instanceof self ? $row->toArray() : $row;
        }, $this->rows);
    }

    /**
     * Get an iterator for the rows.
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->rows);
    }

    /**
     * Determine if a row exists at an offset.
     *
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->rows);
    }

    /**
     * Get a row at a given offset.
     *
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->rows[$offset];
    }

    /**
     * Set the row at a given offset.
     *
     * @param mixed $offset
     * @param mixed $row
     * @return void
     */
    public function offsetSet($offset, $row)
    {
        if (is_null($offset)) {
            $this->rows[] = $row;
        } else {
            $this->rows[$offset] = $row;
        }
    }

    /**
     * Unset the row at a given offset.
     *
     * @param string $offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->rows[$offset]);
    }

    /**
     * Quick grouping.
     *
     * @param array $rows
     * @param array $keys
     *
     * @return array
     */
    protected static function quickGroup(array $rows, array $keys)
    {
        $result = [];

        $groupKey = array_pop($keys);

        foreach ($rows as $row) {
            $result[$row[$groupKey]][] = $row;
        }

        if (count($keys)) {
            foreach ($result as $groupBy => $groupedRows) {
                $result[$groupBy] = self::quickGroup($groupedRows, $keys);
            }
        }

        return $result;
    }

    /**
     * Results array of rows from associative array or traversable.
     *
     * @param mixed $rows
     * @return array
     */
    protected function getAssociativeRows($rows)
    {
        if (is_array($rows)) {
            return $rows;
        } elseif ($rows instanceof self) {
            return $rows->toArray();
        } elseif ($rows instanceof Traversable) {
            return iterator_to_array($rows);
        }

        return (array)$rows;
    }
}
