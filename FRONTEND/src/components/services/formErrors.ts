import axios from 'axios'
import {errorMessage} from '@/axiosClient'
export function formError(error:unknown):string {if(axios.isAxiosError(error)&&error.response?.data?.errors)return Object.values(error.response.data.errors).flat().join(' ');return errorMessage(error)}
export const options=(values:string[])=>values.map(value=>({value,label:value.replaceAll('_',' ').replace(/\b\w/g,s=>s.toUpperCase())}))
