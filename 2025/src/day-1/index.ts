import { splitBy } from '../utils/utils.js'
import chalk from 'chalk'
import type { options } from '../types/types.js'

export const part1 = async (input: string, options: options) => {
    const lines = await splitBy(input)

    const answer = countZeroPositions(lines)

    console.info(chalk.green(`The solution for part 1 is ${answer}`))
}

export const part2 = async (input: string, options: options) => {
    const lines = await splitBy(input)

    const answer = countZeroPositions(lines, true)

    console.info(chalk.green(`The solution for part 2 is ${answer}`))
}

const getDirectionOperation = (command: string): string => {
    const direction = command.charAt(0)

    if (direction === 'L') return '-'
    if (direction === 'R') return '+'

    throw Error('Unknown Operation')
}

const getClicks = (command: string): number => {
    return parseInt(command.slice(1))
}

const getNewPosition = (operation: string, position: number, clicks: number, max: number): number => {
    let newPosition = 0

    switch (operation) {
        case '+':
            newPosition = position + clicks
            break
        case '-':
            newPosition = position - clicks
            break
    }

    newPosition = newPosition % max
    if (newPosition < 0) newPosition += max

    return newPosition
}

const getNumberOfRotations = (position: number, clicks: number, max: number, operation: string): number => {
    switch (operation) {
        case '+':
            return Math.floor((position + clicks) / max)
        case '-':
            if (position === 0) {
                return Math.floor(clicks / max)
            }

            return clicks >= position ? Math.floor((clicks - position) / max + 1) : 0
    }

    return 0
}

const countZeroPositions = (lines: string[], includeRotations?: boolean): number => {
    const max = 100
    let position = 50
    let found = 0
    let rotations = 0

    for (const line of lines) {
        const operation = getDirectionOperation(line)
        const clicks = getClicks(line)

        if (includeRotations) {
            rotations += getNumberOfRotations(position, clicks, max, operation)
        }

        position = getNewPosition(operation, position, clicks, max)

        if (position === 0) found++
    }

    if (includeRotations) {
        return rotations
    }

    return found
}
