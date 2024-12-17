<?php

namespace AdventOfCode2024;

use Exception;

class Day10 {
    public function main(string $filename) {
        $startTime = microtime(true);

        $input = file_get_contents(__DIR__.'/'.$filename) ?: throw new Exception('Can not read input file');
        $originalRowsArray = explode("\n", $input);
        $matrix = $this->splitStringRows($originalRowsArray);

        $sumHikingTrails = $this->findHikingTrails($matrix);

        echo "Part 1 - The sum of the scores of all trailheads on the topographic map is $sumHikingTrails \n";
        echo (microtime(true) - $startTime) * 1000, " ms \n";

        $ratings = true;
        $sumHikingTrails = $this->findHikingTrails($matrix, $ratings);

        echo "Part 2 - The sum of the ratings of all trailheads is $sumHikingTrails \n";
        echo (microtime(true) - $startTime) * 1000, " ms \n";
    }

    private function findHikingTrails(array $matrix, bool $ratings = false): int {
        $sumHikingTrails = 0;

        foreach ($matrix as $rowIndex => $row) {
            if (!in_array(0, $row)) {
                continue;
            }

            $startPositions = array_keys($row, 0);
            $hikingTrails = [];

            foreach ($startPositions as $startPosition) {
                $validHikingTrails = $this->countValidHikingTrails($matrix, $rowIndex, $startPosition);

                $sumHikingTrails += !$ratings 
                    ? count($validHikingTrails)
                    : $this->countRatings($validHikingTrails);
            }
        }

        return $sumHikingTrails;
    }

    private function countRatings(array $validHikingTrails): int {
        $ratings = 0;

        foreach ($validHikingTrails as $hikingTrail) {
            $ratings += $hikingTrail;
        }

        return $ratings;
    }

    private function countValidHikingTrails(array $matrix, int $rowIndex, int $startPosition): array {
        $currentValue = (int)$matrix[$rowIndex][$startPosition];
        $nextValue = $currentValue + 1;
    
        if ($currentValue === 9) {
            return ["$rowIndex|$startPosition" => 1];
        }
    
        $found = [];
    
        // check up
        if (!empty($matrix[$rowIndex - 1][$startPosition]) && (int)$matrix[$rowIndex - 1][$startPosition] === $nextValue) {
            $found = $this->mergeCounts($found, $this->countValidHikingTrails($matrix, $rowIndex - 1, $startPosition));
        }
    
        // check left
        if (!empty($matrix[$rowIndex][$startPosition - 1]) && (int)$matrix[$rowIndex][$startPosition - 1] === $nextValue) {
            $found = $this->mergeCounts($found, $this->countValidHikingTrails($matrix, $rowIndex, $startPosition - 1));
        }
    
        // check right
        if (!empty($matrix[$rowIndex][$startPosition + 1]) && (int)$matrix[$rowIndex][$startPosition + 1] === $nextValue) {
            $found = $this->mergeCounts($found, $this->countValidHikingTrails($matrix, $rowIndex, $startPosition + 1));
        }
    
        // check down
        if (!empty($matrix[$rowIndex + 1][$startPosition]) && (int)$matrix[$rowIndex + 1][$startPosition] === $nextValue) {
            $found = $this->mergeCounts($found, $this->countValidHikingTrails($matrix, $rowIndex + 1, $startPosition));
        }
    
        return $found;
    }

    private function mergeCounts(array $original, array $new): array {
        foreach ($new as $key => $value) {
            if (isset($original[$key])) {
                $original[$key] += $value;
            } else {
                $original[$key] = $value;
            }
        }
        return $original;
    }

    private function splitStringRows(array $originalRowsArray): array {
        $matrix = [];

        foreach ($originalRowsArray as $row) {
            $matrix[] = str_split($row);
        }

        return $matrix;
    }
}

count($argv) <= 1 ? $filename = 'example_data.txt' : $filename = $argv[1];

$Day10 = new Day10();
$Day10->main($filename);