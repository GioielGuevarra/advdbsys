import { cva } from 'class-variance-authority';

export { default as Badge } from './Badge.vue';

export const badgeVariants = cva(
  'inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2',
  {
    variants: {
      variant: {
        default:
          'border-transparent bg-primary text-primary-foreground hover:bg-primary/80',
        secondary:
          'border-transparent bg-secondary text-secondary-foreground hover:bg-secondary/80',
        destructive:
          'border-transparent bg-destructive text-destructive-foreground hover:bg-destructive/80',
        outline: 'text-foreground',
        pending:
          'border-transparent bg-yellow-100 text-yellow-700 dark:bg-yellow-900/50 dark:text-yellow-400',
        preparing:
          'border-transparent bg-orange-100 text-orange-700 dark:bg-orange-900/50 dark:text-orange-400',
        ready:
          'border-transparent bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-400',
        completed:
          'border-transparent bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-400',
        cancelled:
          'border-transparent bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-400'
      }
    },
    defaultVariants: {
      variant: 'default'
    }
  }
);
