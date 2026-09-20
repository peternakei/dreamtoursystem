// Color schemes matching InlineItemsComponent
export const colorClasses = {
	default: {
		border: 'border-l-primary',
		dot: 'bg-primary',
		gradient: 'bg-gradient-to-r from-primary/5 to-transparent',
		header: 'bg-muted/50 border-border text-foreground',
		hover: 'hover:border-primary hover:bg-primary/5',
		button: 'text-primary hover:text-primary hover:bg-primary/10',
		primary: 'bg-primary hover:bg-primary/90'
	},
	primary: {
		border: 'border-l-primary',
		dot: 'bg-primary',
		gradient: 'bg-gradient-to-r from-primary/10 to-transparent'
	},
	secondary: {
		border: 'border-l-muted',
		dot: 'bg-muted-foreground',
		gradient: 'bg-muted/50'
	},
    duara_orange: {
        border: 'border-l-[var(--sige-orange)]',
        dot: 'bg-[var(--sige-orange)]',
        gradient: 'bg-gradient-to-r from-[var(--sige-orange)]/20 to-transparent',
        header: 'bg-[var(--sige-orange)]/10 border-[var(--sige-orange)]/30 text-[var(--sige-orange-darker)]',
        hover: 'hover:border-[var(--sige-orange)]/50 hover:bg-[var(--sige-orange)]/10',
        button: 'text-[var(--sige-orange-dark)] hover:text-[var(--sige-orange-darker)] hover:bg-[var(--sige-orange)]/10',
        primary: 'bg-[var(--sige-orange)] hover:bg-[var(--sige-orange-dark)]'
    },
}

// Semantic color system supporting dark mode
export const semanticColors = {
  success: {
    bg: 'bg-green-500/10 dark:bg-green-500/20',
    text: 'text-green-700 dark:text-green-300',
    border: 'border-green-500/30',
    icon: 'bg-green-600 dark:bg-green-400'
  },
  error: {
    bg: 'bg-destructive/10 dark:bg-destructive/20',
    text: 'text-destructive dark:text-red-300',
    border: 'border-destructive/30',
    icon: 'bg-destructive dark:bg-red-400'
  },
  warning: {
    bg: 'bg-amber-500/10 dark:bg-amber-500/20',
    text: 'text-amber-700 dark:text-amber-300',
    border: 'border-amber-500/30',
    icon: 'bg-amber-600 dark:bg-amber-400'
  },
  info: {
    bg: 'bg-blue-500/10 dark:bg-blue-500/20',
    text: 'text-blue-700 dark:text-blue-300',
    border: 'border-blue-500/30',
    icon: 'bg-blue-600 dark:bg-blue-400'
  },
  neutral: {
    bg: 'bg-muted',
    text: 'text-muted-foreground',
    border: 'border-border',
    icon: 'bg-muted-foreground'
  }
}

// Status badge variants for StatusBadge component
export const statusBadgeVariants = {
  active: semanticColors.success,
  inactive: semanticColors.neutral,
  pending: semanticColors.warning,
  error: semanticColors.error,
  // Common status aliases
  enabled: semanticColors.success,
  disabled: semanticColors.neutral,
  warning: semanticColors.warning,
  success: semanticColors.success
}