/* =============================================================================
   VOWLYN — Front-end bootstrap
   Alpine.js for interactivity · GSAP for scroll-orchestrated reveals
   Native scrolling · Lucide for outlined icons
   ========================================================================== */

import Alpine from 'alpinejs';
import contentVideo from './content-video';
import collapse from '@alpinejs/collapse';
import intersect from '@alpinejs/intersect';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import {
    Activity,
    ArrowDown,
    ArrowDownRight,
    ArrowRight,
    ArrowUpRight,
    BadgeCheck,
    Bell,
    BrainCircuit,
    Bot,
    Check,
    CheckCircle2,
    ChevronDown,
    ChevronsUpDown,
    ClipboardCheck,
    Clock3,
    Cloud,
    CloudCog,
    Code2,
    Compass,
    Component,
    Cpu,
    CreditCard,
    FileCode,
    Gem,
    GitBranch,
    Gauge,
    Globe,
    Handshake,
    KeyRound,
    Layers,
    LayoutGrid,
    LayoutPanelTop,
    List,
    Loader2,
    Lock,
    LockKeyhole,
    Menu,
    MessageSquare,
    Monitor,
    Network,
    PackageCheck,
    PencilRuler,
    Pause,
    Play,
    PlayCircle,
    Plus,
    Plug,
    Quote,
    ReceiptText,
    Rocket,
    ScanEye,
    Search,
    Send,
    ShieldCheck,
    ShoppingBag,
    SlidersHorizontal,
    Smartphone,
    Sparkles,
    Star,
    TabletSmartphone,
    Timer,
    TimerReset,
    TrendingUp,
    UsersRound,
    Volume2,
    VolumeX,
    WifiOff,
    Workflow,
    X,
    Zap,
    createIcons,
} from 'lucide';

// Import only the icons rendered by this site. Importing `icons` from Lucide
// pulls the complete library into the entry chunk, even though most pages use
// only a small subset of it.
const siteIcons = {
    Activity,
    ArrowDown,
    ArrowDownRight,
    ArrowRight,
    ArrowUpRight,
    BadgeCheck,
    Bell,
    BrainCircuit,
    Bot,
    Check,
    CheckCircle2,
    ChevronDown,
    ChevronsUpDown,
    ClipboardCheck,
    Clock3,
    Cloud,
    CloudCog,
    Code2,
    Compass,
    Component,
    Cpu,
    CreditCard,
    FileCode,
    Gem,
    GitBranch,
    Gauge,
    Globe,
    Handshake,
    KeyRound,
    Layers,
    LayoutGrid,
    LayoutPanelTop,
    List,
    Loader2,
    Lock,
    LockKeyhole,
    Menu,
    MessageSquare,
    Monitor,
    Network,
    PackageCheck,
    PencilRuler,
    Pause,
    Play,
    PlayCircle,
    Plus,
    Plug,
    Quote,
    ReceiptText,
    Rocket,
    ScanEye,
    Search,
    Send,
    ShieldCheck,
    ShoppingBag,
    SlidersHorizontal,
    Smartphone,
    Sparkles,
    Star,
    TabletSmartphone,
    Timer,
    TimerReset,
    TrendingUp,
    UsersRound,
    Volume2,
    VolumeX,
    WifiOff,
    Workflow,
    X,
    Zap,
};

/* -------- Alpine -------------------------------------------------------- */
window.Alpine = Alpine;
Alpine.plugin(collapse);
Alpine.plugin(intersect);

/* -------- GSAP --------------------------------------------------------- */
gsap.registerPlugin(ScrollTrigger);

/* -------- Motion preferences ------------------------------------------- */
const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const isConstrainedDevice = Boolean(
    (navigator.hardwareConcurrency && navigator.hardwareConcurrency <= 4)
    || (navigator.deviceMemory && navigator.deviceMemory <= 4)
    || navigator.connection?.saveData
);
const useHeavyMotion = !prefersReducedMotion && !isConstrainedDevice;

if (isConstrainedDevice) {
    document.documentElement.classList.add('motion-lite');
}

/* -------- Scroll-triggered reveals ------------------------------------- */
function initReveals() {
    const els = document.querySelectorAll('[data-reveal]');
    els.forEach((el) => {
        const delay = parseFloat(el.dataset.revealDelay || '0');
        ScrollTrigger.create({
            trigger: el,
            start: 'top 88%',
            once: true,
            onEnter: () => {
                gsap.to(el, {
                    opacity: 1,
                    y: 0,
                    duration: 0.95,
                    delay,
                    ease: 'expo.out',
                    onStart: () => el.classList.add('is-visible'),
                });
            },
        });
    });
}

/* -------- Staggered group reveals ([data-stagger] parent) -------------- */
function initStaggers() {
    const groups = document.querySelectorAll('[data-stagger]');
    groups.forEach((group) => {
        const children = group.querySelectorAll('[data-stagger-item]');
        if (!children.length) return;

        gsap.set(children, { opacity: 0, y: 28 });

        ScrollTrigger.create({
            trigger: group,
            start: 'top 82%',
            once: true,
            onEnter: () => {
                gsap.to(children, {
                    opacity: 1,
                    y: 0,
                    duration: 0.9,
                    ease: 'expo.out',
                    stagger: parseFloat(group.dataset.stagger) || 0.08,
                });
            },
        });
    });
}

/* -------- Floating glass orbs (hero atmosphere) ----------------------- */
function initFloaters() {
    if (!useHeavyMotion) return;
    const floaters = document.querySelectorAll('[data-float]');
    floaters.forEach((el, i) => {
        const dur = 6 + (i % 4) * 1.5;
        const dist = 14 + (i % 3) * 6;
        gsap.to(el, {
            y: `+=${dist}`,
            duration: dur,
            ease: 'sine.inOut',
            yoyo: true,
            repeat: -1,
            delay: i * 0.4,
        });
    });
}

/* -------- Magnetic CTA (small lift on hover) -------------------------- */
function initMagnetic() {
    if (!useHeavyMotion) return;
    const items = document.querySelectorAll('[data-magnetic]');
    items.forEach((el) => {
        const strength = parseFloat(el.dataset.magnetic) || 0.25;
        el.addEventListener('mousemove', (e) => {
            const rect = el.getBoundingClientRect();
            const x = (e.clientX - rect.left - rect.width / 2) * strength;
            const y = (e.clientY - rect.top - rect.height / 2) * strength;
            gsap.to(el, { x, y, duration: 0.4, ease: 'power3.out' });
        });
        el.addEventListener('mouseleave', () => {
            gsap.to(el, { x: 0, y: 0, duration: 0.6, ease: 'elastic.out(1, 0.5)' });
        });
    });
}

/* -------- Nav glass-fade on scroll ------------------------------------- */
function initNavFade() {
    const nav = document.querySelector('[data-nav]');
    if (!nav) return;
    ScrollTrigger.create({
        start: 80,
        onUpdate: (self) => {
            nav.classList.toggle('is-scrolled', self.progress > 0);
        },
    });
}

/* -------- Services mega menu (desktop) --------------------------------
   Opening and closing is pure CSS (:has on [data-nav]) so the menu survives
   a failed bundle. This only keeps aria-expanded honest for screen readers
   and lets Escape dismiss the menu without moving focus — the hover state is
   suppressed until the pointer leaves the nav, otherwise Escape would look
   like it did nothing.
   --------------------------------------------------------------------- */
function initServicesMenu() {
    const nav = document.querySelector('[data-nav]');
    const trigger = nav?.querySelector('[data-services-trigger]');
    const panel = nav?.querySelector('[data-services-panel]');
    if (!nav || !trigger || !panel) return;

    const setExpanded = (open) => trigger.setAttribute('aria-expanded', open ? 'true' : 'false');
    setExpanded(false);

    ['mouseenter', 'focus'].forEach((type) => trigger.addEventListener(type, () => setExpanded(true)));
    panel.addEventListener('mouseenter', () => setExpanded(true));
    nav.addEventListener('mouseleave', () => setExpanded(false));

    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape' || trigger.getAttribute('aria-expanded') !== 'true') return;
        nav.setAttribute('data-services-suppressed', '');
        setExpanded(false);
        document.activeElement?.blur?.();
    });
    nav.addEventListener('pointerleave', () => nav.removeAttribute('data-services-suppressed'));
}

/* -------- Counter animation (stats) ------------------------------------ */
function initCounters() {
    const counters = document.querySelectorAll('[data-counter]');
    counters.forEach((el) => {
        const target = parseFloat(el.dataset.counter);
        const decimals = parseInt(el.dataset.counterDecimals || '0', 10);
        ScrollTrigger.create({
            trigger: el,
            start: 'top 90%',
            once: true,
            onEnter: () => {
                const obj = { val: 0 };
                gsap.to(obj, {
                    val: target,
                    duration: 1.8,
                    ease: 'expo.out',
                    onUpdate: () => {
                        el.textContent = obj.val.toFixed(decimals);
                    },
                });
            },
        });
    });
}

/* -------- Scroll-linked parallax ([data-parallax="0.15"]) -------------
   Inspired by Exo Ape's masonry collage. Each tagged element gets a
   translateY whose magnitude is the section's scroll distance × ratio.
   Cards 1 & 3 stay anchored, cards 2 & 4 drift = depth illusion.
   ------------------------------------------------------------------ */
function initParallax() {
    if (!useHeavyMotion) return;
    const items = document.querySelectorAll('[data-parallax]');
    items.forEach((el) => {
        const ratio = parseFloat(el.dataset.parallax) || 0.12;
        // Use the closest [data-parallax-group] as the scroll bound, or the section
        const group = el.closest('[data-parallax-group]') || el.closest('section') || el.parentElement;
        gsap.fromTo(el,
            { yPercent: ratio * 50 },     // start lower than natural
            {
                yPercent: ratio * -50,    // end higher than natural (drifts ~100% of ratio*section height)
                ease: 'none',
                scrollTrigger: {
                    trigger: group,
                    start: 'top bottom',
                    end: 'bottom top',
                    scrub: 1,
                    invalidateOnRefresh: true,
                },
            }
        );
    });
}

/* -------- Reel section reveal -----------------------------------------
   "Play" slides in from the left, "Reel" from the right, media scales up
   and the caption fades in — all scroll-linked so it reverses on scroll up.
   ------------------------------------------------------------------ */
function initReelReveal() {
    const section = document.querySelector('[data-reel]');
    if (!section) return;
    const leftWord = section.querySelector('[data-reel-word="left"]');
    const rightWord = section.querySelector('[data-reel-word="right"]');
    const media = section.querySelector('[data-reel-media]');
    const caption = document.querySelector('[data-reel-caption]');

    if (!useHeavyMotion) {
        gsap.set([leftWord, rightWord, media, caption].filter(Boolean), { opacity: 1, x: 0, scale: 1 });
        return;
    }

    // Initial state
    gsap.set(leftWord, { xPercent: -110, opacity: 0 });
    gsap.set(rightWord, { xPercent: 110, opacity: 0 });
    gsap.set(media, { scale: 0.9, opacity: 0 });
    if (caption) gsap.set(caption, { opacity: 0, y: 20 });

    const tl = gsap.timeline({
        scrollTrigger: {
            trigger: section,
            start: 'top 80%',
            end: 'top 25%',
            scrub: 1,
            invalidateOnRefresh: true,
        },
    });

    tl.to(leftWord, { xPercent: 0, opacity: 1, ease: 'none' }, 0)
        .to(rightWord, { xPercent: 0, opacity: 1, ease: 'none' }, 0)
        .to(media, { scale: 1, opacity: 1, ease: 'none' }, 0);
    if (caption) tl.to(caption, { opacity: 1, y: 0, ease: 'none' }, 0.4);
}

/* -------- Process progress thread --------------------------------------
   Scroll-fills the horizontal bar above the phase cards as the user moves
   through the Process section. Pure visual cue, no layout impact.
   --------------------------------------------------------------------- */
function initProcessProgress() {
    const bar = document.querySelector('[data-process-progress]');
    const grid = document.querySelector('[data-process-grid]');
    if (!bar || !grid) return;

    if (!useHeavyMotion) {
        bar.style.width = '100%';
        return;
    }

    ScrollTrigger.create({
        trigger: grid,
        start: 'top 75%',
        end: 'bottom 60%',
        scrub: 0.4,
        onUpdate: (self) => {
            bar.style.width = `${Math.round(self.progress * 100)}%`;
        },
    });
}

/* -------- Cursor spotlight on cards ------------------------------------
   Writes --mouse-x / --mouse-y CSS variables on pointermove so the
   .card-spotlight::before radial gradient follows the pointer.
   --------------------------------------------------------------------- */
function initSpotlights() {
    if (!useHeavyMotion) return;
    const cards = document.querySelectorAll('[data-spotlight]');
    if (!cards.length) return;

    cards.forEach((card) => {
        card.addEventListener('pointermove', (e) => {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            card.style.setProperty('--mouse-x', `${x}px`);
            card.style.setProperty('--mouse-y', `${y}px`);
        });
    });
}

/* ----- Loadout plates ---------------------------------------------------
   For each [data-loadout-card]:
     1. Measure every SVG stroke in [data-glyph-group] and set
        stroke-dasharray = length, stroke-dashoffset = length.
     2. On scroll-into-view, animate dashoffset → 0 (stroke draws itself).
     3. On pointer enter/leave, run a subtle 3D tilt + lift via GSAP.
   --------------------------------------------------------------------- */
function initLoadoutCards() {
    const cards = document.querySelectorAll('[data-loadout-card]');
    if (!cards.length) return;

    cards.forEach((card, idx) => {
        const strokes = card.querySelectorAll('[data-glyph-group] > *');
        const lengths = [];

        strokes.forEach((node) => {
            // getTotalLength works for path/line/circle/ellipse/rect/polygon
            let len = 80;
            try { len = node.getTotalLength?.() || 80; } catch (e) { /* noop */ }
            lengths.push(len);
            gsap.set(node, { strokeDasharray: len, strokeDashoffset: len });
        });

        // Stroke draw-in on enter
        ScrollTrigger.create({
            trigger: card,
            start: 'top 88%',
            once: true,
            onEnter: () => {
                gsap.to(strokes, {
                    strokeDashoffset: 0,
                    duration: 1.6,
                    ease: 'power2.out',
                    stagger: 0.09,
                    delay: 0.1 + (idx % 3) * 0.06,
                });
            },
        });

        if (!useHeavyMotion) return;

        // 3D hover tilt — pointer-position aware
        const onMove = (e) => {
            const rect = card.getBoundingClientRect();
            const px = (e.clientX - rect.left) / rect.width;  // 0..1
            const py = (e.clientY - rect.top) / rect.height;  // 0..1
            const rotY = (px - 0.5) * 6;   // ±3deg
            const rotX = (0.5 - py) * 6;
            gsap.to(card, { rotateX: rotX, rotateY: rotY, y: -6, duration: 0.45, ease: 'power3.out' });
        };
        const onLeave = () => {
            gsap.to(card, { rotateX: 0, rotateY: 0, y: 0, duration: 0.7, ease: 'power3.out' });
        };
        card.addEventListener('pointermove', onMove);
        card.addEventListener('pointerleave', onLeave);
    });
}

/* ----- Horizontal cinema strip (Services page) ---------------------------
   Pin a section for `track.scrollWidth - viewport` worth of vertical scroll,
   translating the track horizontally. Also updates a top progress bar and
   panel counter as we scrub.
   ----------------------------------------------------------------------- */
function initHorizontalCinema() {
    const section = document.querySelector('[data-cinema-section]');
    if (!section) return;
    const pin = section.querySelector('[data-cinema-pin]');
    const track = section.querySelector('[data-cinema-track]');
    const progress = section.querySelector('[data-cinema-progress]');
    const counter = section.querySelector('[data-cinema-counter]');
    const panels = track ? track.querySelectorAll('.cinema-panel') : [];
    if (!pin || !track || !panels.length) return;

    const getDistance = () => Math.max(0, track.scrollWidth - window.innerWidth);

    gsap.to(track, {
        x: () => -getDistance(),
        ease: 'none',
        scrollTrigger: {
            trigger: pin,
            pin: true,
            scrub: 0.6,
            start: 'top top',
            end: () => '+=' + getDistance(),
            invalidateOnRefresh: true,
            onUpdate: (self) => {
                if (progress) progress.style.transform = `scaleX(${self.progress})`;
                if (counter) {
                    const idx = Math.min(panels.length, Math.max(1, Math.ceil(self.progress * panels.length) || 1));
                    counter.textContent = String(idx).padStart(2, '0');
                }
            },
        },
    });
}

/* ----- Reading progress bar (About page) ---------------------------------
   Single scroll-driven horizontal bar pinned to the top of the viewport.
   ----------------------------------------------------------------------- */
function initReadingProgress() {
    const bar = document.querySelector('[data-reading-progress]');
    if (!bar) return;

    gsap.to(bar, {
        scaleX: 1,
        ease: 'none',
        scrollTrigger: {
            trigger: document.body,
            start: 'top top',
            end: 'bottom bottom',
            scrub: true,
        },
    });
}

/* ----- Timeline spine progress (About page) ------------------------------
   Vertical gradient line that fills as the user scrolls through the
   timeline section.
   ----------------------------------------------------------------------- */
function initTimelineProgress() {
    const wrap = document.querySelector('[data-timeline]');
    if (!wrap) return;
    const line = wrap.querySelector('[data-timeline-progress]');
    if (!line) return;

    gsap.to(line, {
        scaleY: 1,
        ease: 'none',
        scrollTrigger: {
            trigger: wrap,
            start: 'top 80%',
            end: 'bottom 70%',
            scrub: 0.8,
        },
    });
}

/* ----- Org-tree line draw (About → The Crew hierarchy SVG) ------------- */
function initOrgLines() {
    const paths = document.querySelectorAll('[data-org-line]');
    if (!paths.length) return;
    paths.forEach((path) => {
        const len = path.getTotalLength();
        gsap.set(path, { strokeDasharray: len, strokeDashoffset: len });
        gsap.to(path, {
            strokeDashoffset: 0,
            ease: 'power2.out',
            scrollTrigger: {
                trigger: path.closest('article') || path,
                start: 'top 75%',
                end: 'bottom 60%',
                scrub: 0.6,
            },
        });
    });
}

/* ----- Subtle 3D tilt on [data-tilt] cards (About crew cards) ---------- */
function initTilt() {
    if (!useHeavyMotion) return;
    const cards = document.querySelectorAll('[data-tilt]');
    if (!cards.length) return;
    cards.forEach((card) => {
        const onMove = (e) => {
            const r = card.getBoundingClientRect();
            const cx = (e.clientX - r.left) / r.width - 0.5;
            const cy = (e.clientY - r.top) / r.height - 0.5;
            gsap.to(card, {
                rotationY: cx * 6,
                rotationX: -cy * 6,
                transformPerspective: 800,
                transformOrigin: 'center',
                ease: 'power3.out',
                duration: 0.5,
            });
        };
        const onLeave = () => {
            gsap.to(card, { rotationY: 0, rotationX: 0, ease: 'power3.out', duration: 0.6 });
        };
        card.addEventListener('mousemove', onMove);
        card.addEventListener('mouseleave', onLeave);
    });
}

/* ----- Journal "load more" -------------------------------------------
   The button *is* the next-page link, so it works without JavaScript by
   navigating. With JavaScript the next page is fetched, its cards are
   appended, and the numbered links — kept in the markup for crawlers — step
   aside. A failed request falls back to that same navigation rather than
   leaving a dead button on screen.
   --------------------------------------------------------------------- */
function initJournalLoadMore() {
    const root = document.querySelector('[data-journal-more]');
    const grid = document.querySelector('[data-journal-cards]');
    if (!root || !grid) return;

    const button = root.querySelector('[data-journal-more-button]');
    const counter = root.querySelector('[data-journal-more-shown]');
    let shown = Number(counter?.textContent ?? '0') || 0;

    document.documentElement.classList.add('has-journal-load-more');

    button?.addEventListener('click', async (event) => {
        event.preventDefault();
        button.setAttribute('aria-busy', 'true');

        try {
            const response = await fetch(button.href);
            if (!response.ok) throw new Error(`Load more failed with ${response.status}`);

            const page = new DOMParser().parseFromString(await response.text(), 'text/html');
            const cards = [...(page.querySelector('[data-journal-cards]')?.children ?? [])];
            if (!cards.length) throw new Error('Load more found no further notes');

            cards.forEach((card) => {
                card.classList.add('journal-card--appended');
                grid.append(card);
            });

            // The count reflects what is on screen, and the button follows the
            // fetched page's own next link until there is nothing left to load.
            shown += cards.length;
            if (counter) counter.textContent = String(shown);

            const next = page.querySelector('[data-journal-more-button]');
            if (next) {
                button.href = next.href;
            } else {
                button.remove();
            }
        } catch {
            window.location.assign(button.href);
        } finally {
            button.removeAttribute('aria-busy');
        }
    });
}

/* ----- Journal share-link copy --------------------------------------- */
function initCopyLinks() {
    document.querySelectorAll('[data-copy-url]').forEach((button) => {
        button.addEventListener('click', async () => {
            const url = button.dataset.copyUrl;
            if (!url) return;

            try {
                await navigator.clipboard.writeText(url);
                const originalLabel = button.getAttribute('aria-label');
                const originalContent = button.innerHTML;
                button.textContent = button.dataset.copySuccess || 'Copied';
                button.setAttribute('aria-label', button.dataset.copySuccess || 'Link copied');
                window.setTimeout(() => {
                    button.innerHTML = originalContent;
                    button.setAttribute('aria-label', originalLabel || 'Copy link');
                }, 1800);
            } catch {
                window.prompt('Copy this article link:', url);
            }
        });
    });
}

/* -------- Boot --------------------------------------------------------- */
document.addEventListener('DOMContentLoaded', () => {
    createIcons({ icons: siteIcons });
    initReveals();
    initStaggers();
    initFloaters();
    initMagnetic();
    initNavFade();
    initServicesMenu();
    initCounters();
    initParallax();
    initReelReveal();
    initProcessProgress();
    initSpotlights();
    initLoadoutCards();
    initHorizontalCinema();
    initReadingProgress();
    initTimelineProgress();
    initOrgLines();
    initTilt();
    initCopyLinks();
    initJournalLoadMore();

    // Refresh ScrollTrigger after icons render (layout shifts)
    requestAnimationFrame(() => ScrollTrigger.refresh());
});

Alpine.data('contentVideo', contentVideo);
Alpine.start();
