<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description"
        content="@yield('meta_description', 'SISKALA - Sistem Kelola Agenda, Tindak Lanjut, dan Administrasi Dewan Jaminan Sosial Nasional')">
    <link rel="icon" href="{{ asset('images/logo.svg') }}">
    <title>@yield('title', 'SISKALA')</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/feather.css') }}">
    <style>
        :root {
            --auth-accent: #2563eb;
            --auth-accent-strong: #1748cf;
            --auth-accent-soft: #eaf2ff;
            --auth-gold: #c69749;
            --auth-ink: #10213d;
            --auth-muted: #5f718f;
            --auth-surface: rgba(255, 255, 255, 0.94);
            --auth-surface-soft: rgba(255, 255, 255, 0.08);
            --auth-border: rgba(148, 163, 184, 0.22);
            --auth-field-border: #d8e2f3;
            --auth-field-bg: #f8fbff;
            --auth-brand-height: clamp(430px, 74vh, 720px);
        }

        * {
            box-sizing: border-box;
        }

        html {
            min-height: 100%;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--auth-ink);
            background:
                radial-gradient(circle at top left, rgba(37, 99, 235, 0.12), transparent 28%),
                radial-gradient(circle at bottom right, rgba(198, 151, 73, 0.09), transparent 24%),
                linear-gradient(180deg, #f5f8ff 0%, #eef3fb 42%, #f8fbff 100%);
            overflow-x: hidden;
            overflow-y: auto;
        }

        body.loading-active {
            overflow: hidden;
        }

        a {
            color: inherit;
        }

        button,
        input,
        textarea,
        select {
            font: inherit;
        }

        .auth-page {
            position: relative;
            min-height: 100vh;
            display: grid;
            grid-template-columns: minmax(0, 1.06fr) minmax(420px, 0.94fr);
            align-items: center;
        }

        .auth-brand-panel {
            padding: clamp(1rem, 2.8vw, 2.5rem) 0 1rem clamp(1rem, 2.8vw, 2.5rem);
            position: relative;
            display: flex;
            align-items: center;
        }

        .auth-brand-surface {
            position: relative;
            min-height: var(--auth-brand-height);
            width: 100%;
            padding: clamp(2rem, 4.5vw, 3.75rem);
            border-radius: 36px;
            overflow: hidden;
            background: linear-gradient(145deg, #0d1b3a 0%, #132656 55%, #1a2d73 100%);
            box-shadow: 0 30px 80px -48px rgba(11, 25, 53, 0.72);
            display: grid;
            align-content: center;
            gap: clamp(1.5rem, 3vw, 2rem);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .auth-slider-panel {
            position: relative;
            min-height: var(--auth-brand-height);
            height: var(--auth-brand-height);
            padding: 1.2rem;
            border-radius: 36px;
            overflow: hidden;
            background: linear-gradient(145deg, #081325 0%, #10213d 50%, #173067 100%);
            box-shadow: 0 30px 80px -48px rgba(11, 25, 53, 0.72);
            border: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            flex: 1;
            align-self: center;
        }

        .auth-slider-panel::before,
        .auth-slider-panel::after {
            content: '';
            position: absolute;
            border-radius: 999px;
            pointer-events: none;
        }

        .auth-slider-panel::before {
            width: 24rem;
            height: 24rem;
            top: -10rem;
            right: -8rem;
            background: radial-gradient(circle, rgba(125, 211, 252, 0.18) 0%, rgba(125, 211, 252, 0) 72%);
        }

        .auth-slider-panel::after {
            width: 26rem;
            height: 26rem;
            bottom: -13rem;
            left: -10rem;
            background: radial-gradient(circle, rgba(198, 151, 73, 0.16) 0%, rgba(198, 151, 73, 0) 72%);
        }

        .auth-slider-frame {
            position: relative;
            flex: 1;
            min-height: 0;
            height: 100%;
            border-radius: 28px;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.08);
        }

        .auth-slider-frame::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                linear-gradient(180deg, rgba(8, 19, 37, 0.1) 0%, rgba(8, 19, 37, 0.32) 100%),
                radial-gradient(circle at top, rgba(255, 255, 255, 0.08), transparent 40%);
            z-index: 1;
            pointer-events: none;
        }

        .auth-slider-slide {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: clamp(2rem, 4vw, 3.5rem);
            margin: 0;
            opacity: 0;
            transform: scale(1.04);
            transition: opacity 0.8s ease, transform 1.2s ease;
            z-index: 0;
        }

        .auth-slider-slide.is-active {
            opacity: 1;
            transform: scale(1);
            z-index: 1;
        }

        .auth-slider-media {
            position: relative;
            z-index: 2;
            width: min(100%, 780px);
            padding: clamp(0.4rem, 0.9vw, 0.8rem);
            border-radius: clamp(24px, 2.4vw, 34px);
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.14);
            box-shadow:
                0 24px 44px -28px rgba(8, 19, 37, 0.65),
                inset 0 1px 0 rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(10px);
            overflow: hidden;
        }

        .auth-slider-slide img {
            position: relative;
            z-index: 1;
            display: block;
            width: 100%;
            height: auto;
            max-width: 100%;
            max-height: min(100%, 520px);
            object-fit: contain;
            border-radius: clamp(20px, 2vw, 28px);
            background: #ffffff;
            filter: drop-shadow(0 22px 40px rgba(8, 19, 37, 0.35));
            user-select: none;
            -webkit-user-drag: none;
        }

        .auth-slider-meta {
            position: absolute;
            left: 1.2rem;
            right: 1.2rem;
            bottom: 1.2rem;
            z-index: 2;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .auth-slider-label {
            display: inline-flex;
            align-items: center;
            padding: 0.58rem 0.9rem;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.08);
            color: #f8fbff;
            border: 1px solid rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(16px);
            font-size: 0.74rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .auth-slider-dots {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.45rem 0.55rem;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(16px);
        }

        .auth-slider-dot {
            appearance: none;
            width: 0.62rem;
            height: 0.62rem;
            padding: 0;
            border: none;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.38);
            cursor: pointer;
            transition: width 0.25s ease, background-color 0.25s ease, transform 0.25s ease;
        }

        .auth-slider-dot.is-active {
            width: 1.65rem;
            background: #ffffff;
        }

        .auth-slider-dot:hover,
        .auth-slider-dot:focus-visible {
            transform: scale(1.05);
            outline: none;
        }

        .auth-brand-surface::before,
        .auth-brand-surface::after {
            content: '';
            position: absolute;
            inset: auto;
            border-radius: 999px;
            pointer-events: none;
        }

        .auth-brand-surface::before {
            width: 24rem;
            height: 24rem;
            top: -9rem;
            right: -7rem;
            background: radial-gradient(circle, rgba(125, 211, 252, 0.22) 0%, rgba(125, 211, 252, 0) 72%);
        }

        .auth-brand-surface::after {
            width: 28rem;
            height: 28rem;
            bottom: -14rem;
            left: -10rem;
            background: radial-gradient(circle, rgba(198, 151, 73, 0.16) 0%, rgba(198, 151, 73, 0) 72%);
        }

        .brand-header,
        .brand-pillars {
            position: relative;
            z-index: 1;
        }

        .brand-header {
            display: grid;
            gap: 0.55rem;
            max-width: 28rem;
        }

        .brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.65rem;
            padding: 0.48rem 0.82rem;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.1);
            color: #f8fbff;
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            border: 1px solid rgba(255, 255, 255, 0.1);
            width: fit-content;
        }

        .brand-badge::before {
            content: '';
            width: 0.55rem;
            height: 0.55rem;
            border-radius: 999px;
            background: #7dd3fc;
            box-shadow: 0 0 0 0.35rem rgba(125, 211, 252, 0.16);
        }

        .brand-mark {
            position: relative;
            width: 90px;
            height: 90px;
            display: grid;
            place-items: center;
            margin: 1rem 0 0.75rem;
            border-radius: 28px;
            background: linear-gradient(145deg, rgba(255, 255, 255, 0.18), rgba(255, 255, 255, 0.06));
            border: 1px solid rgba(255, 255, 255, 0.22);
            box-shadow:
                inset 0 1px 0 rgba(255, 255, 255, 0.18),
                0 18px 45px -30px rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(14px);
        }

        .brand-mark::after {
            content: '';
            position: absolute;
            inset: 8px;
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            background: radial-gradient(circle at top, rgba(255, 255, 255, 0.14), transparent 70%);
            pointer-events: none;
        }

        .brand-mark img {
            width: 56px;
            height: auto;
            filter: drop-shadow(0 8px 14px rgba(10, 25, 61, 0.32));
            position: relative;
            z-index: 1;
        }

        .brand-title {
            margin: 0;
            font-size: clamp(3rem, 4.6vw, 4.8rem);
            line-height: 0.94;
            letter-spacing: -0.05em;
            color: #ffffff;
        }

        .brand-copy {
            max-width: 24rem;
            margin: 0.35rem 0 0;
            color: rgba(226, 232, 240, 0.86);
            font-size: 0.98rem;
            line-height: 1.7;
        }

        .brand-pillars {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .brand-pillar {
            display: inline-flex;
            align-items: center;
            gap: 0.7rem;
            padding: 0.72rem 1rem;
            border-radius: 999px;
            background: var(--auth-surface-soft);
            border: 1px solid rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(16px);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.05);
            animation: rise-in 0.6s cubic-bezier(0.22, 1, 0.36, 1) both;
        }

        .brand-pillar:nth-child(2) {
            animation-delay: 0.08s;
        }

        .brand-pillar:nth-child(3) {
            animation-delay: 0.16s;
        }

        .brand-pillar-icon {
            width: 2.5rem;
            height: 2.5rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 14px;
            flex-shrink: 0;
            background: linear-gradient(145deg, rgba(255, 255, 255, 0.2), rgba(255, 255, 255, 0.08));
            color: #f8fbff;
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.08);
        }

        .brand-pillar-icon i,
        .helper-icon i,
        .password-toggle i {
            display: block;
            line-height: 1;
        }

        .brand-pillar-icon i {
            font-size: 0.95rem;
        }

        .brand-pillar strong {
            display: block;
            margin: 0;
            font-size: 0.88rem;
            line-height: 1.2;
            color: #ffffff;
        }

        .brand-pillar span {
            display: none;
        }

        .auth-content-panel {
            padding: clamp(1rem, 3vw, 2.5rem);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            z-index: 1;
        }

        .auth-content-shell {
            width: min(100%, 520px);
            display: grid;
            gap: 1rem;
        }

        .auth-card {
            background: var(--auth-surface);
            border: 1px solid var(--auth-border);
            border-radius: 32px;
            padding: clamp(1.4rem, 3vw, 2.3rem);
            box-shadow: 0 28px 70px -44px rgba(15, 23, 42, 0.35);
            backdrop-filter: blur(18px);
            animation: rise-in 0.6s cubic-bezier(0.22, 1, 0.36, 1) both;
        }

        .auth-topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.9rem;
            margin-bottom: 0.8rem;
        }

        .panel-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.55rem;
            padding: 0.48rem 0.85rem;
            border-radius: 999px;
            background: var(--auth-accent-soft);
            color: var(--auth-accent);
            font-size: 0.76rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .panel-badge::before {
            content: '';
            width: 0.48rem;
            height: 0.48rem;
            border-radius: 999px;
            background: currentColor;
            opacity: 0.75;
        }

        .mini-brand {
            display: inline-flex;
            align-items: center;
            gap: 0.55rem;
            padding: 0.45rem 0.7rem;
            border-radius: 999px;
            background: #f8fbff;
            border: 1px solid rgba(148, 163, 184, 0.24);
            color: var(--auth-muted);
            font-size: 0.76rem;
            font-weight: 700;
        }

        .mini-brand img {
            width: 20px;
            height: 20px;
        }

        .section-title {
            margin: 0;
            font-size: clamp(2rem, 3vw, 2.75rem);
            line-height: 1.05;
            letter-spacing: -0.035em;
            font-weight: 800;
            color: var(--auth-ink);
        }

        .section-subtitle {
            margin: 0.65rem 0 1.5rem;
            color: var(--auth-muted);
            font-size: 0.97rem;
            line-height: 1.6;
            max-width: 24rem;
        }

        .alert {
            display: flex;
            gap: 0.85rem;
            align-items: flex-start;
            padding: 1rem 1.1rem;
            border-radius: 20px;
            margin-bottom: 1.35rem;
            border: 1px solid transparent;
        }

        .alert i {
            flex-shrink: 0;
            margin-top: 0.1rem;
        }

        .alert strong {
            display: block;
            margin-bottom: 0.25rem;
            font-size: 0.92rem;
        }

        .alert ul {
            margin: 0.35rem 0 0 1rem;
            padding: 0;
        }

        .alert li {
            margin-bottom: 0.2rem;
        }

        .alert p {
            margin: 0;
        }

        .alert-danger {
            background: #fef2f2;
            color: #991b1b;
            border-color: #fecaca;
        }

        .alert-danger i {
            color: #dc2626;
        }

        .alert-success {
            background: #ecfdf5;
            color: #065f46;
            border-color: #a7f3d0;
        }

        .alert-success i {
            color: #10b981;
        }

        .auth-form {
            display: grid;
            gap: 1.35rem;
        }

        .form-stack {
            display: grid;
            gap: 1.3rem;
        }

        .form-group {
            display: grid;
            gap: 0.72rem;
        }

        .form-label {
            font-size: 0.92rem;
            font-weight: 700;
            color: var(--auth-ink);
            line-height: 1.35;
        }

        .form-hint {
            margin: -0.05rem 0 0;
            font-size: 0.82rem;
            color: var(--auth-muted);
            line-height: 1.6;
        }

        .input-shell {
            position: relative;
        }

        .input-shell.has-toggle .form-control {
            padding-right: 3.15rem;
        }

        .input-icon {
            position: absolute;
            left: 0.85rem;
            top: 50%;
            transform: translateY(-50%);
            width: 1.8rem;
            height: 1.8rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            background: #edf4ff;
            color: #355685;
            font-size: 0.9rem;
            pointer-events: none;
            transition: color 0.2s ease, background-color 0.2s ease;
        }

        .input-shell:focus-within .input-icon {
            color: var(--auth-accent);
            background: #e1eeff;
        }

        .form-control {
            width: 100%;
            min-height: 52px;
            padding: 0.82rem 1rem 0.82rem 3.25rem;
            border: 1px solid var(--auth-field-border);
            border-radius: 16px;
            background: var(--auth-field-bg);
            color: var(--auth-ink);
            transition: border-color 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
        }

        .form-control::placeholder {
            color: #94a3b8;
        }

        .form-control:hover {
            border-color: #c3d4f0;
        }

        .form-control:focus {
            outline: none;
            border-color: rgba(37, 99, 235, 0.65);
            background: #ffffff;
            box-shadow: 0 0 0 5px rgba(37, 99, 235, 0.12);
        }

        .password-toggle {
            position: absolute;
            right: 0.7rem;
            top: 50%;
            transform: translateY(-50%);
            width: 36px;
            height: 36px;
            border: none;
            border-radius: 12px;
            background: transparent;
            color: #6b7d9c;
            display: grid;
            place-items: center;
            cursor: pointer;
            transition: background-color 0.2s ease, color 0.2s ease;
        }

        .password-toggle:hover,
        .password-toggle:focus-visible {
            background: var(--auth-accent-soft);
            color: var(--auth-accent);
            outline: none;
        }

        .form-meta {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 0.8rem;
        }

        .inline-check {
            display: inline-flex;
            align-items: center;
            gap: 0.7rem;
            font-size: 0.93rem;
            color: var(--auth-muted);
            line-height: 1.5;
        }

        .inline-check input {
            appearance: none;
            width: 1.15rem;
            height: 1.15rem;
            margin: 0;
            border: 1px solid #c7d2fe;
            border-radius: 6px;
            background: #ffffff;
            display: grid;
            place-items: center;
            cursor: pointer;
        }

        .inline-check input::before {
            content: '';
            width: 0.36rem;
            height: 0.72rem;
            border: solid #ffffff;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg) scale(0);
            transform-origin: center;
            transition: transform 0.2s ease;
        }

        .inline-check input:checked {
            background: var(--auth-accent);
            border-color: var(--auth-accent);
        }

        .inline-check input:checked::before {
            transform: rotate(45deg) scale(1);
        }

        .inline-link,
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            color: var(--auth-accent);
            font-size: 0.93rem;
            font-weight: 700;
            text-decoration: none;
        }

        .inline-link:hover,
        .back-link:hover {
            color: var(--auth-accent-strong);
            text-decoration: underline;
        }

        .primary-button {
            width: 100%;
            min-height: 60px;
            border: none;
            border-radius: 20px;
            background: linear-gradient(135deg, var(--auth-accent) 0%, var(--auth-accent-strong) 100%);
            color: #ffffff;
            font-size: 1rem;
            font-weight: 800;
            letter-spacing: -0.01em;
            cursor: pointer;
            box-shadow: 0 20px 36px -24px rgba(37, 99, 235, 0.78);
            transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease;
        }

        .primary-button:hover {
            transform: translateY(-1px);
            box-shadow: 0 24px 36px -20px rgba(37, 99, 235, 0.55);
            filter: saturate(1.05);
        }

        .primary-button:active {
            transform: translateY(0);
        }

        .subtle-note {
            margin: 0.1rem 0 0;
            font-size: 0.88rem;
            color: #64748b;
            line-height: 1.65;
        }

        .helper-card {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            padding: 0.95rem 1rem;
            text-decoration: none;
            background: rgba(255, 255, 255, 0.92);
            border: 1px solid rgba(148, 163, 184, 0.2);
            border-radius: 20px;
            box-shadow: 0 20px 38px -32px rgba(15, 23, 42, 0.24);
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
            animation: rise-in 0.6s cubic-bezier(0.22, 1, 0.36, 1) both;
            animation-delay: 0.1s;
        }

        .helper-card:hover {
            transform: translateY(-1px);
            border-color: rgba(37, 99, 235, 0.22);
            box-shadow: 0 28px 38px -28px rgba(37, 99, 235, 0.28);
        }

        .helper-icon {
            width: 2.65rem;
            height: 2.65rem;
            border-radius: 16px;
            background: var(--auth-accent-soft);
            color: var(--auth-accent);
            display: grid;
            place-items: center;
            flex-shrink: 0;
            font-size: 1rem;
        }

        .helper-copy {
            min-width: 0;
        }

        .helper-label,
        .helper-title,
        .helper-caption {
            display: block;
        }

        .helper-label {
            margin-bottom: 0.14rem;
            font-size: 0.72rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            font-weight: 800;
            color: #94a3b8;
        }

        .helper-title {
            font-weight: 800;
            font-size: 0.95rem;
            color: var(--auth-ink);
        }

        .helper-caption {
            display: none;
        }

        .helper-arrow {
            margin-left: auto;
            color: #94a3b8;
            font-size: 1rem;
        }

        .footer-note {
            margin: 0;
            text-align: center;
            font-size: 0.82rem;
            color: #94a3b8;
        }

        .error-message {
            display: block;
            margin-top: 0.1rem;
            font-size: 0.82rem;
            color: #dc2626;
            font-weight: 600;
        }

        .loader-wrapper {
            position: fixed;
            inset: 0;
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(244, 247, 255, 0.78);
            backdrop-filter: blur(14px);
            transition: opacity 0.35s ease, visibility 0.35s ease;
        }

        .loader-wrapper.is-hidden {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }

        .loader-core {
            position: relative;
            width: 120px;
            height: 120px;
            display: grid;
            place-items: center;
        }

        .loader-ring,
        .loader-ring::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 999px;
            border: 3px solid transparent;
        }

        .loader-ring {
            border-top-color: var(--auth-accent);
            animation: spin 1s linear infinite;
        }

        .loader-ring::before {
            inset: 12px;
            border-top-color: var(--auth-gold);
            animation: spin 1.6s linear infinite reverse;
        }

        .loader-core img {
            width: 52px;
            height: auto;
            z-index: 1;
        }

        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        @keyframes rise-in {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: none;
            }
        }

        @media (max-width: 1180px) {
            .auth-page {
                grid-template-columns: 1fr;
            }

            .auth-brand-panel {
                display: none;
            }

            .auth-content-panel {
                min-height: 100vh;
                padding: max(1rem, 3vh) 1rem;
            }

            .auth-content-shell {
                width: min(100%, 520px);
            }
        }

        @media (max-width: 720px) {
            .auth-page {
                min-height: 100svh;
                min-height: 100dvh;
            }

            .auth-content-panel {
                min-height: 100svh;
                min-height: 100dvh;
                align-items: center;
                padding:
                    max(0.9rem, env(safe-area-inset-top))
                    0.75rem
                    max(1rem, env(safe-area-inset-bottom));
            }

            .auth-content-shell {
                width: min(100%, 420px);
            }

            .auth-card {
                border-radius: 26px;
                padding: 1.25rem 1.05rem 1.1rem;
            }

            .auth-topbar {
                flex-direction: column;
                align-items: flex-start;
            }

            .section-title {
                font-size: 2rem;
            }

            .section-subtitle {
                font-size: 0.95rem;
                line-height: 1.6;
                margin-bottom: 1.35rem;
            }

            .form-control {
                min-height: 50px;
                border-radius: 15px;
            }

            .primary-button {
                min-height: 56px;
                border-radius: 18px;
            }

            .form-meta {
                align-items: flex-start;
            }

            .helper-card {
                border-radius: 20px;
                padding: 1rem;
            }

            .helper-title {
                font-size: 0.95rem;
            }
        }

        @media (max-width: 420px) {
            .section-title {
                font-size: 1.78rem;
            }

            .mini-brand {
                display: none;
            }
        }

        @media (prefers-reduced-motion: reduce) {

            *,
            *::before,
            *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
                scroll-behavior: auto !important;
            }
        }
    </style>
    @stack('styles')
</head>

<body class="@yield('body_class')">
    @yield('overlay')

    <div class="auth-page">
        <aside class="auth-brand-panel" aria-label="Informasi SISKALA">
            @hasSection('brand_panel')
                @yield('brand_panel')
            @else
                <div class="auth-brand-surface">
                    <div class="brand-header">
                        <span class="brand-badge">SISKALA</span>
                        <div class="brand-mark">
                            <img src="{{ asset('images/logo.svg') }}" alt="Logo DJSN">
                        </div>
                        <h1 class="brand-title">SISKALA</h1>
                        <p class="brand-copy">
                            Agenda dan administrasi DJSN.
                        </p>
                    </div>

                    <div class="brand-pillars">
                        <article class="brand-pillar">
                            <span class="brand-pillar-icon" aria-hidden="true">
                                <i class="fe fe-calendar"></i>
                            </span>
                            <div>
                                <strong>Agenda</strong>
                            </div>
                        </article>

                        <article class="brand-pillar">
                            <span class="brand-pillar-icon" aria-hidden="true">
                                <i class="fe fe-check-circle"></i>
                            </span>
                            <div>
                                <strong>Tindak Lanjut</strong>
                            </div>
                        </article>

                        <article class="brand-pillar">
                            <span class="brand-pillar-icon" aria-hidden="true">
                                <i class="fe fe-monitor"></i>
                            </span>
                            <div>
                                <strong>Responsif</strong>
                            </div>
                        </article>
                    </div>
                </div>
            @endif
        </aside>

        <main class="auth-content-panel">
            <div class="auth-content-shell">
                <section class="auth-card">
                    <div class="auth-topbar">
                        <span class="panel-badge">@yield('eyebrow', 'Akses Aman')</span>
                        <span class="mini-brand">
                            <img src="{{ asset('images/logo.svg') }}" alt="">
                            DJSN
                        </span>
                    </div>

                    <h2 class="section-title">@yield('heading', 'Akses SISKALA')</h2>
                    <p class="section-subtitle">
                        @yield('subheading', 'Gunakan akun yang terdaftar untuk melanjutkan ke layanan SISKALA.')
                    </p>

                    @yield('alerts')
                    @yield('content')
                </section>

                @if (trim($__env->yieldContent('after_form')))
                    @yield('after_form')
                @endif

                <a href="{{ route('developer') }}" class="helper-card">
                    <span class="helper-icon" aria-hidden="true">
                        <i class="fe fe-user"></i>
                    </span>
                    <span class="helper-copy">
                        <span class="helper-label">Informasi</span>
                        <span class="helper-title">Developer SISKALA</span>
                        <span class="sr-only">Lihat halaman developer SISKALA</span>
                    </span>
                    <span class="helper-arrow" aria-hidden="true">
                        <i class="fe fe-arrow-right"></i>
                    </span>
                </a>

                <p class="footer-note">&copy; {{ date('Y') }} Dewan Jaminan Sosial Nasional</p>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('[data-password-toggle]').forEach(function (button) {
                button.addEventListener('click', function () {
                    const target = document.getElementById(button.dataset.passwordToggle);
                    if (!target) {
                        return;
                    }

                    const revealPassword = target.getAttribute('type') === 'password';
                    target.setAttribute('type', revealPassword ? 'text' : 'password');
                    button.setAttribute('aria-pressed', revealPassword ? 'true' : 'false');

                    const icon = button.querySelector('i');
                    if (icon) {
                        icon.className = revealPassword ? 'fe fe-eye-off' : 'fe fe-eye';
                    }

                    const label = button.querySelector('.sr-only');
                    if (label) {
                        label.textContent = revealPassword ? 'Sembunyikan password' : 'Tampilkan password';
                    }
                });
            });

            document.querySelectorAll('[data-auth-slider]').forEach(function (slider) {
                const slides = Array.from(slider.querySelectorAll('[data-slider-slide]'));
                const dots = Array.from(slider.querySelectorAll('[data-slider-dot]'));
                const activeLabel = slider.querySelector('[data-slider-label]');
                const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
                const delay = Number(slider.dataset.sliderDelay || 5000);
                let currentIndex = Math.max(slides.findIndex(function (slide) {
                    return slide.classList.contains('is-active');
                }), 0);
                let timerId = null;

                if (!slides.length) {
                    return;
                }

                const setActiveSlide = function (nextIndex) {
                    currentIndex = nextIndex;

                    slides.forEach(function (slide, slideIndex) {
                        const isActive = slideIndex === nextIndex;
                        slide.classList.toggle('is-active', isActive);
                        slide.setAttribute('aria-hidden', isActive ? 'false' : 'true');
                    });

                    dots.forEach(function (dot, dotIndex) {
                        const isActive = dotIndex === nextIndex;
                        dot.classList.toggle('is-active', isActive);
                        dot.setAttribute('aria-selected', isActive ? 'true' : 'false');
                    });

                    if (activeLabel) {
                        activeLabel.textContent = slides[nextIndex].dataset.label || '';
                    }
                };

                const stopSlider = function () {
                    if (timerId !== null) {
                        window.clearInterval(timerId);
                        timerId = null;
                    }
                };

                const startSlider = function () {
                    if (reduceMotion || slides.length < 2) {
                        return;
                    }

                    stopSlider();
                    timerId = window.setInterval(function () {
                        setActiveSlide((currentIndex + 1) % slides.length);
                    }, delay);
                };

                dots.forEach(function (dot) {
                    dot.addEventListener('click', function () {
                        setActiveSlide(Number(dot.dataset.slideIndex || 0));
                        startSlider();
                    });
                });

                slider.addEventListener('mouseenter', stopSlider);
                slider.addEventListener('mouseleave', startSlider);
                slider.addEventListener('focusin', stopSlider);
                slider.addEventListener('focusout', startSlider);

                document.addEventListener('visibilitychange', function () {
                    if (document.hidden) {
                        stopSlider();
                    } else {
                        startSlider();
                    }
                });

                setActiveSlide(currentIndex);
                startSlider();
            });

            window.addEventListener('pageshow', function () {
                document.body.classList.remove('loading-active');
                document.querySelectorAll('.loader-wrapper').forEach(function (loader) {
                    loader.classList.add('is-hidden');
                });
            });
        });
    </script>
    @stack('scripts')
</body>

</html>
