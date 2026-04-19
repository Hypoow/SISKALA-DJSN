<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="SISKALA">
    <link rel="icon" href="{{ asset('images/logo.svg') }}">
    <title>@yield('title', 'SISKALA')</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('tinydash/css/feather.css') }}">
    <style>
        :root {
            --public-bg: #f8fafc;
            --public-ink: #0f172a;
            --public-muted: #64748b;
            --public-line: rgba(148, 163, 184, 0.22);
            --public-surface: rgba(255, 255, 255, 0.88);
            --public-primary: #0f2c59;
            --public-accent: #c69749;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
            color: var(--public-ink);
            background:
                radial-gradient(circle at top left, rgba(198, 151, 73, 0.12), transparent 26%),
                radial-gradient(circle at top right, rgba(15, 44, 89, 0.08), transparent 24%),
                var(--public-bg);
        }

        .public-layout {
            min-height: 100vh;
            padding: 1rem;
        }

        .public-header,
        .public-main {
            width: min(1280px, 100%);
            margin: 0 auto;
        }

        .public-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1rem;
            padding: 0.85rem 1rem;
            border-radius: 24px;
            background: var(--public-surface);
            border: 1px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 24px 40px -34px rgba(15, 44, 89, 0.36);
            backdrop-filter: blur(14px);
        }

        .public-brand {
            display: inline-flex;
            align-items: center;
            gap: 0.85rem;
            text-decoration: none;
            min-width: 0;
        }

        .public-brand-mark {
            width: 46px;
            height: 46px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 16px;
            background: linear-gradient(135deg, rgba(15, 44, 89, 0.08), rgba(198, 151, 73, 0.14));
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.8);
            flex-shrink: 0;
            overflow: hidden;
        }

        .public-brand-mark img {
            width: 24px;
            height: 24px;
            display: block;
            object-fit: contain;
        }

        .public-brand-copy {
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .public-brand-copy strong {
            color: var(--public-primary);
            font-size: 0.96rem;
            letter-spacing: 0.04em;
        }

        .public-brand-copy span {
            color: var(--public-muted);
            font-size: 0.8rem;
            margin-top: 0.18rem;
        }

        .public-header-actions {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .public-header-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.55rem;
            min-height: 44px;
            padding: 0.75rem 1rem;
            border-radius: 16px;
            text-decoration: none;
            font-weight: 600;
            color: var(--public-primary);
            background: #ffffff;
            border: 1px solid var(--public-line);
            box-shadow: 0 18px 28px -24px rgba(15, 44, 89, 0.4);
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        }

        .public-header-link:hover {
            transform: translateY(-1px);
            text-decoration: none;
            border-color: rgba(198, 151, 73, 0.4);
            box-shadow: 0 22px 32px -24px rgba(15, 44, 89, 0.45);
        }

        .public-main {
            padding-bottom: 1.2rem;
        }

        @media (max-width: 767.98px) {
            .public-layout {
                padding: 0.75rem;
            }

            .public-header {
                padding: 0.8rem 0.9rem;
                border-radius: 20px;
            }

            .public-brand-copy span {
                display: none;
            }

            .public-header-link {
                padding: 0.72rem 0.9rem;
            }

            .public-header-link span {
                display: none;
            }
        }
    </style>
    @stack('styles')
</head>

<body>
    <div class="public-layout">
        <header class="public-header">
            <a href="{{ route('developer') }}" class="public-brand" aria-label="Halaman developer SISKALA">
                <span class="public-brand-mark">
                    <img src="{{ asset('images/logo.svg') }}" alt="Logo SISKALA" width="24" height="24">
                </span>
                <span class="public-brand-copy">
                    <strong>SISKALA</strong>
                    <span>Informasi pengembang aplikasi</span>
                </span>
            </a>

            <div class="public-header-actions">
                <a href="{{ route('login') }}" class="public-header-link">
                    <i class="fe fe-log-in"></i>
                    <span>Masuk ke SISKALA</span>
                </a>
            </div>
        </header>

        <main class="public-main">
            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>

</html>