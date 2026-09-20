export default [
 {path:'/subscriptions/list',name:'subscriptions',component:()=>import('./SubscriptionsListPage.vue'),meta:{title:'Subscriptions'}},
 {path:'/subscriptions/:id/details',component:()=>import('./SubscriptionsDetailsPage.vue'),meta:{title:'Subscriptions'}}
]
