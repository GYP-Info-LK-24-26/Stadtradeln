<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Stadtradeln - Gemeinsam radeln für das Klima</title>
    <link rel="stylesheet" href="/css/main.css">
    <style>
        /* Hero Section */
        .hero {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: var(--space-xl);
            position: relative;
            overflow: hidden;
        }

        /* Forest background layers */
        .hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                /* Dappled sunlight effect */
                radial-gradient(ellipse at 30% 20%, rgba(233, 180, 76, 0.15) 0%, transparent 40%),
                radial-gradient(ellipse at 70% 60%, rgba(82, 183, 136, 0.1) 0%, transparent 50%),
                radial-gradient(ellipse at 20% 80%, rgba(116, 198, 157, 0.08) 0%, transparent 40%),
                /* Base gradient */
                linear-gradient(
                    180deg,
                    light-dark(var(--cream-light), var(--night-deep)) 0%,
                    light-dark(var(--cream-mid), var(--night-forest)) 100%
                );
            z-index: -2;
        }

        /* Decorative forest silhouette */
        .hero::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 200px;
            background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1440 200' preserveAspectRatio='none'%3E%3Cpath fill='%2340916C' fill-opacity='0.1' d='M0,200 L0,120 Q60,80 120,110 T240,90 T360,100 T480,70 T600,95 T720,75 T840,105 T960,80 T1080,100 T1200,85 T1320,95 T1440,80 L1440,200 Z'/%3E%3Cpath fill='%2352B788' fill-opacity='0.08' d='M0,200 L0,140 Q80,110 160,130 T320,100 T480,120 T640,90 T800,115 T960,95 T1120,110 T1280,100 T1440,120 L1440,200 Z'/%3E%3C/svg%3E") no-repeat bottom center;
            background-size: cover;
            z-index: -1;
        }

        .hero-content {
            max-width: 700px;
            animation: fadeInUp 0.8s ease;
        }

        /* Logo/Icon */
        .hero-icon {
            width: 100px;
            height: 100px;
            margin: 0 auto var(--space-xl);
            background: linear-gradient(135deg, var(--forest-light) 0%, var(--mint-fresh) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow:
                0 4px 20px rgba(64, 145, 108, 0.3),
                inset 0 2px 10px rgba(255, 255, 255, 0.2);
            animation: gentleFloat 4s ease-in-out infinite;
        }

        @keyframes gentleFloat {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .hero-icon svg {
            width: 50px;
            height: 50px;
            fill: white;
        }

        .hero h1 {
            font-size: clamp(2.5rem, 7vw, 4rem);
            margin-bottom: var(--space-md);
            color: light-dark(var(--forest-deep), var(--mint-soft));
            letter-spacing: -0.02em;
        }

        .hero h1 span {
            display: block;
            font-size: 0.5em;
            font-weight: 400;
            color: var(--color-text-muted);
            margin-top: var(--space-xs);
        }

        .hero-subtitle {
            font-size: clamp(1.1rem, 2.5vw, 1.35rem);
            color: var(--color-text-muted);
            margin-bottom: var(--space-2xl);
            line-height: 1.6;
        }

        .hero-actions {
            display: flex;
            gap: var(--space-md);
            justify-content: center;
            flex-wrap: wrap;
        }

        .hero-actions .btn {
            padding: var(--space-md) var(--space-xl);
            font-size: 1.1rem;
        }

        /* Features Section */
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: var(--space-lg);
            margin-top: var(--space-2xl);
            width: 100%;
            max-width: 900px;
        }

        .feature-card {
            background: var(--color-bg-card);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-lg);
            padding: var(--space-xl);
            text-align: left;
            transition: all var(--transition-normal);
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(180deg, var(--mint-fresh), var(--forest-light));
            opacity: 0;
            transition: opacity var(--transition-fast);
        }

        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-medium);
        }

        .feature-card:hover::before {
            opacity: 1;
        }

        .feature-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, var(--mint-pale) 0%, var(--mint-soft) 100%);
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: var(--space-md);
        }

        .feature-icon svg {
            width: 24px;
            height: 24px;
            fill: var(--forest-deep);
        }

        .feature-card h3 {
            font-size: 1.1rem;
            margin-bottom: var(--space-sm);
            color: light-dark(var(--forest-deep), var(--mint-soft));
        }

        .feature-card p {
            font-size: 0.9rem;
            color: var(--color-text-muted);
            margin: 0;
            line-height: 1.5;
        }

        /* Footer */
        .home-footer {
            margin-top: var(--space-2xl);
            padding-top: var(--space-xl);
            border-top: 1px solid var(--color-border);
            font-size: 0.85rem;
            color: var(--color-text-muted);
        }

        /* Staggered animation for features */
        .feature-card {
            opacity: 0;
            animation: fadeInUp 0.5s ease forwards;
        }

        .feature-card:nth-child(1) { animation-delay: 0.2s; }
        .feature-card:nth-child(2) { animation-delay: 0.35s; }
        .feature-card:nth-child(3) { animation-delay: 0.5s; }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <section class="hero">
        <div class="hero-content">
            <div class="hero-icon">
                <!-- Bicycle icon -->
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M5 18a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm14 6a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-7-8h3l2 4h-4l-1-4zm-2 0L8 8H5V6h4l1-2zm3 4l2 4H9l-1-4h5z"/>
                </svg>
            </div>

            <h1>
                Stadtradeln
                <span>Gemeinsam radeln für das Klima</span>
            </h1>

            <p class="hero-subtitle">
                Tracke deine Fahrradtouren, schließe dich einem Team an und sammle gemeinsam Kilometer für eine nachhaltige Zukunft.
            </p>

            <div class="hero-actions">
                <a href="/register" class="btn btn-primary btn-lg">Jetzt registrieren</a>
                <a href="/login" class="btn btn-secondary btn-lg">Anmelden</a>
                <a href="/leaderboard" class="btn btn-secondary btn-lg">Rangliste ansehen</a>
            </div>

            <div class="features">
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                    </div>
                    <h3>Touren erfassen</h3>
                    <p>Trage deine Fahrradtouren einfach ein und behalte den Überblick über deine gefahrenen Kilometer.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                        </svg>
                    </div>
                    <h3>Team beitreten</h3>
                    <p>Schließe dich einem Team an oder erstelle dein eigenes und radelt gemeinsam für euer Ziel.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M7.5 21H2V9h5.5v12zm7.25-18h-5.5v18h5.5V3zM22 11h-5.5v10H22V11z"/>
                        </svg>
                    </div>
                    <h3>Rangliste</h3>
                    <p>Vergleiche dich mit anderen Radlern und Teams in der Rangliste und motiviere dich gegenseitig.</p>
                </div>
            </div>

            <footer class="home-footer">
                <p>Mit jedem Kilometer zählt dein Beitrag für eine grünere Zukunft.</p>
            </footer>
        </div>
    </section>
</body>
</html>
