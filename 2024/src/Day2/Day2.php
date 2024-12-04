<?php

namespace AdventOfCode2024;

use Exception;

class Day2 {
    public function main(string $filename) {
        $input = file_get_contents(__DIR__.'/'.$filename) ?: throw new Exception('Can not read input file');
        $originalRowsArray = explode("\n", $input);
        $tryToFixReport = true;

        $safeReports = $this->getNumberOfSafeReports($originalRowsArray);
        $safeReportsWithCorrection = $this->getNumberOfSafeReports($originalRowsArray, $tryToFixReport);
        
        echo "Part1 - Number of safe reports is " . $safeReports . "\n";
        echo "Part2 - Number of safe reports after fixing is " . $safeReportsWithCorrection . "\n";
    }

    private function getNumberOfSafeReports(array $originalRowsArray, bool $tryToFixReport = false): int {
        $safeReports = 0;

        foreach ($originalRowsArray as $line) {
            $safe = false;
            $levels = explode(' ', $line);
            $levelDifferences = $this->getDifferencesBetweenLevels($levels);
            
            if ($this->checkIsSafeLevel($levelDifferences)) {
                $safeReports++;
                $safe = true;
            } elseif ($tryToFixReport && !$safe) {
                foreach ($levels as $key => $level) {
                    $copiedLevels = $levels;
                    unset($copiedLevels[$key]);
                    $copiedLevels = array_values($copiedLevels);
                    $levelDifferences = $this->getDifferencesBetweenLevels($copiedLevels);

                    if ($this->checkIsSafeLevel($levelDifferences)) {
                        $safeReports++;
                        break;
                    }
                }
            }
        }

        return $safeReports;
    }

    private function getDifferencesBetweenLevels(array $levels): array {
        $differences = [];

        for ($i = 0; $i < count($levels); $i++) {
            if (!empty($levels[$i+1])) {
                $differences[] = $levels[$i+1] - $levels[$i];
            }
        }

        return $differences;
    }

    private function checkIsSafeLevel(array $differences): bool {
        $maxAllowedIncrease = 3;
        $maxAllowedDecrease = -3;

        // includes no level change === UNSAFE
        if (in_array(0, $differences)) {
            return false;
        }

        $maxIncrease = max($differences);
        // only increasing levels and max difference === 3
        if (min($differences) >= 0 && $maxIncrease <= $maxAllowedIncrease) {
            return true;
        }

        $maxDecrease = min($differences);
        // only decreasing levels and max difference === -2
        if (max($differences) <= 0 && $maxDecrease >= $maxAllowedDecrease) {
            return true;
        }

        return false;
    }
}

count($argv) <= 1 ? $filename = 'example_data.txt' : $filename = $argv[1];

$Day2 = new Day2();
$Day2->main($filename);