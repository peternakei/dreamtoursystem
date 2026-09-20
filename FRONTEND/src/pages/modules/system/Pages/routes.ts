export default [
 {path:'/travel_content/list',name:'travel-content',component:()=>import('./TravelContentPage.vue'),meta:{title:'Travel content'}},
 {path:'/pages/list',name:'pages',component:()=>import('./PagesListPage.vue'),meta:{title:'Pages'}},
 {path:'/pages/:id/details',component:()=>import('./PagesDetailsPage.vue'),meta:{title:'Pages'}}
]
