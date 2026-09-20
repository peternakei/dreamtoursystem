import type {RouteRecordRaw} from 'vue-router'
export default [
 {path:'/quotation-versions/:id/builder',component:()=>import('./QuotationBuilderPage.vue'),meta:{title:'Quotation builder'}},
 {path:'/quotation-versions/:id/preview',component:()=>import('./QuotationPreviewPage.vue'),meta:{title:'Quotation preview'}},
 {path:'/inquiries/:id/quotations/new',component:()=>import('./QuotationCreatePage.vue'),meta:{title:'Create quotation'}},
 {path:'/quotations/:id',redirect:to=>'/quotations/'+to.params.id+'/details'},
 {path:'/quotations/list',name:'quotations',component:()=>import('./QuotationsListPage.vue'),meta:{title:'Quotations'}},
 {path:'/quotations/:id/details',component:()=>import('./QuotationsDetailsPage.vue'),meta:{title:'Quotations'}}
] satisfies RouteRecordRaw[]
