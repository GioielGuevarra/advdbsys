function setThemeOnLoad() {
    const isDark = localStorage.theme === 'dark' || 
        (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches);
    
    document.documentElement.classList[isDark ? 'add' : 'remove']('dark');
    const body = document.querySelector('body');
    body.setAttribute('data-theme', isDark ? 'dark' : 'light');
}

function switchTheme() {
    const isDark = document.documentElement.classList.contains('dark');
    document.documentElement.classList[isDark ? 'remove' : 'add']('dark');
    const body = document.querySelector('body');
    const newTheme = isDark ? 'light' : 'dark';
    body.setAttribute('data-theme', newTheme);
    localStorage.theme = newTheme;
}

export { setThemeOnLoad, switchTheme }