export default [
 {path:'/invoices/list',name:'invoices',component:()=>import('./InvoicesListPage.vue'),meta:{title:'Invoices'}},
 {path:'/invoices/:id/details',component:()=>import('./InvoicesDetailsPage.vue'),meta:{title:'Invoices'}}
]
