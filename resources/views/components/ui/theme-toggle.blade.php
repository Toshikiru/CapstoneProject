{{--
    Dark/Light mode toggle button.
    Persists preference in localStorage and applies the 'dark' class to <html>
    before first paint (via the inline script in the layout head) to avoid
    a flash of the wrong theme.
--}}
<button type="button"
        onclick="window.toggleTheme()"
        class="flex items-center justify-center w-9 h-9 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:text-slate-200 dark:hover:bg-slate-700 transition-colors"
        title="Toggle dark mode"
        aria-label="Toggle dark mode">
    <i class="fas fa-sun text-base dark:hidden"></i>
    <i class="fas fa-moon text-base hidden dark:block"></i>
</button>
