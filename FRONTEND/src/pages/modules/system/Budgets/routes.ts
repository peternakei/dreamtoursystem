export default [
 {path:'/budgets/list',name:'budgets',component:()=>import('./BudgetsListPage.vue'),meta:{title:'Budgets'}},
 {path:'/budgets/:id/details',component:()=>import('./BudgetsDetailsPage.vue'),meta:{title:'Budgets'}}
]
