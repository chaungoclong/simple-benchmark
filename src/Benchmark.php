<?php

namespace Chaungoclong\SimpleBenchmark;

use InvalidArgumentException;
use RuntimeException;

class Benchmark
{
    private $points = array();

    /**
     * @param $name
     *
     * @return void
     */
    public function add($name)
    {
        $this->points[$name] = microtime(true);
    }

    /**
     * @param $startPoint
     * @param $endPoint
     * @param $format
     * @param $round
     *
     * @return string
     */
    public function getTime($startPoint = null, $endPoint = null, $format = null, $round = 3)
    {
        // if the starting and ending points are not specified and the number of added points is > 2,
        // the time is calculated by the end point minus the starting point.
        if ($startPoint === null && $endPoint === null && count($this->points) >= 2) {
            $pointsValue = array_values($this->points);
            $time        = abs(array_pop($pointsValue) - array_shift($pointsValue));
            return $this->formatTime($time, $format, $round);
        }

        // Error if starting point is not specified
        if ($startPoint === null) {
            throw new InvalidArgumentException('Start point cannot be null.');
        }

        // Error if starting point value is not exists
        if (!isset($this->points[$startPoint])) {
            throw new InvalidArgumentException('Not found start point value.');
        }

        // If the end point is not specified or the end point value does not exist, the end point value is the current time.
        if (!isset($this->points[$endPoint])) {
            $time = microtime(true) - $this->points[$startPoint];
            return $this->formatTime($time, $format, $round);
        }

        $time = abs($this->points[$endPoint] - $this->points[$startPoint]);

        return $this->formatTime($time, $format, $round);
    }

    /**
     * @param $time
     * @param $format
     * @param $round
     *
     * @return string
     */
    private function formatTime($time, $format = null, $round = 3)
    {
        if (empty($format)) {
            $format = '%.3f%s';
        }

        switch (true) {
            case ($time >= 1):
                $unit = 's';
                break;
            case ($time >= 10 ** -3):
                $unit = 'ms';
                $time = round($time * 10 ** 3, $round);
                break;
            case ($time >= 10 ** -6):
                $unit = 'µs';
                $time = round($time * 10 ** 6, $round);
                break;
            default :
                $unit = 'ns';
                $time = round($time * 10 ** 9, $round);
                break;
        }

        return sprintf($format, $time, $unit);
    }

    /**
     * @param $format
     * @param $round
     * @param $real
     *
     * @return string
     */
    public function getMemory($format = null, $round = 3, $real = false)
    {
        return $this->formatMemory(memory_get_usage($real), $format, $round);
    }

    /**
     * @param $format
     * @param $round
     * @param $real
     *
     * @return string
     */
    public function getMemoryPeak($format = null, $round = 3, $real = false)
    {
        return $this->formatMemory(memory_get_peak_usage($real), $format, $round);
    }

    /**
     * @param $memory
     * @param $format
     * @param $round
     *
     * @return string
     */
    private function formatMemory($memory, $format = null, $round = 3)
    {
        if (empty($format)) {
            $format = '%.3f%s';
        }

        switch (true) {
            case ($memory >= 2 ** 40):
                $memory = round($memory / (2 ** 40), $round);
                $unit   = 'TiB';
                break;
            case ($memory >= 2 ** 30):
                $memory = round($memory / (2 ** 30), $round);
                $unit   = 'GiB';
                break;
            case ($memory >= 2 ** 20):
                $memory = round($memory / (2 ** 20), $round);
                $unit   = 'MiB';
                break;
            case ($memory >= 2 ** 10):
                $memory = round($memory / (2 ** 10), $round);
                $unit   = 'KiB';
                break;
            default:
                $unit = 'B';
                break;
        }

        return sprintf($format, $memory, $unit);
    }
}