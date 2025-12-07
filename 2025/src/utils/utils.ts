import { readFile } from 'fs/promises'
import path from 'path'

export const getDayInput = async (dayNumber: number | string, fileName = 'example_data.txt'): Promise<string> => {
    const filePath = path.resolve(process.cwd(), `src/day-${dayNumber}`, fileName)

    try {
        const fileContents = await readFile(filePath, 'utf8')
        return fileContents
    } catch (error) {
        const message = error instanceof Error ? error.message : 'Unknown error'
        throw new Error(`Unable to read input file at ${filePath}: ${message}`)
    }
}

export const splitBy = (input: string, splitBy?: string | RegExp): string[] => {
    splitBy = splitBy ? splitBy : '\n'
    return input.trim().split(splitBy)
}

export const sumNumbers = (numbers: number[]): number => {
    if (!numbers || numbers.length === 0) return 0

    let sum = 0
    for (let i = 0; i < numbers.length; i++) {
        sum += numbers[i]
    }

    return sum
}

export const inputToGrid = (input: string): string[][] => {
    return input
        .trim()
        .split('\n')
        .map((line) => line.split(''))
}
