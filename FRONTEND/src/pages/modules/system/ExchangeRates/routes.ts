export default [
 {path:'/exchange_rates/list',name:'exchange_rates',component:()=>import('./ExchangeRatesListPage.vue'),meta:{title:'Exchange Rates'}},
 {path:'/exchange_rates/:id/details',component:()=>import('./ExchangeRatesDetailsPage.vue'),meta:{title:'Exchange Rates'}}
]
