import { ref, onMounted, onUnmounted } from 'vue'

/**
 * Reactive media query composable
 * @param query - The media query string (e.g., '(min-width: 768px)')
 * @returns ref<boolean> - Whether the media query matches
 */
export function useMediaQuery(query: string) {
  const matches = ref(false)
  let mediaQuery: MediaQueryList | null = null

  const updateMatches = (event: MediaQueryListEvent | MediaQueryList) => {
    matches.value = event.matches
  }

  onMounted(() => {
    if (typeof window !== 'undefined' && 'matchMedia' in window) {
      mediaQuery = window.matchMedia(query)
      matches.value = mediaQuery.matches

      // Modern browsers
      if (mediaQuery.addEventListener) {
        mediaQuery.addEventListener('change', updateMatches)
      } else {
        // Legacy browsers
        mediaQuery.addListener(updateMatches)
      }
    }
  })

  onUnmounted(() => {
    if (mediaQuery) {
      if (mediaQuery.removeEventListener) {
        mediaQuery.removeEventListener('change', updateMatches)
      } else {
        // Legacy browsers
        mediaQuery.removeListener(updateMatches)
      }
    }
  })

  return matches
}

/**
 * Common breakpoint utilities
 */
export const useBreakpoints = () => {
  return {
    isMobile: useMediaQuery('(max-width: 767px)'),
    isTablet: useMediaQuery('(min-width: 768px) and (max-width: 1023px)'),
    isDesktop: useMediaQuery('(min-width: 768px)'),
    isLarge: useMediaQuery('(min-width: 1024px)'),
  }
}
