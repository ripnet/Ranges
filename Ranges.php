<?php
/**
 * Class Ranges
 *
 * Container for storing ranges (start, end), summarizing them, and grabbing the first x elements
 * with the ability to remove those elements from the range container.
 *
 * @author Tom Young <ripnet@gmail.com>
 *
 */
class Ranges {
    private $ranges = array();
    private $count = 0;

    /**
     * Add a range to the container.
     *
     * @param int $start First integer in the range
     * @param int $stop Second integer ihe range
     * @param bool $coalesce Whether or not to coalesce after adding. Set to false if adding several ranges, then call coalesce() manually
     */
    public function addRange($start, $stop, $coalesce = true)
    {
        $this->ranges[] = [$start, $stop];
        if ($coalesce)
            $this->coalesce();
    }

    /**
     * Summarize ranges.
     *
     * This loops through all the ranges, and sorts them and summarizes them.
     * For example, [1,3] and [3,6] will become [1,6]
     */
    public function coalesce()
    {
        $this->count = 0;
        usort($this->ranges, array($this, 'rSort'));
        for ($i = 0; $i < (count($this->ranges) - 1); $i++) {
            if ($this->ranges[$i + 1][0] <= ($this->ranges[$i][1] + 1)) {
                $k = $i + 1;
                $max = max($this->ranges[$i][1], $this->ranges[$k][1]);
                while ($k < (count($this->ranges) - 1)) {
                    if ($this->ranges[$k + 1][0] <= $this->ranges[$k][1] + 1) {
                        $k++;
                        $max = max($max, $this->ranges[$k][1]);
                    } else {
                        break;
                    }
                }
                $this->ranges[$i][1] = $max;
                foreach (range($i + 1, $k) as $key) {
                    unset($this->ranges[$key]);
                }
                $this->ranges = array_values($this->ranges);
            }
        }
        foreach ($this->ranges as $r) {
            $this->count += $r[1] - $r[0] + 1;
        }
    }

    /**
     * Return the number of elements in all the ranges.
     *
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }


    /**
     * Returns an array of all ranges in the container.
     *
     * @return array
     */
    public function getAllRanges()
    {
        return $this->ranges;
    }

    /**
     * Return a Ranges containing the first $x numbers in this Ranges.
     *
     * @param int $x Number of elements to seek.
     * @param bool $destroy If true, modify to remove the numbers that were returned.
     * @return Ranges
     */
    public function getFirst($x, $destroy = true)
    {
        $a = new Ranges();
        $c = 0;
        if ($this->getCount()) {
            $index = 0;
            while ($c < min($x, $this->getCount())) {
                $currentCount = $this->count($this->ranges[$index]);
                if ($currentCount == ($x - $c)) {
                    $a->addRange($this->ranges[$index][0], $this->ranges[$index][1]);
                    $c += $currentCount;
                    if ($destroy) {
                        unset($this->ranges[$index]);
                    }
                } elseif ($currentCount > ($x - $c)) {
                    $a->addRange($this->ranges[$index][0], $this->ranges[$index][0] + $x - $c - 1);
                    if ($destroy) {
                        $this->ranges[$index][0] = $this->ranges[$index][0] + $x - $c;
                    }
                    $c += $x;
                } else {
                    $a->addRange($this->ranges[$index][0], $this->ranges[$index][1]);
                    $c += $this->ranges[$index][1] - $this->ranges[$index][0] + 1;
                    if ($destroy) {
                        unset($this->ranges[$index]);
                    }
                }
                $index++;
            }
        }
        return $a;
    }

    /**
     * Helper function to assist with sorting the range elements.
     *
     * @param int $a
     * @param int $b
     * @return int mixed
     */
    private function rSort($a, $b)
    {
        return $a[0] - $b[0];
    }

    /**
     * Return the count of number in a range, given the array [start, stop]
     *
     * @param array $element
     * @return int mixed
     */
    private function count($element)
    {
        return $element[1] - $element[0] + 1;
    }


}