import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Muncul saat scroll (sekali saja). CSS menonaktifkannya bila prefers-reduced-motion.
document.documentElement.classList.add('js');

const revealTargets = document.querySelectorAll('[data-reveal]');

if ('IntersectionObserver' in window) {
    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-revealed');
                    observer.unobserve(entry.target);
                }
            });
        },
        { rootMargin: '0px 0px -10% 0px', threshold: 0.05 },
    );

    revealTargets.forEach((el) => observer.observe(el));
} else {
    revealTargets.forEach((el) => el.classList.add('is-revealed'));
}
