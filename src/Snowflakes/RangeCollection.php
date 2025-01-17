<?php

namespace Snowflakes;

class RangeCollection
{
    private $ranges;

    public function __construct() {
        $this->ranges = [];
    }

    public function contains($value) {
        foreach ($this->ranges as $range) {
            if ($range[0] > $value) {
                return false;
            }
            if ($range[1] >= $value) {
                return true;
            }
        }
        return false;
    }

    public function add($value) {
        if (count($this->ranges) == 0) {
            $this->ranges[] = [$value, $value];
            return;
        }

        $ranges = $this->ranges;
        $insert = true;
        foreach ($ranges as $i => $range) {
            if ($range[0] > $value) {
                array_splice($this->ranges, $i, 0, [[$value, $value]]);
                $insert = false;
                break;
            }
            if ($range[0] <= $value && $value <= $range[1]) {
                $insert = false;
                break;
            } elseif ($value == ($range[0] - 1)) {
                $this->ranges[$i][0] = $value;
                $insert = false;
                break;
            } elseif ($value == $range[1] + 1) {
                $this->ranges[$i][1] = $value;
                $insert = false;
                break;
            }
        }
        if ($insert) {
            array_splice($this->ranges, $i, 0, [[$value, $value]]);
        }
    }

    /**
     * Glues together ranges that have joined
     */
    public function flush() {
        if (count($this->ranges) < 2) {
            return;
        }
        $ranges = [];
        $lastStart = null;
        $lastEnd = null;
        foreach ($this->ranges as $range) {
            if (is_null($lastEnd)) {
                $lastStart = $range[0];
                $lastEnd = $range[1];
                continue;
            }
            if ($range[0] <= $lastEnd + 1) {
                $lastEnd = max($lastEnd, $range[1]);
                continue;
            } else {
                $ranges[] = [$lastStart, $lastEnd];
                $lastStart = $range[0];
                $lastEnd = $range[1];
            }
        }
        $ranges[] = [$lastStart, $lastEnd];
        $this->ranges = $ranges;
    }
}
