export default [
 {path:'/testimonials/list',name:'testimonials',component:()=>import('./TestimonialsListPage.vue'),meta:{title:'Testimonials'}},
 {path:'/testimonials/:id/details',component:()=>import('./TestimonialsDetailsPage.vue'),meta:{title:'Testimonials'}}
]
