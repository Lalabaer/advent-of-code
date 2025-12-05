import { mkdir, writeFile } from 'fs/promises'
import { readFile } from 'fs/promises'
import { fileURLToPath } from 'url'

import chalk from 'chalk'
import path from 'path'

const __filename = fileURLToPath(import.meta.url)
const __dirname = path.dirname(__filename)

const createDay = async (dayNumber) => {
    const day = parseInt(dayNumber, 10)

    if (isNaN(day) || day < 1 || day > 25) {
        console.error('Please provide a valid day number between 1 and 25')
        process.exit(1)
    }

    const dayDir = path.resolve(process.cwd(), 'src', `day-${day}`)

    try {
        // create directory
        await mkdir(dayDir, { recursive: true })
        console.log(`✓ Created directory: ${dayDir}`)

        // read template
        const templatePath = path.resolve(__dirname, '..', 'templates', 'day-template.ts')
        const template = await readFile(templatePath, 'utf8')

        // rreate index.ts
        const indexPath = path.resolve(dayDir, 'index.ts')
        await writeFile(indexPath, template, 'utf8')
        console.log(chalk.green(`✓ Created file: ${indexPath}`))

        // create example_data.txt
        const exampleDataPath = path.resolve(dayDir, 'example_data.txt')
        await writeFile(exampleDataPath, '', 'utf8')
        console.log(`✓ Created file: ${exampleDataPath}`)

        // create input_data.txt
        const inputDataPath = path.resolve(dayDir, 'input_data.txt')
        await writeFile(inputDataPath, '', 'utf8')
        console.log(`✓ Created file: ${inputDataPath}`)

        console.log(chalk.green(`\n✓ Successfully created files for day ${day}!`))
        console.log(chalk.green(`  You can now run: yarn dev ${day}`))
    } catch (error) {
        if (error.code === 'EEXIST') {
            console.error(
                chalk.red(`Directory ${dayDir} already exists. Please remove it first if you want to recreate it.`),
            )
        } else {
            console.error(chalk.red(`Error creating day ${day}:`), error.message)
        }

        process.exit(1)
    }
}

const dayNumber = process.argv[2]

if (!dayNumber) {
    console.error(chalk.yellow('Please provide a day number (1-25)'))
    console.error(chalk.yellow('Usage: yarn create-day <day-number>'))
    process.exit(1)
}

createDay(dayNumber)
