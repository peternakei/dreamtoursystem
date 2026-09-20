import { ref, watch } from 'vue'

const STORAGE_KEY = 'color-mode'

type ColorMode = 'light' | 'dark'

const colorMode = ref<ColorMode>(
  localStorage.getItem(STORAGE_KEY) === 'dark' ? 'dark' : 'light'
)

function applyMode(mode: ColorMode) {
  const html = document.documentElement
  if (mode === 'dark') {
    html.classList.add('dark')
  } else {
    html.classList.remove('dark')
  }
  localStorage.setItem(STORAGE_KEY, mode)
}

// Apply on init
applyMode(colorMode.value)

watch(colorMode, applyMode)

export function useColorMode() {
  const isDark = ref(colorMode.value === 'dark')

  watch(colorMode, (mode) => {
    isDark.value = mode === 'dark'
  })

  const toggle = () => {
    colorMode.value = colorMode.value === 'dark' ? 'light' : 'dark'
  }

  const setLight = () => { colorMode.value = 'light' }
  const setDark = () => { colorMode.value = 'dark' }

  return { isDark, toggle, setLight, setDark, colorMode }
}
