export default [
 {path:'/quotations/list',name:'quotations',component:()=>import('./QuotationsListPage.vue'),meta:{title:'Quotations'}},
 {path:'/quotations/:id/details',component:()=>import('./QuotationsDetailsPage.vue'),meta:{title:'Quotations'}}
]
