<?php

namespace AdventOfCode2024;

use Exception;

class Day12 {
    private $usedForArea = [];

    private $corners = 0;

    public function main(string $filename) {
        $startTime = microtime(true);
        $input = file_get_contents(__DIR__.'/'.$filename) ?: throw new Exception('Can not read input file');
        $originalRowsArray = explode("\n", $input);
        $matrix = $this->splitStringRows($originalRowsArray);

        // part 1
        $price = $this->findGardenPlotAreas($matrix);

        echo "Part1 - The total price of fencing all regions is " . $price . "\n";
        echo (microtime(true) - $startTime) * 1000, " ms \n";

        // part 2
        $this->cleanUsedAreas();

        $withDiscount = true;
        $price = $this->findGardenPlotAreas($matrix, $withDiscount);

        echo "Part2 - The new total price of fencing all regions with discount is " . $price . "\n";
        echo (microtime(true) - $startTime) * 1000, " ms \n";
    }

    private function findGardenPlotAreas(array $matrix, bool $withDiscount = false): int {
        $totalPrice = 0;

        foreach ($matrix as $rowIndex => $row) {
            for ($columnIndex = 0; $columnIndex < count($row); $columnIndex++) {
                if (!empty($this->usedForArea[$rowIndex][$columnIndex])) {
                    continue;
                }

                $this->resetCorners();
                $gardenPlotAreas = $this->gardenPlotAreaInfos($matrix, $rowIndex, $columnIndex);

                if ($withDiscount === true) {
                    $totalPrice += $gardenPlotAreas['area'] * $this->corners;
                } else {
                    $totalPrice += $gardenPlotAreas['area'] * $gardenPlotAreas['perimeter'];
                }

            }
        }

        return $totalPrice;
    }

    private function gardenPlotAreaInfos(array $matrix, int $rowIndex, int $columnIndex, string $plant = ''): array {
        $plant = empty($plant)
            ? $matrix[$rowIndex][$columnIndex]
            : $plant;

        if (!empty($this->usedForArea[$rowIndex][$columnIndex]) || $matrix[$rowIndex][$columnIndex] !== $plant) {
            return ['area' => 0, 'perimeter' => 0, 'plant' => $plant];
        }

        $area = 1;
        $perimeter = 0;
        $this->usedForArea[$rowIndex][$columnIndex] = true;

        $up = $left = $right = $down = true;

        // check up
        if (!empty($matrix[$rowIndex - 1][$columnIndex]) && $matrix[$rowIndex - 1][$columnIndex] === $plant) {
            $infos = $this->gardenPlotAreaInfos($matrix, $rowIndex - 1, $columnIndex, $plant);
            
            $area += $infos['area'];
            $perimeter += $infos['perimeter'];
        } else {
            $perimeter++;
            $up = false;
        }
    
        // check left
        if (!empty($matrix[$rowIndex][$columnIndex - 1]) && $matrix[$rowIndex][$columnIndex - 1] === $plant) {
            $infos = $this->gardenPlotAreaInfos($matrix, $rowIndex, $columnIndex - 1, $plant);
            
            $area += $infos['area'];
            $perimeter += $infos['perimeter'];
        } else {
            $perimeter++;
            $left = false;
        }
    
        // check right
        if (!empty($matrix[$rowIndex][$columnIndex + 1]) && $matrix[$rowIndex][$columnIndex + 1] === $plant) {
            $infos = $this->gardenPlotAreaInfos($matrix, $rowIndex, $columnIndex + 1, $plant);
            
            $area += $infos['area'];
            $perimeter += $infos['perimeter'];
        } else {
            $perimeter++;
            $right = false;
        }
    
        // check down
        if (!empty($matrix[$rowIndex + 1][$columnIndex]) && $matrix[$rowIndex + 1][$columnIndex] === $plant) {
            $infos = $this->gardenPlotAreaInfos($matrix, $rowIndex + 1, $columnIndex, $plant);
            
            $area += $infos['area'];
            $perimeter += $infos['perimeter'];
        } else {
            $perimeter++;
            $down = false;
        }

        // counting corners to get number of sides
        // drawing on paper helped a lot to understand the logic
        if (!$up && !$left) $this->corners++;
        if (!$up && !$right) $this->corners++;
        if (!$down && !$left) $this->corners++;
        if (!$down && !$right) $this->corners++;
        if ($down && $right && !empty($matrix[$rowIndex + 1][$columnIndex+1]) && $matrix[$rowIndex + 1][$columnIndex+1] !== $plant) $this->corners++;
        if ($down && $left && !empty($matrix[$rowIndex + 1][$columnIndex-1]) && $matrix[$rowIndex + 1][$columnIndex-1] !== $plant) $this->corners++;
        if ($up && $right && !empty($matrix[$rowIndex - 1][$columnIndex+1]) && $matrix[$rowIndex - 1][$columnIndex+1] !== $plant) $this->corners++;
        if ($up && $left && !empty($matrix[$rowIndex - 1][$columnIndex-1]) && $matrix[$rowIndex - 1][$columnIndex-1] !== $plant) $this->corners++;

        $gardenPlotAreas = [
            'area' => $area,
            'perimeter' => $perimeter,
            'plant' => $plant,
        ];

        return $gardenPlotAreas;
    }


    private function splitStringRows(array $originalRowsArray): array {
        $matrix = [];

        foreach ($originalRowsArray as $row) {
            $matrix[] = str_split($row);
        }

        return $matrix;
    }

    private function resetCorners() {
        $this->corners = 0;
    }

    private function cleanUsedAreas() {
        $this->usedForArea = [];
    }
}

count($argv) <= 1 ? $filename = 'example_data.txt' : $filename = $argv[1];

$Day12 = new Day12();
$Day12->main($filename);