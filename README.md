# VYNARA FINANCE

Site web professionnel multilingue pour services financiers.

## Déploiement Vercel

### 1. Variables d'environnement à configurer dans Vercel

| Variable | Valeur |
|----------|--------|
| `DATABASE_URL` | Votre URL Neon PostgreSQL |

### 2. Initialisation de la base de données

Après déploiement, accédez à :
```
https://vynara-finance.cfd/setup.php?token=setup-vynara-2026
```
**Supprimez `setup.php` après l'initialisation.**

### 3. Administration

Accès admin : `https://vynara-finance.cfd/admin007`  
Mot de passe : `19990000`

Dans l'admin, configurez :
- Numéro WhatsApp
- Email de contact
- Configuration SMTP

### 4. Langues supportées

| Drapeau | Pays | Code |
|---------|------|------|
| 🇩🇰 | Danemark | da |
| 🇩🇪 | Allemagne | de |
| 🇦🇹 | Autriche | at |
| 🇮🇹 | Italie | it |
| 🇵🇹 | Portugal | pt |
| 🇬🇷 | Grèce | el |
| 🇸🇰 | Slovaquie | sk |
| 🇸🇮 | Slovénie | sl |
| 🇨🇭 | Suisse | ch |

### 5. Pages

- `/` — Accueil
- `/services` — Services financiers
- `/process` — Processus
- `/about` — À propos
- `/contact` — Contact
- `/apply` — Demande de prêt
- `/admin007` — Panneau d'administration

## Stack technique

- **PHP 8.1+** — Routeur maison, aucun framework
- **PostgreSQL (Neon)** — Base de données
- **CSS custom** — Design system complet, responsive
- **JavaScript vanilla** — Animations, carousel, compteurs
- **Vercel PHP runtime** — Déploiement serverless
