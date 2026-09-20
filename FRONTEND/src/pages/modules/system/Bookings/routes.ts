import type {RouteRecordRaw} from 'vue-router'
export default [
 {path:'/bookings',redirect:'/bookings/list'},
 {path:'/bookings/:id',redirect:to=>'/bookings/'+to.params.id+'/details'},
 {path:'/bookings/list',name:'bookings',component:()=>import('./BookingsListPage.vue'),meta:{title:'Bookings'}},
 {path:'/bookings/:id/details',component:()=>import('./BookingsDetailsPage.vue'),meta:{title:'Bookings'}}
] satisfies RouteRecordRaw[]
