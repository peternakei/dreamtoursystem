import type {RouteRecordRaw} from 'vue-router'
export default [
 {path:'/invoices',redirect:'/invoices/list'},
 {path:'/invoices/:id',redirect:to=>'/invoices/'+to.params.id+'/details'},
 {path:'/invoices/list',name:'invoices',component:()=>import('./InvoicesListPage.vue'),meta:{title:'Invoices'}},
 {path:'/invoices/:id/details',component:()=>import('./InvoicesDetailsPage.vue'),meta:{title:'Invoices'}}
] satisfies RouteRecordRaw[]
