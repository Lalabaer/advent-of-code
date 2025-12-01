import { Command } from 'commander'
import { access } from 'fs/promises'

import chalk from 'chalk'
import path from 'path'

const isCompiled = import.meta.url.includes('/dist/')
const baseDir = isCompiled ? 'dist' : '.'
const fileExtension = isCompiled ? '.js' : '.ts'

const executeDay = async (day: string, part?: string, filename?: string): Promise<void> => {
    const pathToDaySolution = path.resolve(process.cwd(), baseDir, `day-${day}`, `index${fileExtension}`)

    try {
        await access(pathToDaySolution)
    } catch {
        console.error(chalk.red(`No solution found for day ${day}`))
        process.exit(1)
    }

    try {
        const daySolution = await import(pathToDaySolution)

        if (part) {
            const partName = `part${part}` as keyof typeof daySolution

            if (daySolution[partName]) {
                console.log(chalk.green(`Running Part ${part}:`))
                await daySolution[partName](filename)
            } else {
                console.error(chalk.red(`Part ${part} not found for day ${day}`))
                process.exit(1)
            }
        }

        if (daySolution.part1) {
            console.log(chalk.green('Running Part 1:'))
            await daySolution.part1(filename)
        }

        if (daySolution.part2) {
            console.log(chalk.green('Running Part 2:'))
            await daySolution.part2(filename)
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
    .action(async (day: string | undefined, options: { part?: string; filename?: string }) => {
        if (!day) {
            console.info('No day specified! Please provide a day number (1-25).')
            return
        }

        await executeDay(day, options.part, options.filename)
    })

program.parse()
