import type {RouteRecordRaw} from 'vue-router'
export default [
 {path:'/receipts',redirect:'/receipts/list'},
 {path:'/receipts/:id',redirect:to=>'/receipts/'+to.params.id+'/details'},
 {path:'/receipts/list',name:'receipts',component:()=>import('./ReceiptsListPage.vue'),meta:{title:'Receipts'}},
 {path:'/receipts/:id/details',component:()=>import('./ReceiptsDetailsPage.vue'),meta:{title:'Receipts'}}
] satisfies RouteRecordRaw[]
