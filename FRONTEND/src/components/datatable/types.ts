export type TableRow = Record<string, unknown>

export interface Column {
  key: string
  label: string
  sortable?: boolean
  align?: 'left' | 'right' | 'center'
  formatter?: (value: any, row: any) => unknown
}

export interface TableFilters {
  startDate: string
  endDate: string
  search: string
}
