import {ref} from 'vue'
import api, {setCsrf} from '@/axiosClient'
export interface User {name:string; username:string; roles:string[]; must_change_password:boolean}
export const user=ref<User|null>(null)
export async function session() {const {data}=await api.get('/workspace/session');user.value=data.user;setCsrf(data.csrf_token);return data.user}
export async function login(username:string,password:string,remember:boolean) {await session();const {data}=await api.post('/workspace/login',{username,password,remember});user.value=data.user;setCsrf(data.csrf_token)}
export async function logout() {await api.post('/workspace/logout');user.value=null;setCsrf('')}
