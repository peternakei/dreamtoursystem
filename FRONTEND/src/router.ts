import {brand} from '@/brand'
import {createRouter,createWebHistory} from 'vue-router'
import {session} from '@/composables/useAuth'
const files=import.meta.glob('./pages/modules/**/routes.ts',{eager:true})
const moduleRoutes=Object.values(files).flatMap((m:any)=>m.default)
const router=createRouter({history:createWebHistory(),routes:[
 {path:'/',redirect:'/dashboard'},
 {path:'/login',name:'Login',component:()=>import('@/pages/Auth/login/LoginPage.vue'),meta:{public:true}},
 {path:'/dashboard',component:()=>import('@/pages/modules/system/Dashboard/DashboardPage.vue'),meta:{title:'Dashboard'}},
 ...moduleRoutes,
 {path:'/:pathMatch(.*)*',component:()=>import('@/pages/NotFoundPage.vue'),meta:{public:true}}
]})
router.beforeEach(async to=>{
 if(to.meta.public)return true
 try {const user=await session();if(!user)return {name:'Login'};if(user.must_change_password){window.location.href=(import.meta.env.VITE_BACKEND_URL||'http://127.0.0.1:8001')+'/force-password-change';return false}}
 catch{return {name:'Login'}}
 return true
})
router.afterEach(to=>{document.title=`${to.meta.title||'Workspace'} · ${brand.name}`})
export default router
