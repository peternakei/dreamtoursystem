import test from 'node:test'
import assert from 'node:assert/strict'
import {listPresentation} from '../src/components/workspace/listPresentation.ts'
import {filterRows, sortRows} from '../src/components/datatable/tableUtils.ts'

test('inquiry lists search and sort displayed customers and retain travel dates and IDs', () => {
 const source=[{uuid:'first',inquiry_code:'INQ-1',tourist:{name:'Zara',email:'zara@example.test',phone:'25501'},guests:10,is_approved:'0',from_date:'2026-10-01',assigned_to:{username:'staff'},quotations_count:2},{uuid:'second',tourist:{name:'Alex'},guests:2,is_approved:1}]
 const result=listPresentation('inquiries',source)
 assert.equal(result.rows[0]._approval,'Pending')
 assert.equal(result.rows[1]._approval,'Approved')
 assert.equal(result.rows[0]._assigned,'staff')
 assert.equal(result.rows[0].from_date,'2026-10-01')
 const keys=result.columns.map(c=>c.key)
 assert.deepEqual(filterRows(result.rows,{search:'zara@example.test',startDate:'',endDate:''},'',keys).map(r=>r.uuid),['first'])
 assert.deepEqual(sortRows(result.rows,'_customer','asc').map(r=>r.uuid),['second','first'])
 assert.deepEqual(sortRows(result.rows,'guests','asc').map(r=>r.uuid),['second','first'])
 assert.equal(source[0]._customer,undefined)
})

test('empty lists retain headings and generic lists never expose credentials or JSON', () => {
 const empty=listPresentation('inquiries',[])
 assert.ok(empty.columns.some(c=>c.key==='inquiry_code'))
 const users=listPresentation('users',[{username:'staff',password:'hidden',roles:[{name:'SuperAdmin',pivot:{secret:'hidden'}}],is_active:0}])
 assert.equal(users.rows[0].roles,'SuperAdmin')
 assert.equal(users.rows[0].is_active,'Inactive')
 assert.ok(!users.columns.some(c=>c.key==='password'))
 const vehicles=listPresentation('vehicles',[{name:'Car',capacity:'6 guests',is_active:1}])
 assert.ok(vehicles.columns.some(c=>c.key==='capacity'))
})
