import test from 'node:test'
import assert from 'node:assert/strict'
import { calendarDay, filterRows, sortRows } from '../src/components/datatable/tableUtils.ts'

const empty = { startDate: '', endDate: '', search: '' }
const rows = [
  { id: 1, name: 'Safari Alpha', amount: '100', created_at: '2026-09-18 00:00:00' },
  { id: 2, name: 'Safari Beta', amount: '20.50', created_at: '2026-09-18 23:59:59' },
  { id: 3, name: 'Zanzibar', amount: 3, created_at: '2026-09-19' },
  { id: 4, name: 'Unknown date', amount: null, created_at: null },
  { id: 5, name: 'Invalid date', amount: 0, created_at: 'not-a-date' },
]

test('includes the full start and end days for SQL and date-only values', () => {
  assert.deepEqual(filterRows(rows, { ...empty, startDate: '2026-09-18', endDate: '2026-09-18' }, 'created_at').map(r => r.id), [1, 2])
  assert.deepEqual(filterRows(rows, { ...empty, startDate: '2026-09-19' }, 'created_at').map(r => r.id), [3])
  assert.deepEqual(filterRows(rows, { ...empty, endDate: '2026-09-18' }, 'created_at').map(r => r.id), [1, 2])
})

test('date-only strings do not shift by timezone; timestamps use local calendar days', () => {
  assert.equal(calendarDay('2026-09-18'), '2026-09-18')
  const date = new Date('2026-09-18T23:30:00Z')
  const expected = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`
  assert.equal(calendarDay('2026-09-18T23:30:00Z'), expected)
  assert.equal(calendarDay('2026-02-30'), null)
  assert.equal(calendarDay('2026-02-30T12:00:00'), null)
  assert.equal(calendarDay('invalid'), null)
})

test('missing/invalid dates are included without a range but excluded with a range', () => {
  assert.equal(filterRows(rows, empty, 'created_at').length, 5)
  assert.equal(filterRows(rows, { ...empty, startDate: '2026-01-01' }, 'created_at').length, 3)
})

test('omitting the date field disables date filtering; inverted ranges have no matches', () => {
  const inverted = { ...empty, startDate: '2026-09-20', endDate: '2026-09-18' }
  assert.equal(filterRows(rows, inverted).length, 5)
  assert.equal(filterRows(rows, inverted, 'created_at').length, 0)
})

test('search combines with dates, trims whitespace and respects search keys', () => {
  assert.deepEqual(filterRows(rows, { ...empty, endDate: '2026-09-18', search: ' BETA ' }, 'created_at', ['name']).map(r => r.id), [2])
  assert.equal(filterRows(rows, { ...empty, search: '100' }, '', ['name']).length, 0)
  assert.equal(filterRows(rows, { ...empty, search: '100' }).length, 1)
})

test('numeric strings sort numerically, missing values stay last, and input is unchanged', () => {
  assert.deepEqual(sortRows(rows, 'amount', 'asc').map(r => r.id), [5, 3, 2, 1, 4])
  assert.deepEqual(sortRows(rows, 'amount', 'desc').map(r => r.id), [1, 2, 3, 5, 4])
  assert.deepEqual(rows.map(r => r.id), [1, 2, 3, 4, 5])
})
