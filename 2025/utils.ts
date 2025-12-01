import { readFile } from 'fs/promises'
import path from 'path'

export const getDayInput = async (dayNumber: number | string, fileName = 'example_data.txt'): Promise<string> => {
    const filePath = path.resolve(process.cwd(), `day-${dayNumber}`, fileName)

    try {
        const fileContents = await readFile(filePath, 'utf8')
        return fileContents
    } catch (error) {
        const message = error instanceof Error ? error.message : 'Unknown error'
        throw new Error(`Unable to read input file at ${filePath}: ${message}`)
    }
}

export const readLines = async (input: string): Promise<string[]> => {
    return input.trim().split('\n')
}
