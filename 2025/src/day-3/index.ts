import { getDayInput, splitBy, sumNumbers } from '../utils/utils.js'
import type { options } from '../types/types.js'

import chalk from 'chalk'

export const part1 = async (options: options) => {
    const filename = options.filename

    console.time('executionTime')
    const input = await getDayInput(3, filename)
    const banks = await splitBy(input)

    const jolatges = findLargestPossibleJoltagesByUsingTwoBatteries(banks)
    const answer = sumNumbers(jolatges)

    console.timeEnd('executionTime')
    console.info(chalk.green(`The solution for part 1 is ${answer}`))
}

export const part2 = async (options: options) => {
    const filename = options.filename

    console.time('executionTime')
    const input = await getDayInput(3, filename)
    const banks = await splitBy(input)
    const targetNumberOfUsedBatteries = 12

    const jolatges = findLargestPossibleJoltagesByUsingTwelveBatteries(banks, targetNumberOfUsedBatteries)
    const answer = sumNumbers(jolatges)

    console.timeEnd('executionTime')
    console.info(chalk.green(`The solution for part 2 is ${answer}`))
}

const findLargestPossibleJoltagesByUsingTwoBatteries = (banks: string[]): number[] => {
    const joltages = []

    for (let bank of banks) {
        const pair: number[] = []
        let firstIndex = -1

        for (let i = 9; i > 0; i--) {
            if (pair[0] && pair[1]) break

            let position = bank.indexOf(`${i}`)

            while (position !== -1) {
                if (position + 1 === bank.length) {
                    pair[1] = i
                    break
                }

                if (!pair[0]) {
                    pair[0] = i
                    firstIndex = position
                }

                if (!pair[1] && position > firstIndex) pair[1] = i

                position = bank.indexOf(`${i}`, position + 1)

                if (pair[0] && pair[1]) break
            }
        }

        joltages.push(parseInt(pair.join('')))
    }

    return joltages
}

const findLargestPossibleJoltagesByUsingTwelveBatteries = (banks: string[], targetLength: number): number[] => {
    const joltages: number[] = []

    for (const bank of banks) {
        const numbers: string[] = []
        let toRemove = bank.length - targetLength

        for (let i = 0; i < bank.length; i++) {
            const currentNumber = bank[i]

            while (toRemove > 0 && numbers.length > 0 && numbers[numbers.length - 1] < currentNumber) {
                numbers.pop()
                toRemove--
            }

            numbers.push(currentNumber)
        }

        // NEED TO SHORTEN TO EXACT LENGTH AT THE END!!
        if (numbers.length > targetLength) {
            numbers.splice(targetLength)
        }

        joltages.push(parseInt(numbers.join('')))
    }

    return joltages
}
