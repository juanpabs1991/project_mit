import './bootstrap';

const revealApplication = () => window.requestAnimationFrame(() => document.documentElement.classList.add('app-ready'));
document.readyState === 'loading'
    ? document.addEventListener('DOMContentLoaded', revealApplication, { once: true })
    : revealApplication();
