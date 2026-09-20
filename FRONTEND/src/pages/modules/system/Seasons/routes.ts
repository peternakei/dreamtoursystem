export default [
 {path:'/seasons/list',name:'seasons',component:()=>import('./SeasonsListPage.vue'),meta:{title:'Seasons'}},
 {path:'/seasons/:id/details',component:()=>import('./SeasonsDetailsPage.vue'),meta:{title:'Seasons'}}
]
