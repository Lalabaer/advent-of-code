<?php

namespace AdventOfCode2024;

use Exception;

class Day8 {
    public function main(string $filename) {
        $startTime = microtime(true);

        $input = file_get_contents(__DIR__.'/'.$filename) ?: throw new Exception('Can not read input file');
        $originalRowsArray = explode("\n", $input);
        $matrix = $this->splitStringRows($originalRowsArray);
        $rows = count($matrix);
        $columns = count($matrix[0]);

        // part 1
        $antennas = $this->findAntennas($matrix, $rows);
        $placedAntiNodes = $this->placeAntiNodes($matrix, $antennas, $rows, $columns);

        echo "Part1 - It's possible to place " . count($placedAntiNodes) . " anti nodes with unique locations within our map \n";
        echo (microtime(true) - $startTime) * 1000, " ms \n";

        // part 2
        $repeatPlacementOfAntiNodes = true;
        $placedAntiNodes = $this->placeAntiNodes($matrix, $antennas, $rows, $columns, $repeatPlacementOfAntiNodes);
        echo "Part2 - It's possible to place " . count($placedAntiNodes) . " anti nodes in line with unique locations within our map \n";
        echo (microtime(true) - $startTime) * 1000, " ms \n";
    }

    private $antiNodes = [];

    private function findAntennas(array $matrix, int $rows): array {
        $antennas = [];

        foreach ($matrix as $rowIndex => $row) {
            for ($columnIndex = 0; $columnIndex < count($row); $columnIndex++) {
                if ($row[$columnIndex] === '.') {
                    continue;
                }

                $antennaToFind = $row[$columnIndex];
                $antennasInRow = array_keys($row, $antennaToFind);
                
                foreach ($antennasInRow as $antennaInRow) {
                    $antennas[$antennaToFind][] =  ['x' => $rowIndex, 'y' => $antennaInRow];
                }
            }
        }

        return $antennas;
    }

    private function placeAntiNodes(array $matrix, array $antennas, int $rows, int $columns, bool $repeatPlacement = false, bool $printMatrixWithAntiNodes = false): array {
        foreach ($antennas as $antenna => $positions) {
            foreach ($positions as $antennaIndex => $position) {
                foreach ($positions as $antennaIndex2 => $position2) {
                    if ($antennaIndex === $antennaIndex2) {
                        continue;
                    }

                    $rowDistance = $position2['x'] - $position['x'];
                    $columnDistance = $position2['y'] - $position['y'];

                    /* Why? 
                     * Example:
                     * 
                     * ............
                     * ............
                     * ........A...
                     * .........A..
                     * ............
                     * ............
                     * 
                     * Anti-nodes also on start positions
                     */
                    $startX = $repeatPlacement ? $position['x'] : $position2['x'];
                    $startY = $repeatPlacement ? $position['y'] : $position2['y'];

                    // Calculate the new positions
                    // always + is fine because we compare all antenna pairs with all antenna pairs again (so in both directions)
                    // meaning we will get the negative values as well and place anti-nodes in both directions
                    $placeAntiNodeX = $startX + $rowDistance;
                    $placeAntiNodeY = $startY + $columnDistance;

                    if ($placeAntiNodeX >= 0 && $placeAntiNodeX < $rows && $placeAntiNodeY >= 0 && $placeAntiNodeY < $columns) {
                        $this->antiNodes["$placeAntiNodeX-$placeAntiNodeY"] = ['x' => $placeAntiNodeX, 'y' => $placeAntiNodeY];
                        $matrix[$placeAntiNodeX][$placeAntiNodeY] = '#';
                    }

                    $placeAntiNodeX += $rowDistance;
                    $placeAntiNodeY += $columnDistance;

                    // part 2
                    while ($repeatPlacement && ($placeAntiNodeX >= 0 && $placeAntiNodeX < $rows && $placeAntiNodeY >= 0 && $placeAntiNodeY < $columns)) {
                        $this->antiNodes["$placeAntiNodeX-$placeAntiNodeY"] = ['x' => $placeAntiNodeX, 'y' => $placeAntiNodeY];
                        $matrix[$placeAntiNodeX][$placeAntiNodeY] = '#';

                        $placeAntiNodeX += $rowDistance;
                        $placeAntiNodeY += $columnDistance;
                    }
                }
            }
        }

        if ($printMatrixWithAntiNodes) {
            $this->printMatrix($matrix);
        }

        return $this->antiNodes;
    }

    private function printMatrix(array $matrix): void {
        foreach ($matrix as $row) {
            echo implode('', $row) . "\n";
        }
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

$Day8 = new Day8();
$Day8->main($filename);