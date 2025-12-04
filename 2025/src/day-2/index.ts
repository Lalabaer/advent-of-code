import { getDayInput, splitBy, sumNumbers } from '../utils/utils.js'
import type { options } from '../types/types.js'

import chalk from 'chalk'

export const part1 = async (options: options) => {
    const filename = options.filename

    console.time('executionTime')
    const input = await getDayInput(2, filename)
    const ranges = await splitBy(input, ',')
    const invalidIds = findMirroredInvalidIds(ranges)
    const sumOfInvalidIds = sumNumbers(invalidIds)

    console.timeEnd('executionTime')
    console.info(chalk.green(`The solution for part 1 is ${sumOfInvalidIds}`))
}

export const part2 = async (options: options) => {
    const filename = options.filename

    console.time('executionTime')
    const input = await getDayInput(2, filename)
    const ranges = await splitBy(input, ',')
    const invalidIds = findSequenceInvalidIds(ranges)
    const sumOfInvalidIds = sumNumbers(invalidIds)

    console.timeEnd('executionTime')
    console.info(chalk.green(`The solution for part 2 is ${sumOfInvalidIds}`))
}

/**
 * @param numberAsString
 *
 * First created solution using string comparison.
 * When I read the task I had directly 2 things in mind:
 *
 * 1. quick and direty doing some loop and compare
 * 2. there must be a regex for it
 *
 * Function is replaced by isMirroredUsingRegex() but you could change and would get the same result
 */
const isMirrored = (numberAsString: string, length: number): boolean => {
    const middle = length / 2
    const firstHalf = numberAsString.slice(0, middle)
    const secondHalf = numberAsString.slice(middle)

    return firstHalf === secondHalf
}

const isMirroredUsingRegex = (numberAsString: string): boolean => {
    return /^(.+?)\1$/.test(numberAsString)
}

const isSequence = (numberAsString: string): boolean => {
    return /^(.+?)\1+$/.test(numberAsString)
}

const findMirroredInvalidIds = (ranges: string[]): number[] => {
    const invalidIds = []

    for (const range of ranges) {
        const startAndStop = range.split('-')
        const start = parseInt(startAndStop[0])
        const stop = parseInt(startAndStop[1])

        for (let i = start; i <= stop; i++) {
            const numberAsString = i.toString()
            const length = numberAsString.length

            if (length % 2 === 1) continue
            // First solution
            // if (isMirrored(numberAsString, length)) invalidIds.push(i)
            if (isMirroredUsingRegex(numberAsString)) invalidIds.push(i)
        }
    }

    return invalidIds
}

const findSequenceInvalidIds = (ranges: string[]): number[] => {
    const invalidIds = []

    for (const range of ranges) {
        const startAndStop = range.split('-')
        const start = parseInt(startAndStop[0])
        const stop = parseInt(startAndStop[1])

        for (let i = start; i <= stop; i++) {
            const numberAsString = i.toString()
            const length = numberAsString.length

            // some sequence of digits repeated at least twice -> 11, 22, 99 (valid), 1 invalid
            if (length < 2) continue
            if (isSequence(numberAsString)) invalidIds.push(i)
        }
    }

    return invalidIds
}
