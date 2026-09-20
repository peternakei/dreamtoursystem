export default [
 {path:'/banks/list',name:'banks',component:()=>import('./BanksListPage.vue'),meta:{title:'Banks'}},
 {path:'/banks/:id/details',component:()=>import('./BanksDetailsPage.vue'),meta:{title:'Banks'}}
]
