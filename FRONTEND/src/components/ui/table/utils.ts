import type {Ref} from 'vue'
export function valueUpdater<T>(updaterOrValue: T | ((value: T) => T), ref: Ref<T>) {
 ref.value = typeof updaterOrValue === 'function' ? (updaterOrValue as (value:T)=>T)(ref.value) : updaterOrValue
}
