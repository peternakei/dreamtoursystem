import type { TableFilters, TableRow } from './types'

/** Calendar dates stay on their written day; timestamps use the browser's local day. */
export function calendarDay(value: unknown): string | null {
  if (typeof value !== 'string' || !value.trim()) return null
  const text = value.trim()
  if (/^\d{4}-\d{2}-\d{2}$/.test(text)) {
    const [year, month, day] = text.split('-').map(Number)
    const date = new Date(year, month - 1, day)
    return date.getFullYear() === year && date.getMonth() === month - 1 && date.getDate() === day ? text : null
  }
  // Normalize Laravel SQL timestamps for browsers that reject a space separator.
  if (!/^\d{4}-\d{2}-\d{2}[T ]\d{2}:\d{2}/.test(text)) return null
  if (!calendarDay(text.slice(0, 10))) return null
  const date = new Date(text.replace(' ', 'T'))
  if (!Number.isFinite(date.getTime())) return null
  return `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`
}

export function displayValue(value: unknown): string {
  if (value === null || value === undefined || value === '') return '—'
  if (typeof value === 'boolean') return value ? 'Yes' : 'No'
  if (typeof value === 'object') return JSON.stringify(value)
  return String(value)
}

export function filterRows<T extends TableRow>(rows: T[], filters: TableFilters, dateField = '', searchKeys?: string[]): T[] {
  const { startDate, endDate } = filters
  if (dateField && startDate && endDate && startDate > endDate) return []
  const query = filters.search.trim().toLocaleLowerCase()
  return rows.filter(row => {
    if (dateField && (startDate || endDate)) {
      const day = calendarDay(row[dateField])
      if (!day || (startDate && day < startDate) || (endDate && day > endDate)) return false
    }
    const values = searchKeys?.length ? searchKeys.map(key => row[key]) : Object.values(row)
    return !query || values.some(value => value != null && displayValue(value).toLocaleLowerCase().includes(query))
  })
}

const collator = new Intl.Collator(undefined, { numeric: true, sensitivity: 'base' })
export function sortRows<T extends TableRow>(rows: T[], key: string | null, direction: 'asc' | 'desc'): T[] {
  if (!key) return rows
  const numeric = (value: unknown) => typeof value === 'number' || (typeof value === 'string' && value.trim() !== '' && Number.isFinite(Number(value)))
  return [...rows].sort((a, b) => {
    const left = a[key], right = b[key]
    // Missing values stay last in either direction.
    if (left == null || right == null) return left == null ? (right == null ? 0 : 1) : -1
    const comparison = numeric(left) && numeric(right)
      ? Number(left) - Number(right)
      : collator.compare(displayValue(left), displayValue(right))
    return direction === 'asc' ? comparison : -comparison
  })
}
