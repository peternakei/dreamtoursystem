export default [
 {path:'/faqs/list',name:'faqs',component:()=>import('./FaqsListPage.vue'),meta:{title:'Faqs'}},
 {path:'/faqs/:id/details',component:()=>import('./FaqsDetailsPage.vue'),meta:{title:'Faqs'}}
]
