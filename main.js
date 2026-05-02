// main.js - Global Logic for Salud Equilibrio

const LANG_KEY = 'salud_equilibrio_lang';

function toggleDropdown(event) {
    if (event) event.stopPropagation();
    const dropdown = document.getElementById('lang-dropdown');
    if (dropdown) dropdown.classList.toggle('show');
}

function setLanguage(lang) {
    document.body.setAttribute('data-lang', lang);
    localStorage.setItem(LANG_KEY, lang);
    
    // Update UI elements if they exist
    const flagElem = document.getElementById('current-flag');
    const textElem = document.getElementById('current-lang-text');
    
    if (flagElem) flagElem.innerText = lang === 'es' ? '🇪🇸' : '🇬🇧';
    if (textElem) textElem.innerText = lang.toUpperCase();

    // Close dropdown
    const dropdown = document.getElementById('lang-dropdown');
    if (dropdown) dropdown.classList.remove('show');
    
    // Dispatch custom event for other components
    window.dispatchEvent(new CustomEvent('languageChanged', { detail: lang }));
}

function initLanguage() {
    const savedLang = localStorage.getItem(LANG_KEY) || 'es';
    setLanguage(savedLang);
}

document.addEventListener('DOMContentLoaded', () => {
    // Initialize Language
    initLanguage();

    // Close dropdown when clicking outside
    window.addEventListener('click', (event) => {
        if (!event.target.closest('.lang-trigger')) {
            const dropdowns = document.getElementsByClassName("dropdown-content");
            for (let i = 0; i < dropdowns.length; i++) {
                dropdowns[i].classList.remove('show');
            }
        }
    });

    // Reveal animations on scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('revealed');
            }
        });
    }, observerOptions);

    const revealElements = document.querySelectorAll('.glass-card, .text-content, .photo-card, section h2, section h1, .group');
    revealElements.forEach(el => {
        el.classList.add('reveal-on-scroll');
        observer.observe(el);
    });

    // Injected styles for animations
    const style = document.createElement('style');
    style.textContent = `
        .reveal-on-scroll {
            opacity: 0;
            transform: translateY(30px);
            transition: all 1s cubic-bezier(0.23, 1, 0.32, 1);
        }
        .revealed {
            opacity: 1;
            transform: translateY(0);
        }
    `;
    document.head.appendChild(style);

    // Smooth scroll for anchors
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href !== '#' && href.startsWith('#')) {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    window.scrollTo({
                        top: target.offsetTop - 100,
                        behavior: 'smooth'
                    });
                }
            }
        });
    });
});
