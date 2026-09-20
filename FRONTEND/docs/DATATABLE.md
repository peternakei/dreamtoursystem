# Reusable list table

All module list pages use `src/components/datatable/DataTableReport.vue` through `EntityPage.vue`. Detail screens and record actions retain their existing behavior. The component adapts the supplied DataTableReport example to the Dream Travel and Tours theme; no loan-specific report fields are included.

## Show or hide dates on a list page

Pass a date field to enable the start/end date inputs:

```vue
<EntityPage module="bookings" date-field="created_at" />
```

Hide the inputs and disable date filtering explicitly:

```vue
<EntityPage module="bookings" date-field="created_at" :show-date-filter="false" />
```

Omitting `date-field` also hides them:

```vue
<EntityPage module="activities" />
```

Inquiries, quotations, bookings, invoices, receipts and refunds initially filter by `created_at`. Other list pages initially omit date filtering. To use a different date, pass its key from the API row, for example `date-field="booking_date"` if that field is returned. A date field does not need to be a displayed column.

## Use the table directly in a custom page

```vue
<script setup lang="ts">
import DataTableReport from '@/components/datatable/DataTableReport.vue'
import type { Column } from '@/components/datatable/types'

const columns: Column[] = [
  { key: 'name', label: 'Name', sortable: true },
  { key: 'amount', label: 'Amount', sortable: true, align: 'right',
    formatter: value => Number(value ?? 0).toLocaleString() },
  { key: 'status', label: 'Status' },
]
const rows = [{ id: 1, name: 'Safari booking', amount: 1200, status: 'Pending', created_at: '2026-09-20' }]
</script>

<template>
  <DataTableReport :columns="columns" :rows="rows" row-key="id"
    date-field="created_at" :show-date-filter="true" :default-page-size="25">
    <template #cell-status="{ value }"><strong>{{ value }}</strong></template>
    <template #footer="{ filteredRows }">
      <tr><td :colspan="columns.length + 1" class="p-4">Total: {{ filteredRows.length }} records</td></tr>
    </template>
  </DataTableReport>
</template>
```

The extra footer column accounts for row numbering (`numbered` defaults to true). `cell-<key>` slots receive `{ value, row }`; the footer receives `{ filteredRows, allRows }`. `formatter`, `row-class`, `search-keys`, `searchable`, `numbered`, `paginated`, `page-size-options`, `loading`, and `row-key` are supported. Without `row-key`, the table uses `uuid`, then `id`, then the row index.

Both range boundaries are inclusive. Date-only and SQL date strings use their local calendar day; timestamps with explicit offsets use the browser's local timezone. Rows with missing or invalid dates are excluded only when a date bound is selected. Inverted ranges show a validation message. Hiding date filters clears them, so hidden inputs never continue filtering rows.

Search, dates and sorting reset pagination. Refreshing a dataset clamps the current page if it no longer exists. Reset clears filters and sorting. Sorting also supports keyboard activation.

Filtering and pagination operate on the complete `rows` array passed into the component. `paginated=false` disables slicing; it does not turn this into a server-side table. Do not supply just one backend page and expect filtering to cover the full database. The `filters-change` event emits effective `{ startDate, endDate, search }`; `page-change` emits the page number.

Run `npm run test:datatable`, `npm run typecheck`, and `npm run build` from `FRONTEND`.
