<?php

namespace AdventOfCode2024;

use Exception;

class Day6 {
    public function main(string $filename) {
        $input = file_get_contents(__DIR__.'/'.$filename) ?: throw new Exception('Can not read input file');
        $originalRowsArray = explode("\n", $input);
        $matrix = $this->splitStringRows($originalRowsArray);
        $startPosition = $this->findStartPosition($matrix);

        $path = $this->getPath($startPosition, $matrix);
        $obstructions = $this->putObstructionsToLoop($matrix, $path, $startPosition);

        echo "Part1 - The guard visited " . count($path) . " distinct positions on the map. \n";
        echo "Part2 - There are " . count($obstructions) . " different positions to put an obstruction which results in a loop. \n";
    }

    private function splitStringRows(array $originalRowsArray): array {
        $matrix = [];

        foreach ($originalRowsArray as $row) {
            $matrix[] = str_split($row);
        }

        return $matrix;
    }

    private function findStartPosition(array $matrix): array {
        foreach ($matrix as $rowKey => $row) {
            if (in_array('^', $row)) {
                $columnKey = array_search('^', $row);

                return [$rowKey, $columnKey];
            }
        }
    }

    private function getPath(array $startPosition, array $matrix, $returnIsLoop = false): array|bool {
        $path = [];
        $obstructions = [];

        $currentPosition = $startPosition;
        $path[$currentPosition[0].'|'.$currentPosition[1]] = $currentPosition;
        
        $direction = 'up';
        $obstructionChars = ['O', '#'];

        while (true) {
            $previousPosition = $currentPosition;
            $currentPosition = $this->move($currentPosition, $direction);

            if ($this->isEnd($currentPosition, $matrix)) {
                break;
            }

            if (in_array($matrix[$currentPosition[0]][$currentPosition[1]], $obstructionChars)) {
                // so easy to check... thanks to chatGPT for the hint ;-)
                if (isset($obstructions[$currentPosition[0].'|'.$currentPosition[1].'|'.$direction])) {
                    if ($returnIsLoop) {
                        return false;
                    }
                } 
                
                $obstructions[$currentPosition[0].'|'.$currentPosition[1].'|'.$direction] = true;
                $currentPosition = $previousPosition;
                $direction = $this->changeDirection($direction);
            } else {
                $path[$currentPosition[0].'|'.$currentPosition[1]] = $currentPosition;
            }
        }

        return $path;
    }

    private function move(array $currentPosition, string $direction): array {
        switch ($direction) {
            case 'up':
                return [$currentPosition[0] - 1, $currentPosition[1]];
            case 'right':
                return [$currentPosition[0], $currentPosition[1] + 1];
            case 'down':
                return [$currentPosition[0] + 1, $currentPosition[1]];
            case 'left':
                return [$currentPosition[0], $currentPosition[1] - 1];
        }
    }

    private function isEnd(array $currentPosition, array $matrix): bool {
        return empty($matrix[$currentPosition[0]][$currentPosition[1]]);
    }

    private function changeDirection(string $direction): string {
        switch ($direction) {
            case 'up':
                return 'right';
            case 'right':
                return 'down';
            case 'down':
                return 'left';
            case 'left':
                return 'up';
        }
    }

    private function putObstructionsToLoop(array $originalMatrix, array $path, array $startPosition): array {
        $obstructions = [];
        $returnIsLoop = true;

        foreach ($path as $position) {
            // The new obstruction can't be placed at the guard's starting position - the guard is there right now and would notice.
            if ($position[0] === $startPosition[0] && $position[1] === $startPosition[1]) {
                continue;
            }

            $matrix = $originalMatrix;
            $matrix[$position[0]][$position[1]] = 'O';
            $this->printMatrix($matrix, false);

            if ($this->getPath($startPosition, $matrix, $returnIsLoop) === false) {
                $obstructions[$position[0].'|'.$position[1]] = $position;
            }
        }

        return $obstructions;
    }


    private function printMatrix(array $matrix, bool $print): void {
        if (!$print) {
            return;
        }

        echo "New Matrix: \n";

        foreach ($matrix as $row) {
            echo implode('', $row) . "\n";
        }

        echo "\n";
        echo "\n";
    }
}

count($argv) <= 1 ? $filename = 'example_data.txt' : $filename = $argv[1];

$Day6 = new Day6();
$Day6->main($filename);