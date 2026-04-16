@extends(auth()->check() ? 'layouts.app' : 'layouts.public')

@section('title', 'Developer')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">

<style>
    :root {
        --developer-navy: #0a2347;
        --developer-navy-soft: #123767;
        --developer-ink: #0f172a;
        --developer-mist: #eff4fb;
        --developer-panel: rgba(255, 255, 255, 0.92);
        --developer-line: rgba(148, 163, 184, 0.22);
        --developer-gold: #c69749;
        --developer-gold-soft: #f5e7cb;
        --developer-sky: #dbeafe;
    }

    @keyframes developerRise {
        from {
            opacity: 0;
            transform: translateY(24px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes developerFloat {
        0%,
        100% {
            transform: translateY(0);
        }

        50% {
            transform: translateY(-10px);
        }
    }

    .developer-stage {
        position: relative;
        isolation: isolate;
        padding: clamp(0.25rem, 1vw, 0.75rem);
        font-family: 'Plus Jakarta Sans', sans-serif;
        overflow-x: clip;
    }

    .developer-stage::before,
    .developer-stage::after {
        content: "";
        position: absolute;
        border-radius: 999px;
        filter: blur(12px);
        z-index: -1;
        pointer-events: none;
    }

    .developer-stage::before {
        top: -4rem;
        left: -2rem;
        width: 16rem;
        height: 16rem;
        background: radial-gradient(circle, rgba(198, 151, 73, 0.18) 0%, rgba(198, 151, 73, 0) 72%);
    }

    .developer-stage::after {
        right: -3rem;
        bottom: -4rem;
        width: 20rem;
        height: 20rem;
        background: radial-gradient(circle, rgba(37, 99, 235, 0.12) 0%, rgba(37, 99, 235, 0) 74%);
    }

    .developer-shell {
        display: grid;
        gap: 1.2rem;
        width: 100%;
        max-width: 1280px;
        margin: 0 auto;
        min-width: 0;
    }

    .developer-hero {
        position: relative;
        overflow: visible;
        display: grid;
        grid-template-columns: minmax(0, 1.15fr) minmax(320px, 0.85fr);
        gap: 1.25rem;
        padding: clamp(1.4rem, 2vw, 2rem);
        border-radius: 36px;
        max-width: 100%;
        background:
            linear-gradient(135deg, rgba(255, 255, 255, 0.08), rgba(255, 255, 255, 0)),
            linear-gradient(135deg, #061a36 0%, #0d2c58 55%, #123e71 100%);
        box-shadow: 0 34px 65px -42px rgba(8, 24, 48, 0.9);
        animation: developerRise 0.55s ease-out both;
    }

    .developer-hero::before {
        content: "";
        position: absolute;
        inset: 0;
        background:
            radial-gradient(circle at top right, rgba(255, 255, 255, 0.14), transparent 24%),
            linear-gradient(rgba(255, 255, 255, 0.05) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255, 255, 255, 0.05) 1px, transparent 1px);
        background-size: auto, 34px 34px, 34px 34px;
        mask-image: linear-gradient(180deg, rgba(0, 0, 0, 0.9), transparent 95%);
        pointer-events: none;
    }

    .developer-copy,
    .developer-portrait-column {
        position: relative;
        z-index: 1;
    }

    .developer-copy {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 1.5rem;
        min-width: 0;
    }

    .developer-topline {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        align-items: center;
    }

    .developer-kicker,
    .developer-status {
        display: inline-flex;
        align-items: center;
        gap: 0.55rem;
        min-height: 40px;
        padding: 0.55rem 0.95rem;
        border-radius: 999px;
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }

    .developer-kicker i,
    .developer-chip i,
    .developer-button i,
    .developer-orbit i,
    .developer-stack-pill i,
    .developer-panel-label i,
    .developer-contact-icon i,
    .developer-feature-icon i,
    .developer-visual-note-icon i {
        display: block;
        line-height: 1;
    }

    .developer-kicker {
        background: rgba(255, 255, 255, 0.1);
        color: rgba(255, 255, 255, 0.92);
        border: 1px solid rgba(255, 255, 255, 0.12);
    }

    .developer-status {
        background: rgba(198, 151, 73, 0.14);
        color: #f8e8c8;
        border: 1px solid rgba(198, 151, 73, 0.32);
    }

    .developer-status-dot {
        width: 10px;
        height: 10px;
        border-radius: 999px;
        background: #f6d28f;
        box-shadow: 0 0 0 6px rgba(246, 210, 143, 0.14);
    }

    .developer-title-wrap {
        max-width: 640px;
    }

    .developer-name {
        margin: 0;
        font-family: 'Space Grotesk', sans-serif;
        font-size: clamp(3rem, 6vw, 5.3rem);
        line-height: 0.94;
        letter-spacing: -0.06em;
        color: #ffffff;
    }

    .developer-title-accent {
        display: block;
        margin-top: 0.6rem;
        color: rgba(255, 255, 255, 0.88);
        font-size: clamp(1rem, 1.8vw, 1.25rem);
        letter-spacing: 0;
    }

    .developer-lead {
        max-width: 38rem;
        margin: 1.15rem 0 0;
        color: rgba(255, 255, 255, 0.76);
        font-size: 1.05rem;
        line-height: 1.8;
    }

    .developer-chip-row {
        display: flex;
        flex-wrap: wrap;
        gap: 0.65rem;
    }

    .developer-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.7rem 0.9rem;
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.09);
        border: 1px solid rgba(255, 255, 255, 0.08);
        color: rgba(255, 255, 255, 0.9);
        font-size: 0.92rem;
        font-weight: 600;
    }

    .developer-chip i {
        color: #f6d28f;
    }

    .developer-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.85rem;
        align-items: stretch;
    }

    .developer-contact-direct {
        display: inline-flex;
        align-items: center;
        gap: 0.85rem;
        min-height: 52px;
        padding: 0.9rem 1rem;
        border-radius: 18px;
        background: linear-gradient(135deg, rgba(246, 210, 143, 0.18) 0%, rgba(198, 151, 73, 0.12) 100%);
        border: 1px solid rgba(246, 210, 143, 0.34);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.12);
        flex: 1 1 20rem;
    }

    .developer-contact-direct-icon {
        width: 40px;
        height: 40px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 14px;
        background: rgba(246, 210, 143, 0.2);
        color: #f6d28f;
    }

    .developer-contact-direct-copy {
        display: flex;
        flex-direction: column;
        min-width: 0;
    }

    .developer-contact-direct-label {
        color: rgba(255, 255, 255, 0.6);
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .developer-contact-direct-value {
        color: #ffffff;
        font-size: 0.98rem;
        font-weight: 700;
        line-height: 1.35;
        word-break: break-word;
        user-select: all;
    }

    .developer-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.7rem;
        min-height: 52px;
        padding: 0.9rem 1.25rem;
        border-radius: 18px;
        text-decoration: none !important;
        font-weight: 700;
        transition: transform 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease, color 0.2s ease;
    }

    .developer-button-primary {
        background: linear-gradient(135deg, #f6d28f 0%, #c69749 100%);
        color: #1f2937;
        box-shadow: 0 22px 36px -26px rgba(198, 151, 73, 0.9);
    }

    .developer-button-primary:hover {
        color: #111827;
        transform: translateY(-2px);
        box-shadow: 0 26px 42px -28px rgba(198, 151, 73, 0.95);
    }

    .developer-button-secondary {
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.14);
        color: #ffffff;
    }

    .developer-button-secondary:hover {
        color: #ffffff;
        background: rgba(255, 255, 255, 0.14);
        transform: translateY(-2px);
    }

    .developer-metrics {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 0.8rem;
    }

    .developer-metric {
        padding: 1rem 1rem 0.95rem;
        border-radius: 22px;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(10px);
    }

    .developer-metric strong {
        display: block;
        color: #ffffff;
        font-size: 1rem;
        font-weight: 800;
    }

    .developer-metric span {
        display: block;
        margin-top: 0.28rem;
        color: rgba(255, 255, 255, 0.68);
        font-size: 0.84rem;
    }

    .developer-portrait-column {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100%;
    }

    .developer-portrait-shell {
        position: relative;
        width: min(100%, 450px);
        min-height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1.5rem 1.1rem 2.85rem;
        overflow: visible;
    }

    .developer-orbit {
        position: absolute;
        display: inline-flex;
        align-items: center;
        gap: 0.55rem;
        padding: 0.7rem 0.95rem;
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.9);
        border: 1px solid rgba(255, 255, 255, 0.7);
        color: var(--developer-ink);
        font-size: 0.82rem;
        font-weight: 700;
        box-shadow: 0 18px 40px -24px rgba(8, 24, 48, 0.55);
        animation: developerFloat 5s ease-in-out infinite;
        z-index: 3;
        max-width: calc(100% - 0.75rem);
        white-space: nowrap;
    }

    .developer-orbit i {
        color: var(--developer-gold);
    }

    .developer-orbit-a {
        top: 0.75rem;
        left: 0.35rem;
    }

    .developer-orbit-b {
        top: 2rem;
        right: 0.2rem;
        animation-delay: 1.2s;
    }

    .developer-orbit-c {
        bottom: 0.9rem;
        left: 0.6rem;
        animation-delay: 2.3s;
    }

    .developer-photo-card {
        position: relative;
        width: min(100%, 300px);
        max-width: 100%;
        border-radius: 34px;
        padding: 0.7rem;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.32), rgba(255, 255, 255, 0.08));
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 36px 48px -28px rgba(7, 17, 33, 0.72);
        z-index: 2;
    }

    .developer-photo-card::before {
        content: "";
        position: absolute;
        inset: auto -1.5rem -1.6rem auto;
        width: 8rem;
        height: 8rem;
        border-radius: 28px;
        background: linear-gradient(135deg, rgba(198, 151, 73, 0.96), rgba(198, 151, 73, 0.18));
        filter: blur(2px);
        z-index: -1;
    }

    .developer-photo {
        width: 100%;
        aspect-ratio: 4 / 5;
        object-fit: cover;
        object-position: center top;
        border-radius: 28px;
        display: block;
    }

    .developer-visual-note {
        position: absolute;
        right: 1.3rem;
        bottom: 1.1rem;
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.85rem 1rem;
        border-radius: 20px;
        background: rgba(6, 26, 54, 0.82);
        color: #ffffff;
        box-shadow: 0 16px 32px -24px rgba(8, 24, 48, 0.92);
        backdrop-filter: blur(10px);
        z-index: 4;
        max-width: calc(100% - 2.6rem);
    }

    .developer-visual-note-icon {
        width: 40px;
        height: 40px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 14px;
        background: rgba(246, 210, 143, 0.16);
        color: #f6d28f;
        flex-shrink: 0;
    }

    .developer-visual-note strong {
        display: block;
        font-size: 0.95rem;
        color: #f6d28f;
    }

    .developer-visual-note span {
        display: block;
        color: rgba(255, 255, 255, 0.68);
        font-size: 0.78rem;
    }

    .developer-visual-note .developer-visual-note-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-top: 0;
        color: #f6d28f;
        font-size: 1rem;
        line-height: 1;
    }

    .developer-grid {
        display: grid;
        grid-template-columns: repeat(12, minmax(0, 1fr));
        gap: 1rem;
    }

    .developer-panel {
        position: relative;
        overflow: hidden;
        padding: 1.35rem;
        border-radius: 28px;
        background: var(--developer-panel);
        border: 1px solid rgba(255, 255, 255, 0.8);
        box-shadow: 0 28px 42px -34px rgba(15, 44, 89, 0.45);
        animation: developerRise 0.55s ease-out both;
    }

    .developer-panel:nth-child(2) {
        animation-delay: 0.08s;
    }

    .developer-panel:nth-child(3) {
        animation-delay: 0.16s;
    }

    .developer-panel:nth-child(4) {
        animation-delay: 0.24s;
    }

    .developer-panel-label {
        display: inline-flex;
        align-items: center;
        gap: 0.55rem;
        margin-bottom: 1rem;
        color: #64748b;
        font-size: 0.75rem;
        font-weight: 800;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }

    .developer-panel-title {
        margin: 0 0 1rem;
        color: var(--developer-ink);
        font-family: 'Space Grotesk', sans-serif;
        font-size: 1.6rem;
        letter-spacing: -0.04em;
    }

    .developer-panel-subtitle {
        margin: -0.45rem 0 1.2rem;
        color: #64748b;
        line-height: 1.7;
    }

    .developer-panel-primary {
        grid-column: span 7;
        background:
            radial-gradient(circle at top right, rgba(219, 234, 254, 0.85), transparent 42%),
            linear-gradient(180deg, rgba(255, 255, 255, 0.96), rgba(247, 250, 255, 0.92));
    }

    .developer-panel-secondary {
        grid-column: span 5;
    }

    .developer-panel-contact {
        grid-column: span 5;
        background:
            linear-gradient(135deg, rgba(198, 151, 73, 0.14), rgba(255, 255, 255, 0.94)),
            #ffffff;
    }

    .developer-panel-principles {
        grid-column: span 7;
    }

    .developer-feature-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 0.9rem;
    }

    .developer-feature-card {
        padding: 1rem;
        border-radius: 22px;
        background: rgba(255, 255, 255, 0.88);
        border: 1px solid var(--developer-line);
        min-height: 100%;
    }

    .developer-feature-icon {
        width: 48px;
        height: 48px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 16px;
        margin-bottom: 0.95rem;
        background: linear-gradient(135deg, rgba(15, 44, 89, 0.08), rgba(198, 151, 73, 0.18));
        color: var(--developer-navy);
        font-size: 1.1rem;
    }

    .developer-feature-card strong {
        display: block;
        color: var(--developer-ink);
        font-size: 1rem;
        font-weight: 800;
    }

    .developer-feature-card span {
        display: block;
        margin-top: 0.35rem;
        color: #64748b;
        font-size: 0.9rem;
        line-height: 1.65;
    }

    .developer-feature-card .developer-feature-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-top: 0;
        color: var(--developer-navy);
        font-size: 1.1rem;
        line-height: 1;
    }

    .developer-stack-cloud {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .developer-stack-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.55rem;
        min-height: 44px;
        padding: 0.7rem 0.95rem;
        border-radius: 999px;
        background: #ffffff;
        border: 1px solid rgba(148, 163, 184, 0.22);
        color: #1e293b;
        font-size: 0.92rem;
        font-weight: 700;
        box-shadow: 0 14px 24px -24px rgba(8, 24, 48, 0.5);
    }

    .developer-stack-pill i {
        color: var(--developer-gold);
    }

    .developer-principles {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 0.85rem;
    }

    .developer-principle {
        display: flex;
        gap: 0.9rem;
        align-items: flex-start;
        padding: 1rem;
        border-radius: 22px;
        background: #ffffff;
        border: 1px solid var(--developer-line);
    }

    .developer-principle-index {
        width: 42px;
        height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 14px;
        background: var(--developer-mist);
        color: var(--developer-navy);
        font-family: 'Space Grotesk', sans-serif;
        font-size: 1rem;
        font-weight: 700;
        flex-shrink: 0;
    }

    .developer-principle strong {
        display: block;
        color: var(--developer-ink);
        font-size: 0.98rem;
        font-weight: 800;
    }

    .developer-principle span {
        display: block;
        margin-top: 0.22rem;
        color: #64748b;
        font-size: 0.9rem;
        line-height: 1.65;
    }

    .developer-principle .developer-principle-index {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-top: 0;
        color: var(--developer-navy);
        font-size: 1rem;
        line-height: 1;
    }

    .developer-contact-list {
        display: grid;
        gap: 0.8rem;
    }

    .developer-contact-item {
        display: flex;
        align-items: center;
        gap: 0.85rem;
        min-height: 62px;
        padding: 0.95rem 1rem;
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.82);
        border: 1px solid rgba(198, 151, 73, 0.16);
        color: #0f172a;
        text-decoration: none !important;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .developer-contact-item:hover {
        color: #0f172a;
        transform: translateY(-2px);
        box-shadow: 0 20px 30px -28px rgba(8, 24, 48, 0.65);
    }

    .developer-contact-icon {
        width: 46px;
        height: 46px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 16px;
        background: rgba(198, 151, 73, 0.14);
        color: #9a7236;
        font-size: 1rem;
        flex-shrink: 0;
    }

    .developer-contact-copy strong {
        display: block;
        font-size: 0.98rem;
        font-weight: 800;
        color: #0f172a;
    }

    .developer-contact-copy span {
        display: block;
        margin-top: 0.18rem;
        color: #64748b;
        font-size: 0.85rem;
    }

    .developer-availability {
        margin-top: 1rem;
        padding: 0.95rem 1rem;
        border-radius: 20px;
        background: rgba(10, 35, 71, 0.06);
        color: #334155;
        font-size: 0.92rem;
        line-height: 1.7;
    }

    .developer-availability strong {
        color: #0f172a;
    }

    @media (max-width: 1199.98px) {
        .developer-hero {
            grid-template-columns: 1fr;
        }

        .developer-portrait-column {
            min-height: 500px;
        }

        .developer-portrait-shell {
            padding-bottom: 3.1rem;
        }

        .developer-panel-primary,
        .developer-panel-secondary,
        .developer-panel-contact,
        .developer-panel-principles {
            grid-column: span 12;
        }
    }

    @media (max-width: 767.98px) {
        .developer-stage {
            padding: 0;
        }

        .developer-hero {
            padding: 1.15rem;
            border-radius: 28px;
        }

        .developer-copy {
            gap: 1.2rem;
        }

        .developer-lead {
            font-size: 0.96rem;
        }

        .developer-actions,
        .developer-metrics,
        .developer-feature-grid,
        .developer-principles {
            grid-template-columns: 1fr;
        }

        .developer-actions {
            flex-direction: column;
        }

        .developer-contact-direct,
        .developer-button {
            width: 100%;
        }

        .developer-portrait-column {
            min-height: 430px;
        }

        .developer-portrait-shell {
            width: min(100%, 330px);
            padding: 0.85rem 0.2rem 2.9rem;
        }

        .developer-orbit {
            font-size: 0.74rem;
            padding: 0.62rem 0.8rem;
            max-width: calc(100% - 0.25rem);
        }

        .developer-orbit-a {
            top: 0;
            left: 0.1rem;
        }

        .developer-orbit-b {
            top: 2.15rem;
            right: 0.1rem;
        }

        .developer-orbit-c {
            bottom: 0.55rem;
            left: 0.1rem;
        }

        .developer-photo-card {
            width: min(100%, 270px);
            margin-top: 0.95rem;
        }

        .developer-visual-note {
            right: 0.8rem;
            left: 0.8rem;
            bottom: 0.8rem;
        }

        .developer-panel {
            padding: 1.1rem;
            border-radius: 24px;
        }

        .developer-panel-title {
            font-size: 1.35rem;
        }
    }
</style>
@endpush

@section('content')
<div class="developer-stage">
    <div class="developer-shell">
        <section class="developer-hero">
            <div class="developer-copy">
                <div>
                    <div class="developer-topline">
                        <span class="developer-kicker">
                            <i class="fe fe-code"></i>
                            Behind SISKALA
                        </span>
                        <span class="developer-status">
                            <span class="developer-status-dot"></span>
                            Design + Build
                        </span>
                    </div>
                    <br>
                    <div class="developer-title-wrap">
                        <h1 class="developer-name">
                            Faisal
                            <span class="developer-title-accent">Developer yang merancang dan membuat aplikasi SISKALA.</span>
                        </h1>
                        <p class="developer-lead">
                            SISKALA dirancang untuk membantu pengelolaan jadwal kegiatan, rapat, dan monitoring tindak lanjut di lingkungan Dewan Jaminan Sosial Nasional (DJSN).
                        </p>
                    </div>
                </div>

                <div class="developer-chip-row">
                    <span class="developer-chip"><i class="fe fe-monitor"></i> UI bersih</span>
                    <span class="developer-chip"><i class="fe fe-zap"></i> Workflow ringan</span>
                    <span class="developer-chip"><i class="fe fe-link"></i> Integrasi rapi</span>
                </div>

                <div class="developer-actions">
                    <div class="developer-contact-direct" role="note" aria-label="Email developer Faisal">
                        <span class="developer-contact-direct-icon" aria-hidden="true">
                            <i class="fe fe-mail"></i>
                        </span>
                        <span class="developer-contact-direct-copy">
                            <span class="developer-contact-direct-label">Email Developer</span>
                            <span class="developer-contact-direct-value">faisfaisal09@gmail.com</span>
                        </span>
                    </div>
                    <a href="https://www.linkedin.com/in/fais-faisal/" target="_blank" rel="noopener" class="developer-button developer-button-secondary">
                        <i class="fe fe-external-link"></i>
                        LinkedIn
                    </a>
                </div>

                <div class="developer-metrics">
                    <div class="developer-metric">
                        <strong>Elegant UI</strong>
                        <span>Terlihat rapi, tetap ringan.</span>
                    </div>
                    <div class="developer-metric">
                        <strong>Fast Flow</strong>
                        <span>Sedikit klik, lebih cepat selesai.</span>
                    </div>
                    <div class="developer-metric">
                        <strong>Reliable</strong>
                        <span>Siap dipakai tim setiap hari.</span>
                    </div>
                </div>
            </div>

            <div class="developer-portrait-column">
                <div class="developer-portrait-shell">
                    <div class="developer-orbit developer-orbit-a">
                        <i class="fe fe-layers"></i>
                        Laravel 10
                    </div>
                    <div class="developer-orbit developer-orbit-b">
                        <i class="fe fe-zap"></i>
                        Livewire
                    </div>
                    <div class="developer-orbit developer-orbit-c">
                        <i class="fe fe-calendar"></i>
                        Google API
                    </div>

                    <div class="developer-photo-card">
                        <img src="{{ asset('assets/images/Faisal Certified.JPG') }}" alt="Faisal, Developer SISKALA" class="developer-photo">

                        <div class="developer-visual-note">
                            <span class="developer-visual-note-icon">
                                <i class="fe fe-award"></i>
                            </span>
                            <span>
                                <strong>Developer SISKALA</strong>
                                <span>Crafting polished digital workflows</span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
