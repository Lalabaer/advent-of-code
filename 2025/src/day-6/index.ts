import { splitBy, sumNumbers } from '../utils/utils.js'
import type { options } from '../types/types.js'

import chalk from 'chalk'

export const part1 = async (input: string, _options: options) => {
    const lines: string[][] = splitBy(input).map((line) => splitBy(line.trim(), /\s+/))
    const columnResults: number[] = calculateResultsPerColumn(lines)
    const answer = sumNumbers(columnResults)

    console.info(chalk.green(`The solution for part 1 is ${answer}`))
}

export const part2 = async (input: string, _options: options) => {
    const lines: string[] = splitBy(input)
    const operators: string[] = lines.splice(lines.length - 1)
    const problems: number[][] = parseProblemsRightToLeft(lines)

    const results: number[] = calculateProblemResults(operators, problems)
    const answer = sumNumbers(results)

    console.info(chalk.green(`The solution for part 2 is ${answer}`))
}

const operate = (firstNumber: number, secondNumber: number, operator: string): number => {
    switch (operator) {
        case '*':
            return firstNumber * secondNumber
        case '+':
            return firstNumber + secondNumber
        default:
            throw new Error(`Unsupported operator: ${operator}`)
    }
}

const calculateResultsPerColumn = (lines: string[][]): number[] => {
    const problemResults: number[] = []
    const operators = lines[lines.length - 1]
    const rowsCount = lines.length - 1
    const columnsCount = lines[0].length

    for (let columnNumber = 0; columnNumber < columnsCount; columnNumber++) {
        const operator = operators[columnNumber]
        let result: number = parseInt(lines[0][columnNumber])

        for (let rowIndex = 1; rowIndex < rowsCount; rowIndex++) {
            const nextNumber = parseInt(lines[rowIndex][columnNumber])
            result = operate(result, nextNumber, operator)
        }

        problemResults.push(result)
    }

    return problemResults
}

const parseProblemsRightToLeft = (lines: string[]): number[][] => {
    const problems: number[][] = []
    const rowsCount = lines.length - 1
    const columnsCount = lines[0].length

    let rightToLeftColumnNumbers: number[] = []

    for (let columnNumber = columnsCount - 1; columnNumber >= 0; columnNumber--) {
        let numberString: string = ''

        for (let rowNumber = 0; rowNumber <= rowsCount; rowNumber++) {
            numberString += lines[rowNumber][columnNumber]
        }

        const currentNumber: number = parseInt(numberString)

        if (isNaN(currentNumber)) {
            problems.push(rightToLeftColumnNumbers)
            rightToLeftColumnNumbers = []
            continue
        }

        rightToLeftColumnNumbers.push(currentNumber)

        if (columnNumber === 0) problems.push(rightToLeftColumnNumbers)
    }

    return problems
}

const calculateProblemResults = (operators: string[], problems: number[][]): number[] => {
    const results: number[] = []

    const reverseOperators = splitBy(operators[0], /\s+/).reverse()
    const operatorsLength = reverseOperators.length

    for (let i = 0; i < operatorsLength; i++) {
        const problemsLength = problems[i].length
        let result: number = problems[i][0]

        for (let j = 1; j < problemsLength; j++) {
            result = operate(result, problems[i][j], reverseOperators[i])
        }

        results.push(result)
    }

    return results
}
