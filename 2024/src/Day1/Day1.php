<?php

namespace AdventOfCode2024;

use Exception;

class Day1 {
    public function main(string $filename) {
        $input = file_get_contents(__DIR__.'/'.$filename) ?: throw new Exception('Can not read input file');
        $originalRowsArray = explode("\n", $input);
        $replacedLinesArray = [];

        // workable data
        $orderedLeftAndRightSides = $this->splitAndOrderList($originalRowsArray);

        //part one
        $distances = $this->calculateDistances($orderedLeftAndRightSides);
        $calculateTotalDistancePartOne = array_sum($distances);
        
        // part two
        $similarities = array_count_values($orderedLeftAndRightSides['right']);
        $distancePartTwo = $this->calculateTotalDistanceBasedOnSimilarities($orderedLeftAndRightSides['left'], $similarities);


        echo "Part1 - The total distance is " . $calculateTotalDistancePartOne . "\n";
        echo "Part2 - The total distance with similarities is " . $distancePartTwo . "\n";
    }

    private function splitAndOrderList(array $originalRowsArray): array {
        $left = [];
        $right = [];

        foreach ($originalRowsArray as $line) {
            $parts = preg_split('/\s+/', $line);
            $left[] = $parts[0];
            $right[] = $parts[1];
        }

        asort($left);
        $left = array_values($left);
        asort($right);
        $right = array_values($right);

        return ['left' => $left, 'right' => $right];
    }

    private function calculateDistances(array $orderedLeftAndRightSides): array {
        $distances = [];
        $left = $orderedLeftAndRightSides['left'];
        $right = $orderedLeftAndRightSides['right'];
        $arrayCount = count($left);

        for ($i = 0; $i < $arrayCount; $i++) {
            $distances[] = abs($right[$i] - $left[$i]);
        }

       return $distances;
    }

    private function calculateTotalDistanceBasedOnSimilarities(array $left, array $similarities): int {
        $totalDistance = 0;
        $arrayCount = count($left);

        for ($i = 0; $i < $arrayCount; $i++) {
            $totalDistance += isset($similarities[$left[$i]]) ? $left[$i] * $similarities[$left[$i]] : 0;
        }

        return $totalDistance;
    }
}

count($argv) <= 1 ? $filename = 'example_data.txt' : $filename = $argv[1];

$Day1 = new Day1();
$Day1->main($filename);