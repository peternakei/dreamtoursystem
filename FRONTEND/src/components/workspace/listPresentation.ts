import type {Column} from '@/components/datatable/types'

type Row = Record<string, any>
type Field = {key: string; label: string; paths?: string[]; value?: (row: Row) => unknown; format?: (value: any) => string}
const field = (key: string, label: string, ...paths: string[]): Field => ({key, label, paths: paths.length ? paths : [key]})
export function text(value: any): string {
  if (value === null || value === undefined || value === '') return '—'
  if (typeof value === 'boolean') return value ? 'Yes' : 'No'
  if (Array.isArray(value)) return value.map(text).filter(v => v !== '—').join(', ') || '—'
  if (typeof value === 'object') return text(value.name ?? value.title ?? value.username ?? value.short_name)
  return String(value)
}
export const enabled = (value: unknown) => value === true || value === 1 || value === '1'
export function dateText(value: unknown): string {
  if (!value) return '—'
  const date = String(value).match(/^(\d{4})-(\d{2})-(\d{2})/)
  return date ? `${date[3]}/${date[2]}/${date[1]}` : String(value)
}
const name = field('name', 'Name', 'name', 'title')
const description = field('description', 'Description')
const created = {...field('created_at', 'Created'), format: dateText}
const active: Field = {...field('is_active', 'State'), value: r => r.is_active == null ? null : enabled(r.is_active) ? 'Active' : 'Inactive'}
const approval: Field = {key: '_approval', label: 'Approval', value: r => enabled(r.is_approved) ? 'Approved' : 'Pending'}
const customer = field('_customer', 'Customer', 'tourist.name', 'contact_name', 'name')
const currency = field('_currency', 'Currency', 'currency.short_name', 'current_version.currency.short_name')
const status = field('_status', 'Status', 'status.name', 'status', 'booking_status.name', 'invoice_status.name')
const amount = (key: string, label: string): Field => ({...field(key, label), format: v => v == null || v === '' ? '—' : Number.isFinite(Number(v)) ? Number(v).toLocaleString('en-GB', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : text(v)})
const date = (key: string, label: string): Field => ({...field(key, label), format: dateText})
const layouts: Record<string, Field[]> = {
  inquiries: [field('inquiry_code', 'Reference'), customer, field('_email', 'Email', 'tourist.email'), field('_phone', 'Phone', 'tourist.phone'),
    {key: '_service', label: 'Service', value: r => r.service_details?.service_type ?? r.trip_type?.name ?? 'Travel'},
    field('tour_title', 'Requested trip'), date('from_date', 'From'), date('to_date', 'To'), field('guests', 'Guests'), approval,
    field('_assigned', 'Assigned to', 'assigned_to.username'), field('quotations_count', 'Quotations'), created],
  quotations: [field('quotation_number', 'Reference'), customer, field('_title', 'Title', 'current_version.title', 'created_from_trip.name'), date('quotation_date', 'Quote date'), currency, amount('total_amount', 'Total'), status, created],
  bookings: [field('booking_number', 'Reference'), customer, field('_trip', 'Trip', 'trip.name', 'trip.title'), date('booking_date', 'Travel date'), field('guest_count', 'Guests'), currency, amount('total_amount', 'Total'), status, created],
  invoices: [field('invoice_number', 'Invoice'), customer, field('_booking', 'Booking', 'booking.booking_number'), date('invoice_date', 'Invoice date'), currency, amount('total_amount', 'Total'), status],
  receipts: [field('reference_number', 'Reference'), field('_invoice', 'Invoice', 'invoice.invoice_number'), date('receipt_date', 'Receipt date'), currency, amount('amount', 'Amount'), field('_payment', 'Payment method', 'payment_mode.name'), created],
  refunds: [field('refund_number', 'Reference'), field('_invoice', 'Invoice', 'invoice.invoice_number'), currency, amount('amount', 'Amount'), status, created],
  tourists: [name, field('email', 'Email'), field('phone', 'Phone'), field('_country', 'Country', 'country.name'), active, created],
  vehicles: [name, field('capacity', 'Capacity'), description, active, created],
  accommodations: [name, field('_stay', 'Stay type', 'stay_type.name'), field('_destination', 'Destination', 'primary_destination.name'), field('location_text', 'Location'), active],
  destinations: [name, description, active, created],
  activities: [name, description, active, created],
  addons: [name, description, active, created],
  budgets: [field('_trip', 'Trip', 'trip.name'), field('_season', 'Season', 'season.name'), field('_class', 'Class', 'service_class.name'), currency, amount('price', 'Price'), field('quantity', 'Quantity'), active],
  bank_details: [field('_bank', 'Bank', 'bank.name'), field('account_name', 'Account name'), field('account_number', 'Account number'), currency, active],
  banks: [name, active, created],
  categories: [name, description, active, created],
  countries: [name, field('code', 'Code'), active],
  regions: [name, field('_country', 'Country', 'country.name'), active],
  districts: [name, field('_region', 'Region', 'region.name'), active],
  locations: [name, active, created],
  exchange_rates: [currency, field('rate', 'Exchange rate'), active, created],
  faqs: [field('question', 'Question'), field('_category', 'Category', 'category.name', 'faq_category.name'), field('answer', 'Answer'), field('order', 'Order'), active],
  testimonials: [customer, field('comments', 'Comments'), approval, created],
  ratings: [customer, field('rating', 'Rating'), field('comment', 'Comment'), created],
  subscriptions: [field('email', 'Email'), active, created],
  users: [field('_name', 'Name', 'name', 'user_profile.name', 'username'), field('username', 'Username', 'login.username', 'username'), field('email', 'Email'), field('phone', 'Phone'), field('_roles', 'Roles', 'login.roles', 'roles'), active, created],
  roles: [name, field('guard_name', 'Guard'), active, created],
  permissions: [name, field('guard_name', 'Guard'), active, created],
  menus: [name, field('url', 'URL', 'url', 'route'), active, created],
  seasons: [name, created],
  system_configurations: [name, description, active, created],
  blogs: [field('title', 'Title', 'title', 'name'), field('slug', 'Slug'), active, created],
  pages: [name, field('title', 'Title'), description, created],
  trips: [field('trip_code', 'Reference'), name, field('_type', 'Trip type', 'trip_type.name'), field('duration_days', 'Days'), field('duration_nights', 'Nights'), date('from_date', 'From'), date('to_date', 'To'), field('_trip_status', 'Status', 'trip_status.name'), {key: '_publication', label: 'Publication', value: r => enabled(r.is_published) ? 'Published' : 'Unpublished'}, created],
}
function at(row: Row, path: string): unknown {
  return path.split('.').reduce<any>((value, part) => value?.[part], row)
}
/** Project human-readable values before filtering/sorting, while retaining IDs and raw dates. */
export function listPresentation(module: string, records: Row[]): {columns: Column[]; rows: Row[]} {
  const fields = layouts[module] ?? [name, description, active, created]
  return {
    columns: fields.map(f => ({key: f.key, label: f.label, sortable: true, formatter: f.format ?? text})),
    rows: records.map(row => {
      const result = {...row}
      for (const f of fields) {
        const value = f.value ? f.value(row) : f.paths?.map(path => at(row, path)).find(v => v !== null && v !== undefined && v !== '')
        result[f.key] = value != null && typeof value === 'object' ? text(value) : value ?? null
      }
      return result
    }),
  }
}
