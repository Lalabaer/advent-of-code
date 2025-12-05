import { splitBy } from '../utils/utils.js'
import type { options } from '../types/types.js'

import chalk from 'chalk'

type ranges = {
    start: number
    end: number
}

export const part1 = async (input: string, _options: options) => {
    const { rangesString, idsString } = await parseInput(input)
    const ranges = sortRanges(await parseRanges(rangesString))
    const ids = (await splitBy(idsString)).map(Number)
    const freshIdsCount = getNumberOfFreshIds(ranges, ids)

    console.info(chalk.green(`The solution for part 1 is ${freshIdsCount}`))
}

export const part2 = async (input: string, _options: options) => {
    const { rangesString } = await parseInput(input)
    const ranges = sortRanges(await parseRanges(rangesString))
    const mergedRanges = mergeRanges(ranges)
    const allAvailableFreshIds = getNumberOfAllAvailableFreshIds(mergedRanges)

    console.info(chalk.green(`The solution for part 2 is ${allAvailableFreshIds}`))
}

const parseInput = async (input: string) => {
    const [rangesString, idsString] = await splitBy(input, '\n\n')
    return { rangesString, idsString }
}

const parseRanges = async (rangesString: string): Promise<ranges[]> => {
    const ranges: ranges[] = []

    for (const range of await splitBy(rangesString)) {
        const [start, end] = (await splitBy(range, '-')).map(Number)
        ranges.push({ start, end })
    }

    return ranges
}

const sortRanges = (ranges: ranges[]): ranges[] => {
    return ranges.sort((a, b) => a.start - b.start)
}

const isInRange = (ranges: ranges[], id: number): boolean => {
    for (const range of ranges) {
        if (id >= range.start && id <= range.end) {
            return true
        }
    }

    return false
}

const getNumberOfFreshIds = (ranges: ranges[], ids: number[]): number => {
    let countFreeId: number = 0

    for (const id of ids) {
        if (isInRange(ranges, id)) {
            countFreeId++
            continue
        }
    }

    return countFreeId
}

const getNumbersBetween = (start: number, end: number): number => {
    if (end < start) return 0

    return start <= end ? end - start + 1 : 0
}

const mergeRanges = (ranges: ranges[]): ranges[] => {
    if (!ranges.length) return []

    const merged: ranges[] = [ranges[0]]

    for (let i = 1; i < ranges.length; i++) {
        const last = merged[merged.length - 1]
        const current = ranges[i]

        if (current.start <= last.end) {
            last.end = Math.max(last.end, current.end)
        } else {
            merged.push(current)
        }
    }

    return merged
}

const getNumberOfAllAvailableFreshIds = (ranges: ranges[]): number => {
    let availableFreshIds = 0

    for (const range of ranges) {
        availableFreshIds += getNumbersBetween(range.start, range.end)
    }

    return availableFreshIds
}
