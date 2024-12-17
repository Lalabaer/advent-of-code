<?php

namespace AdventOfCode2024;

use Exception;

class Day11 {
    private $cache = [];

    public function main(string $filename, bool $debug) {
        $startTime = microtime(true);
        $input = file_get_contents(__DIR__.'/'.$filename) ?: throw new Exception('Can not read input file');
        $stones = explode(" ", $input);
        
        // part 1
        $blinks = 25;
        $stonesAfterBlinks = $this->blinkReturnsArrayWithStones($stones, $blinks, $debug);
        echo "Part1 - inal amount of stones: " . count($stonesAfterBlinks) . " after $blinks blinks \n";
        echo (microtime(true) - $startTime) * 1000, " ms \n";

        // part 2
        $blinks = 75;
        $stonesAfterBlinks = $this->getBlinkRecursive($stones, $blinks);
        echo "Part2 - Final amount of stones: " . $stonesAfterBlinks . " after $blinks blinks \n";
        echo (microtime(true) - $startTime) * 1000, " ms \n";
    }

    private function getBlinkRecursive(array $stones, $maxBlinks): int {
        $total = 0;

        foreach ($stones as $stone) {
            $total += $this->blink($stone, 0, $maxBlinks);
        }

        return $total;
    }

    private function blink(int $stone, int $currentBlink, int $maxBlinks): int {
        if ($currentBlink === $maxBlinks) {
            return 1;
        }

        $cacheKey = "$stone|$currentBlink";

        if (isset($this->cache[$cacheKey])) {
            return $this->cache[$cacheKey];
        }

        $stoneStr = (string)$stone;

        if ($stone == 0) {
            $result = $this->blink(1, $currentBlink + 1, $maxBlinks);
        } elseif (strlen($stoneStr) % 2 === 0) {
            $mid = strlen($stoneStr) / 2;
            $leftStone = (int)substr($stoneStr, 0, $mid);
            $rightStone = (int)substr($stoneStr, $mid);
            $result = $this->blink($leftStone, $currentBlink + 1, $maxBlinks) + $this->blink($rightStone, $currentBlink + 1, $maxBlinks);
        } else {
            $result = $this->blink($stone * 2024, $currentBlink + 1, $maxBlinks);
        }

        $this->cache[$cacheKey] = $result;

        return $result;
    }

    private function blinkReturnsArrayWithStones(array $initialStones, int $blinks, bool $debugMode): array {
        if ($blinks > 25) {
            echo "\n";
            echo "You entered a high amount of blinks ($blinks). \n";
            echo "This function doesn't work with a high amount of blinks. \n";
            echo "We are working with arrays in this solution. As it's growing exponential we will run into memory issues. \n";
            exit;
        }

        for ($i = 1; $i <= $blinks; $i++) {
            $stonesAfterBlinks = [];

            foreach ($initialStones as $stone) {
                $stoneStr = (string)$stone;

                if ($stone == 0) {
                    $stonesAfterBlinks[] = 1;
                } elseif (strlen($stoneStr) % 2 === 0) {
                    $mid = strlen($stoneStr) / 2;
                    $leftStone = (int)substr($stoneStr, 0, $mid);
                    $rightStone = (int)substr($stoneStr, $mid);
                    $stonesAfterBlinks[] = $leftStone;
                    $stonesAfterBlinks[] = $rightStone;
                } else {
                    $stonesAfterBlinks[] = $stone * 2024;
                }
            }

            if ($debugMode) {
                echo "After $i blinks: " . implode(" ", $stonesAfterBlinks) . "\n";
            }

            $initialStones = $stonesAfterBlinks;
        }

        return $stonesAfterBlinks;
    }
}

count($argv) <= 1 ? $filename = 'example_data.txt' : $filename = $argv[1];
count($argv) <= 2 ? $debug = false : $debug = $argv[2];

$Day11 = new Day11();
$Day11->main($filename, $debug);