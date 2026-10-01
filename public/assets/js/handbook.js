(() => {
    const handbook = document.querySelector('[data-handbook]');
    if (!handbook) return;

    const openButton = handbook.querySelector('[data-handbook-menu-open]');
    const closeButtons = handbook.querySelectorAll('[data-handbook-menu-close]');
    const setMenu = (open) => {
        handbook.classList.toggle('is-menu-open', open);
        openButton?.setAttribute('aria-expanded', String(open));
        document.body.classList.toggle('handbook-menu-open', open);
    };
    openButton?.addEventListener('click', () => setMenu(true));
    closeButtons.forEach((button) => button.addEventListener('click', () => setMenu(false)));
    document.addEventListener('keydown', (event) => { if (event.key === 'Escape') setMenu(false); });

    const links = [...handbook.querySelectorAll('[data-handbook-section-link]')];
    const sections = links.map((link) => document.getElementById(link.dataset.handbookSectionLink)).filter(Boolean);
    if (!sections.length || !('IntersectionObserver' in window)) return;
    const activate = (id) => links.forEach((link) => {
        const active = link.dataset.handbookSectionLink === id;
        link.classList.toggle('is-active', active);
        active ? link.setAttribute('aria-current', 'location') : link.removeAttribute('aria-current');
    });
    const observer = new IntersectionObserver((entries) => {
        const visible = entries.filter((entry) => entry.isIntersecting).sort((a, b) => a.boundingClientRect.top - b.boundingClientRect.top);
        if (visible[0]) activate(visible[0].target.id);
    }, {rootMargin: '-20% 0px -65% 0px'});
    sections.forEach((section) => observer.observe(section));
})();
