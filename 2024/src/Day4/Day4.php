<?php

namespace AdventOfCode2024;

use Exception;

class Day4 {
    public function main(string $filename) {
        $input = file_get_contents(__DIR__.'/'.$filename) ?: throw new Exception('Can not read input file');
        $originalRowsArray = explode("\n", $input);
        $wordCounts = $this->findXmasOccurrences($originalRowsArray);

        echo "Part1 - I count XMAS " . $wordCounts[1] . " times, how about you? \n"; 
        echo "Part2 - Ahhh... it's X-MAS: I count it " . $wordCounts[2] . " times. \n"; 
    }

    private function formatData(array $formattedData, string $row, int $index): array {
        $formattedData[$index] = str_split($row);
        return $formattedData;
    }

    private function findXmasOccurrences(array $originalRowsArray): array {
        $xmasWordCount = 0;
        $x_masWordCount = 0;
        $formattedData = [];

        foreach ($originalRowsArray as $index => $rowString) {
            $xmasWordCount += $this->countHorizontal($rowString);
            $formattedData = $this->formatData($formattedData, $rowString, $index);
        }

        $xmasWordCount += $this->countVertical($formattedData);
        $xmasWordCount += $this->countDiagonal($formattedData);
        $x_masWordCount += $this->countX_MAS($formattedData);

        return [1 => $xmasWordCount, 2 => $x_masWordCount];
    }

    private function countHorizontal(string $rowString): int {
        $wordCount = 0;

        $wordCount += substr_count($rowString, 'XMAS');
        $wordCount += substr_count($rowString, 'SAMX');

        return $wordCount;
    }

    private function countVertical(array $formattedData): int {
        $wordCount = 0;

        foreach ($formattedData as $rowIndex => $row) {
            foreach ($row as $characterIndex => $character) {
                if ($character === 'X' 
                        && !empty($formattedData[$rowIndex+1][$characterIndex]) && $formattedData[$rowIndex+1][$characterIndex] === 'M' 
                        && !empty($formattedData[$rowIndex+2][$characterIndex]) && $formattedData[$rowIndex+2][$characterIndex] === 'A'
                        && !empty($formattedData[$rowIndex+3][$characterIndex]) && $formattedData[$rowIndex+3][$characterIndex] === 'S') {
                    $wordCount++;
                }

                if ($character === 'X' 
                        && !empty($formattedData[$rowIndex-1][$characterIndex]) && $formattedData[$rowIndex-1][$characterIndex] === 'M' 
                        && !empty($formattedData[$rowIndex-2][$characterIndex]) && $formattedData[$rowIndex-2][$characterIndex] === 'A'
                        && !empty($formattedData[$rowIndex-3][$characterIndex]) && $formattedData[$rowIndex-3][$characterIndex] === 'S') {
                    $wordCount++;
                }
            }
        }

        return $wordCount;
    }

    private function countDiagonal(array $formattedData): int {
        $wordCount = 0;

        foreach ($formattedData as $rowIndex => $row) {
            foreach ($row as $characterIndex => $character) {
                /**
                 * . . . X . . .
                 * . . M . . . .
                 * . A . . . . .
                 * S . . . . . .
                 */
                if ($character === 'X' 
                        && !empty($formattedData[$rowIndex+1][$characterIndex-1]) && $formattedData[$rowIndex+1][$characterIndex-1] === 'M' 
                        && !empty($formattedData[$rowIndex+2][$characterIndex-2]) && $formattedData[$rowIndex+2][$characterIndex-2] === 'A'
                        && !empty($formattedData[$rowIndex+3][$characterIndex-3]) && $formattedData[$rowIndex+3][$characterIndex-3] === 'S') {
                    $wordCount++;
                }

                if ($character === 'X' 
                        && !empty($formattedData[$rowIndex+1][$characterIndex+1]) && $formattedData[$rowIndex+1][$characterIndex+1] === 'M' 
                        && !empty($formattedData[$rowIndex+2][$characterIndex+2]) && $formattedData[$rowIndex+2][$characterIndex+2] === 'A'
                        && !empty($formattedData[$rowIndex+3][$characterIndex+3]) && $formattedData[$rowIndex+3][$characterIndex+3] === 'S') {
                    $wordCount++;
                }

                if ($character === 'X' 
                        && !empty($formattedData[$rowIndex-1][$characterIndex+1]) && $formattedData[$rowIndex-1][$characterIndex+1] === 'M' 
                        && !empty($formattedData[$rowIndex-2][$characterIndex+2]) && $formattedData[$rowIndex-2][$characterIndex+2] === 'A'
                        && !empty($formattedData[$rowIndex-3][$characterIndex+3]) && $formattedData[$rowIndex-3][$characterIndex+3] === 'S') {
                    $wordCount++;
                }

                if ($character === 'X' 
                        && !empty($formattedData[$rowIndex-1][$characterIndex-1]) && $formattedData[$rowIndex-1][$characterIndex-1] === 'M' 
                        && !empty($formattedData[$rowIndex-2][$characterIndex-2]) && $formattedData[$rowIndex-2][$characterIndex-2] === 'A'
                        && !empty($formattedData[$rowIndex-3][$characterIndex-3]) && $formattedData[$rowIndex-3][$characterIndex-3] === 'S') {
                    $wordCount++;
                }
            }
        }

        return $wordCount;
    }

    private function countX_MAS(array $formattedData): int {
        $wordCount = 0;

        foreach ($formattedData as $rowIndex => $row) {
            foreach ($row as $characterIndex => $character) {
                $characterTopLeft = !empty($formattedData[$rowIndex-1][$characterIndex-1]) ? $formattedData[$rowIndex-1][$characterIndex-1] : '';
                $characterTopRight = !empty($formattedData[$rowIndex-1][$characterIndex+1]) ? $formattedData[$rowIndex-1][$characterIndex+1] : '';
                $characterBottomRight = !empty($formattedData[$rowIndex+1][$characterIndex+1]) ? $formattedData[$rowIndex+1][$characterIndex+1] : '';
                $characterBottomLeft = !empty($formattedData[$rowIndex+1][$characterIndex-1]) ? $formattedData[$rowIndex+1][$characterIndex-1] : '';

                if ($character === 'A' && (
                        ($characterTopLeft === 'M' && $characterTopRight === 'S' && $characterBottomRight === 'S' && $characterBottomLeft === 'M') ||
                        ($characterTopLeft === 'S' && $characterTopRight === 'M' && $characterBottomRight === 'M' && $characterBottomLeft === 'S') ||
                        ($characterTopLeft === 'S' && $characterTopRight === 'S' && $characterBottomRight === 'M' && $characterBottomLeft === 'M') ||
                        ($characterTopLeft === 'M' && $characterTopRight === 'M' && $characterBottomRight === 'S' && $characterBottomLeft === 'S'))) {
                    $wordCount++;
                }
            }
        }

        return $wordCount;
    }

}

count($argv) <= 1 ? $filename = 'example_data.txt' : $filename = $argv[1];

$Day4 = new Day4();
$Day4->main($filename);