import { Command } from 'commander'
import { access } from 'fs/promises'
import type { options } from './src/types/types.js'
import { getDayInput } from './src/utils/utils.js'

import chalk from 'chalk'
import path from 'path'

const isCompiled = import.meta.url.includes('/dist/')
const baseDir = isCompiled ? 'dist' : '.'
const fileExtension = isCompiled ? '.js' : '.ts'

const executeDay = async (day: string, options: options): Promise<void> => {
    const pathToDaySolution = path.resolve(process.cwd(), baseDir, `src/day-${day}`, `index${fileExtension}`)
    const part = options.part
    const filename = options.filename

    try {
        await access(pathToDaySolution)
    } catch {
        console.error(chalk.red(`No solution found for day ${day}`))
        process.exit(1)
    }

    let input: string

    try {
        input = await getDayInput(day, filename)
    } catch (error) {
        console.error(chalk.red(`Unable to read input file for day ${day}`))
        console.error(`Error is: ${error}`)
        process.exit(1)
    }

    try {
        const daySolution = await import(pathToDaySolution)

        const runPart = async (partName: string, partNumber: string) => {
            const partFunction = daySolution[partName as keyof typeof daySolution]

            if (partFunction) {
                console.log(chalk.green(`Running Part ${partNumber}:`))

                if (options.time) {
                    console.time('executionTime')
                }

                await partFunction(input, options)

                if (options.time) {
                    console.timeEnd('executionTime')
                }
            }
        }

        if (part) {
            const partName = `part${part}` as keyof typeof daySolution

            if (daySolution[partName]) {
                await runPart(`part${part}`, part)
            } else {
                console.error(chalk.red(`Part ${part} not found for day ${day}`))
                process.exit(1)
            }
        } else {
            await runPart('part1', '1')
            await runPart('part2', '2')
        }
    } catch (error) {
        console.error(`Error importing solution for day ${day}`)
        console.error(`Error is:  ${error}`)
        process.exit(1)
    }
}

const program = new Command()

program.name('aoc2025').description('🎄🎄🎄 Advent Of Code 2025 🎄🎄🎄 Merry Christmas 🎄🎄🎄').version('1.0.0')

program
    .argument('[day]', 'Day number to run (1-25)')
    .option('-p, --part <number>', 'Run specific part (1 or 2)')
    .option('-f --filename <string>', 'Allows specifying the input file name')
    .option('-d --debug <boolean>', 'Sets debug flag and will print some output in tasks')
    .option('-t, --time', 'Displays execution time for each part')
    .action(async (day: string | undefined, options: options) => {
        if (!day) {
            console.info('No day specified! Please provide a day number (1-25).')
            return
        }

        await executeDay(day, options)
    })

program.parse()
