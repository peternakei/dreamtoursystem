export default [
 {path:'/receipts/list',name:'receipts',component:()=>import('./ReceiptsListPage.vue'),meta:{title:'Receipts'}},
 {path:'/receipts/:id/details',component:()=>import('./ReceiptsDetailsPage.vue'),meta:{title:'Receipts'}}
]
