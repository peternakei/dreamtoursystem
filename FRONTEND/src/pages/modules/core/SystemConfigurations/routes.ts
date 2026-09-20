export default [
 {path:'/system_configurations/list',name:'system_configurations',component:()=>import('./SystemConfigurationsListPage.vue'),meta:{title:'System Configurations'}},
 {path:'/system_configurations/:id/details',component:()=>import('./SystemConfigurationsDetailsPage.vue'),meta:{title:'System Configurations'}}
]
