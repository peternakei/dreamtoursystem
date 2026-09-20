import type {RouteRecordRaw} from 'vue-router'
export default [
 {path:'/service_inquiries/list',name:'service-inquiries',component:()=>import('./ServiceInquiriesPage.vue'),meta:{title:'Service requests'}},
 {path:'/inquiries/:id/services',component:()=>import('./ServiceInquiriesPage.vue'),meta:{title:'Service request'}},
 {path:'/inquiries/:id',redirect:to=>'/inquiries/'+to.params.id+'/details'},
 {path:'/inquiries/list',name:'inquiries',component:()=>import('./InquiriesListPage.vue'),meta:{title:'Inquiries'}},
 {path:'/inquiries/:id/details',component:()=>import('./InquiriesDetailsPage.vue'),meta:{title:'Inquiries'}}
] satisfies RouteRecordRaw[]
