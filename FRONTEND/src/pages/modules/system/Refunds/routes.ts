export default [
 {path:'/refunds/list',name:'refunds',component:()=>import('./RefundsListPage.vue'),meta:{title:'Refunds'}},
 {path:'/refunds/:id/details',component:()=>import('./RefundsDetailsPage.vue'),meta:{title:'Refunds'}}
]
