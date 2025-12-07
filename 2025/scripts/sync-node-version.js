import chalk from 'chalk'
import fs from 'fs'
import path from 'path'

const toolVersionsPath = path.join(process.cwd(), '.tool-versions')
const nvmrcPath = path.join(process.cwd(), '.nvmrc')
const packageJsonPath = path.join(process.cwd(), 'package.json')

if (!fs.existsSync(toolVersionsPath)) {
    console.error(chalk.red('.tool-versions not found!'))
    process.exit(1)
}

const toolVersionsContent = fs.readFileSync(toolVersionsPath, 'utf8')

const match = toolVersionsContent.match(/^nodejs\s+([^\s]+)/m)

if (!match) {
    console.error(chalk.red('No Node version found in .tool-versions!'))
    process.exit(1)
}

const nodeVersion = match[1]
console.log(chalk.green('Found Node version:', nodeVersion))

fs.writeFileSync(nvmrcPath, nodeVersion + '\n')
console.log(chalk.green('.nvmrc updated.'))

if (!fs.existsSync(packageJsonPath)) {
    console.error(chalk.red('package.json not found!'))
    process.exit(1)
}

const packageJsonRaw = fs.readFileSync(packageJsonPath, 'utf8')
const packageJson = JSON.parse(packageJsonRaw)

packageJson.engines = {
    node: `^${nodeVersion}`,
}

fs.writeFileSync(packageJsonPath, JSON.stringify(packageJson, null, 2) + '\n')
console.log(chalk.green('package.json engines updated with ^ prefix.'))
