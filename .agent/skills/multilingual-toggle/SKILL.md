---
name: Multilingual Toggle System (ES/EN)
description: How to implement the CSS+JS bilingual toggle (Spanish/English) used across the Salud Equilibrio portal. Uses CSS class visibility, localStorage persistence, and a dropdown selector with country flags.
---

# Multilingual Toggle System (ES/EN)

This skill describes the **standard pattern** used across the Salud Equilibrio project to provide bilingual **Spanish / English** content on any page, with a persistent dropdown language selector.

## Architecture Overview

The system uses a **CSS-first approach** — no build tools, no i18n libraries. Content for both languages lives inline in the HTML, and visibility is toggled via a `data-lang` attribute on `<body>`.

```
┌─────────────────────────────────────────────┐
│  <body data-lang="es">                      │
│                                             │
│  ┌──────────┐  ┌──────────┐                 │
│  │ .lang-es │  │ .lang-en │  ← CSS hides    │
│  │ VISIBLE  │  │ HIDDEN   │    .lang-en     │
│  └──────────┘  └──────────┘                 │
│                                             │
│  localStorage('salud_equilibrio_lang')      │
│  ↕ persists choice across pages             │
└─────────────────────────────────────────────┘
```

## Step 1 — CSS Rules

Add these CSS rules to the page `<style>` block (or to a shared CSS file). These control visibility of block-level and inline-level bilingual elements:

```css
/* ─── Language toggle system ─── */
/* Block-level: paragraphs, divs, sections */
.lang-en { display: none; }
[data-lang="en"] .lang-en { display: block; }
[data-lang="en"] .lang-es { display: none; }

/* Inline-level: spans inside links, buttons, badges */
.lang-es.inline { display: inline; }
.lang-en.inline { display: none; }
[data-lang="en"] .lang-es.inline { display: none; }
[data-lang="en"] .lang-en.inline { display: inline; }
```

### Block vs Inline Usage

- **Block elements** (paragraphs, divs, headings): Use `class="lang-es"` / `class="lang-en"` — they toggle between `display: block` and `display: none`.
- **Inline elements** (spans inside links, buttons, badges): Use `class="lang-es inline"` / `class="lang-en inline"` — they toggle between `display: inline` and `display: none`.

## Step 2 — Dropdown Trigger Styles

Add these styles for the language selector dropdown UI:

```css
.dropdown-content {
    display: none;
    position: absolute;
    right: 0;
    top: 100%;
    background: rgba(1, 8, 19, 0.95);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(0, 180, 216, 0.2);
    border-radius: 1rem;
    min-width: 150px;
    box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.5);
    padding: 0.5rem;
    z-index: 100;
}

.dropdown-content.show { display: block; }

.lang-option {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 16px;
    cursor: pointer;
    border-radius: 0.5rem;
    transition: all 0.3s ease;
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--text-muted);
}

.lang-option:hover {
    background: rgba(0, 119, 182, 0.1);
    color: #fff;
}

.lang-option.active {
    color: var(--water-clear);
}

.lang-trigger {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    padding: 8px 16px;
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(0, 180, 216, 0.15);
    border-radius: 50px;
    font-size: 0.85rem;
    font-weight: 700;
    color: var(--text-white, #fff);
    transition: all 0.3s ease;
}

.lang-trigger:hover {
    border-color: rgba(0, 180, 216, 0.4);
    background: rgba(0, 180, 216, 0.05);
}
```

> **Note**: If the page uses a gold/zen aesthetic (like the shiatsu pages), adjust colors from `rgba(0, 180, 216, ...)` (water blue) to `rgba(212, 175, 55, ...)` (gold).

## Step 3 — HTML: `data-lang` on `<body>`

Set `<body data-lang="es">` as the default language attribute:

```html
<body data-lang="es">
```

## Step 4 — HTML: Language Toggle Dropdown

Place this inside the navigation bar (typically inside `.nav-links` or the nav header):

```html
<!-- Language Dropdown -->
<div style="position: relative; display: inline-block;">
    <div class="lang-trigger" onclick="toggleDropdown(event)">
        <span id="current-flag">🇪🇸</span>
        <span id="current-lang-text">ES</span>
        <svg style="width:10px;height:10px;margin-left:4px;opacity:0.5;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"></path>
        </svg>
    </div>
    <div class="dropdown-content" id="lang-dropdown">
        <div class="lang-option" onclick="setLanguage('es')">
            <span style="font-size:1.2rem;">🇪🇸</span>
            <span>Castellano</span>
        </div>
        <div class="lang-option" onclick="setLanguage('en')">
            <span style="font-size:1.2rem;">🇬🇧</span>
            <span>English</span>
        </div>
    </div>
</div>
```

## Step 5 — HTML: Bilingual Content Markup

### Block-level content (headings, paragraphs, divs):

```html
<h1>
    <span class="lang-es">Título en español</span>
    <span class="lang-en">Title in English</span>
</h1>

<p>
    <span class="lang-es">Texto en español con <strong>negritas</strong>.</span>
    <span class="lang-en">Text in English with <strong>bold text</strong>.</span>
</p>
```

### Inline content (inside links, buttons, badges):

```html
<a href="index.html">
    <span class="lang-es inline">Inicio</span>
    <span class="lang-en inline">Home</span>
</a>

<a href="#contacto" class="btn-primary">
    <span class="lang-es inline">Solicitar Información</span>
    <span class="lang-en inline">Request Information</span>
</a>
```

### Mixed block + inline in same parent:

```html
<div class="highlight-box">
    <strong>
        <span class="lang-es inline">¿Por qué funciona?</span>
        <span class="lang-en inline">Why does it work?</span>
    </strong><br>
    <span class="lang-es">Explicación en español...</span>
    <span class="lang-en">Explanation in English...</span>
</div>
```

## Step 6 — JavaScript: Language Toggle Logic

Place this script at the bottom of the page, before `</body>`:

```html
<script>
    const LANG_KEY = 'salud_equilibrio_lang';

    function toggleDropdown(event) {
        event.stopPropagation();
        document.getElementById('lang-dropdown').classList.toggle('show');
    }

    // Close dropdown when clicking outside
    window.onclick = function(event) {
        if (!event.target.closest('.lang-trigger')) {
            const dropdowns = document.getElementsByClassName("dropdown-content");
            for (let i = 0; i < dropdowns.length; i++) {
                dropdowns[i].classList.remove('show');
            }
        }
    }

    function setLanguage(lang) {
        document.body.setAttribute('data-lang', lang);
        localStorage.setItem(LANG_KEY, lang);
        
        // Update UI elements
        const flag = lang === 'es' ? '🇪🇸' : '🇬🇧';
        const text = lang.toUpperCase();
        document.getElementById('current-flag').innerText = flag;
        document.getElementById('current-lang-text').innerText = text;

        // Close dropdown
        const dropdown = document.getElementById('lang-dropdown');
        if(dropdown) dropdown.classList.remove('show');
    }

    function initLanguage() {
        const savedLang = localStorage.getItem(LANG_KEY) || 'es';
        setLanguage(savedLang);
    }

    document.addEventListener('DOMContentLoaded', initLanguage);
</script>
```

### Key Details:

| Feature | Implementation |
|---------|---------------|
| **Storage key** | `salud_equilibrio_lang` — shared across ALL pages |
| **Default language** | `es` (Spanish) |
| **Persistence** | `localStorage` — survives page navigation and sessions |
| **Supported languages** | `es`, `en` |
| **Flag mapping** | `es` → 🇪🇸, `en` → 🇬🇧 |

## Step 7 — Adding More Languages (Future Extension)

To add a third language (e.g., French):

1. Add CSS rule:
```css
.lang-fr { display: none; }
[data-lang="fr"] .lang-fr { display: block; }
[data-lang="fr"] .lang-fr.inline { display: inline; }
[data-lang="fr"] .lang-es { display: none; }
[data-lang="fr"] .lang-en { display: none; }
[data-lang="fr"] .lang-es.inline { display: none; }
[data-lang="fr"] .lang-en.inline { display: none; }
```

2. Add a new dropdown option:
```html
<div class="lang-option" onclick="setLanguage('fr')">
    <span style="font-size:1.2rem;">🇫🇷</span>
    <span>Français</span>
</div>
```

3. Update the flag mapping in `setLanguage()`:
```javascript
const flags = { es: '🇪🇸', en: '🇬🇧', fr: '🇫🇷' };
const flag = flags[lang] || '🌐';
```

4. Add `<span class="lang-fr">...</span>` or `<span class="lang-fr inline">...</span>` for every content block.

## Reference Implementations

- **Kangen testimonial (with Kangen ocean theme)**: `kangen_water/kangen/testimonios/gabriel_boxeador/index.html`
- **Shiatsu Lung Channel (with gold/zen theme)**: `tecnicas_masaje/shiatsu/canal_de_pulmon/index.html`

## Checklist for New Pages

- [ ] `<body data-lang="es">` is set
- [ ] CSS visibility rules are included (block + inline)
- [ ] Dropdown trigger + content HTML is placed in the nav
- [ ] Every user-visible text has both `lang-es` and `lang-en` spans
- [ ] Inline elements inside `<a>`, `<button>`, `<span>` use the `.inline` class
- [ ] The JS block is placed before `</body>`
- [ ] `LANG_KEY` matches: `'salud_equilibrio_lang'`
