<script setup lang="ts">
import {ref} from 'vue'
import {brand} from '@/brand'
import BrandLogo from '@/components/BrandLogo.vue'
import {useRouter} from 'vue-router'
import {Eye,EyeOff,LoaderCircle} from 'lucide-vue-next'
import {Button} from '@/components/ui/button'
import {Input} from '@/components/ui/input'
import {Label} from '@/components/ui/label'
import {login,user} from '@/composables/useAuth'
import {errorMessage} from '@/axiosClient'
const username=ref(''),password=ref(''),remember=ref(false),visible=ref(false),busy=ref(false),error=ref('')
const router=useRouter()
const backendUrl=import.meta.env.VITE_BACKEND_URL || 'http://127.0.0.1:8001'
async function submit(){busy.value=true;error.value='';try{await login(username.value,password.value,remember.value);if(user.value?.must_change_password)window.location.href=backendUrl+'/force-password-change';else await router.push('/dashboard')}catch(e){error.value=errorMessage(e)}finally{busy.value=false}}
</script>
<template>
<div class="grid min-h-screen lg:grid-cols-12">
<div class="relative hidden lg:col-span-8 lg:block">
<img src="/images/safari.webp" alt="Elephants on safari in Tanzania" class="absolute inset-0 h-full w-full object-cover">
<div class="absolute inset-0 bg-black/35"/>
<div class="absolute bottom-12 left-12 right-12 text-white">
<p class="text-3xl font-semibold">{{brand.name}}</p>
<p class="mt-3 max-w-xl text-lg">{{brand.tagline}}</p>
</div>
</div>
<div class="flex flex-col justify-center p-8 lg:col-span-4 lg:p-12">
<BrandLogo class="mb-10 w-full max-w-sm self-center"/>
<form @submit.prevent="submit" class="brand-form space-y-5 rounded-xl p-6 shadow-sm">
<div>
<h1 class="text-2xl font-semibold">Welcome back</h1>
<p class="mt-2 text-sm text-muted-foreground">Sign in to manage your safari operations.</p>
</div>
<p v-if="error" role="alert" class="rounded-md border border-destructive/30 bg-destructive/10 p-3 text-sm text-destructive">{{error}}</p>
<div class="space-y-2">
<Label for="username">Email address</Label>
<Input id="username" v-model="username" type="email" autocomplete="username" required :disabled="busy"/>
</div>
<div class="space-y-2">
<Label for="password">Password</Label>
<div class="relative">
<Input id="password" v-model="password" :type="visible?'text':'password'" autocomplete="current-password" required :disabled="busy" class="pr-10"/>
<button type="button" @click="visible=!visible" class="absolute right-3 top-3" :aria-label="visible?'Hide password':'Show password'">
<component :is="visible?EyeOff:Eye" class="h-4 w-4"/>
</button>
</div>
</div>
<label class="flex items-center gap-2 text-sm">
<input type="checkbox" v-model="remember">Remember me</label>
<Button type="submit" class="w-full" :disabled="busy">
<LoaderCircle v-if="busy" class="h-4 w-4 animate-spin"/>{{busy?'Signing in…':'Sign in'}}</Button>
<a :href="backendUrl+'/forgot-password'" class="block text-center text-sm text-muted-foreground underline">Forgot your password?</a>
</form>
<p class="mt-12 text-xs text-muted-foreground">© {{new Date().getFullYear()}} {{brand.name}}</p>
</div>
</div>
</template>
