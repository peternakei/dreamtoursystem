export default [
 {path:'/inquiries/list',name:'inquiries',component:()=>import('./InquiriesListPage.vue'),meta:{title:'Inquiries'}},
 {path:'/inquiries/:id/details',component:()=>import('./InquiriesDetailsPage.vue'),meta:{title:'Inquiries'}}
]
