import { getDayInput, inputToGrid } from '../utils/utils.js'
import type { options } from '../types/types.js'

import chalk from 'chalk'

export const part1 = async (options: options) => {
    const filename = options.filename
    const debug = options.debug

    console.time('executionTime')
    const input = await getDayInput(4, filename)
    const grid = inputToGrid(input)
    const accessibleRolls = getAccessibleRolls(grid, false, debug)

    console.timeEnd('executionTime')
    console.info(chalk.green(`The solution for part 1 is ${accessibleRolls}`))
}

export const part2 = async (options: options) => {
    const filename = options.filename
    const debug = options.debug

    console.time('executionTime')
    const input = await getDayInput(4, filename)
    const grid = await inputToGrid(input)
    const accessibleRolls = getAccessibleRolls(grid, true, debug)

    console.timeEnd('executionTime')
    console.info(chalk.green(`The solution for part 2 is ${accessibleRolls}`))
}

const getAccessibleRolls = (grid: string[][], removeUntilDone?: boolean, debug?: boolean): number => {
    let accessibleRolls: number = 0
    let removedThisRound: boolean = false

    for (let row = 0; row < grid.length; row++) {
        for (let column = 0; column < grid[0].length; column++) {
            if (grid[row][column] !== '@') continue

            const pos = { row, column }
            const neighbors = checkHorizontal(pos, grid) + checkVertical(pos, grid) + checkDiagonal(pos, grid)

            if (neighbors < 4) {
                if (removeUntilDone) grid[row][column] = 'x'

                accessibleRolls++
                removedThisRound = true
            }
        }
    }

    if (removedThisRound && removeUntilDone) {
        accessibleRolls += getAccessibleRolls(grid, true)
    }

    return accessibleRolls
}

interface position {
    row: number
    column: number
}

// reused concept with separate functions for checking from day-4 2024
// not exactly the same but worked
const checkHorizontal = ({ row, column }: position, grid: string[][]): number => {
    let count: number = 0

    // left
    if (column - 1 >= 0 && grid[row][column - 1] === '@') {
        count++
    }

    // right
    if (column + 1 < grid[row].length && grid[row][column + 1] === '@') {
        count++
    }

    return count
}

const checkVertical = ({ row, column }: position, grid: string[][]): number => {
    let count: number = 0

    // up
    if (row - 1 >= 0 && grid[row - 1][column] === '@') {
        count++
    }

    // down
    if (row + 1 < grid.length && grid[row + 1][column] === '@') {
        count++
    }

    return count
}

const checkDiagonal = ({ row, column }: position, grid: string[][]): number => {
    let count: number = 0

    const directions = [
        [-1, -1], // up left
        [-1, +1], // up right
        [+1, -1], // down left
        [+1, +1], // down right
    ]

    for (const [directionRow, directionColumn] of directions) {
        const nextRow = row + directionRow
        const nextColumn = column + directionColumn

        if (
            nextRow >= 0 &&
            nextRow < grid.length &&
            nextColumn >= 0 &&
            nextColumn < grid[0].length &&
            grid[nextRow][nextColumn] === '@'
        ) {
            count++
        }
    }

    return count
}
