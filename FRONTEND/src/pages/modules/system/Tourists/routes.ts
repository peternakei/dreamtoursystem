import type {RouteRecordRaw} from 'vue-router'
export default [
 {path:'/tourists',redirect:'/tourists/list'},
 {path:'/tourists/:id',redirect:to=>'/tourists/'+to.params.id+'/details'},
 {path:'/tourists/list',name:'tourists',component:()=>import('./TouristsListPage.vue'),meta:{title:'Tourists'}},
 {path:'/tourists/:id/details',component:()=>import('./TouristsDetailsPage.vue'),meta:{title:'Tourists'}}
] satisfies RouteRecordRaw[]
