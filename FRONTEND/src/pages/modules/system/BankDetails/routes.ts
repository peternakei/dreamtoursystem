export default [
 {path:'/bank_details/list',name:'bank_details',component:()=>import('./BankDetailsListPage.vue'),meta:{title:'Bank Details'}},
 {path:'/bank_details/:id/details',component:()=>import('./BankDetailsDetailsPage.vue'),meta:{title:'Bank Details'}}
]
