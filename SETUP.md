# VYNARA FINANCE – Setup & Configuration Guide

## 🚀 Getting Started

### 1. Database Configuration (Neon PostgreSQL)

#### Option A: Local Development (.env file)
1. Copy `.env.example` to `.env`:
   ```bash
   cp .env.example .env
   ```

2. Update your `.env` file with your Neon PostgreSQL connection string:
   ```
   DATABASE_URL=postgresql://neondb_owner:YOUR_PASSWORD@ep-billowing-sea-ahmhmtma-pooler.c-3.us-east-1.aws.neon.tech:5432/neondb?sslmode=require
   ```

3. Set other configuration variables:
   ```
   CONTACT_EMAIL=your-email@example.com
   WHATSAPP_NUMBER=+33612345678
   ADMIN_PASSWORD=your_secure_password
   ```

#### Option B: Production (Vercel/Environment Variables)
Set environment variables directly in your hosting platform:
- `DATABASE_URL` — Your Neon PostgreSQL connection string
- `CONTACT_EMAIL` — Contact email address
- `WHATSAPP_NUMBER` — WhatsApp number
- `ADMIN_PASSWORD` — Secure admin password

### 2. Initialize Database Tables

Once your `.env` is configured (or env vars are set), run the setup script:

```
https://your-domain.com/setup.php?token=setup-vynara-2026
```

This will create:
- `settings` table (site configuration)
- `loan_applications` table (loan requests)
- `contact_messages` table (contact form submissions)

**⚠️ Important:** Delete or rename `setup.php` after initialization to prevent unauthorized database resets.

### 3. Admin Panel Access

Access the admin panel at:
```
https://your-domain.com/admin007
```

Login with the password you set in `ADMIN_PASSWORD` environment variable.

---

## 🔒 Security Best Practices

### Environment Variables
- **Never commit `.env` file** — It's in `.gitignore`
- Use `.env.example` as a template
- Keep passwords in environment variables, not in code
- Rotate admin password regularly

### Admin Password
- Change the default password immediately
- Use a strong password (min 12 characters, mix of uppercase, lowercase, numbers, symbols)
- Password is hashed with bcrypt (never stored in plain text)

### Database
- Neon PostgreSQL uses SSL encryption by default (`sslmode=require`)
- Keep your connection string confidential
- Review database backups regularly

---

## 🌐 Language Support

Supported languages (9 countries):
- 🇩🇰 Danish (Danemark)
- 🇩🇪 German (Allemagne)
- 🇦🇹 Austrian German (Autriche)
- 🇮🇹 Italian (Italie)
- 🇵🇹 Portuguese (Portugal)
- 🇬🇷 Greek (Grèce)
- 🇸🇰 Slovak (Slovaquie)
- 🇸🇮 Slovenian (Slovénie)
- 🇨🇭 Swiss German (Suisse)

Switch languages with the `?lang=` parameter:
```
https://your-domain.com/?lang=de
https://your-domain.com/?lang=it
```

---

## 📧 Email Configuration

### SMTP Settings
Configure email sending in Admin Panel > Paramètres:
- SMTP Host
- SMTP Port (typically 587)
- SMTP User (sender email)
- SMTP Password

### Contact Email
Set the contact email address displayed on the website and used for form submissions.

### WhatsApp Integration
Add your WhatsApp number to enable the WhatsApp button on the website.

---

## 📁 Project Structure

```
vynara-finance/
├── admin007/              # Admin dashboard (protected)
├── api/                   # API endpoints
├── assets/
│   ├── css/style.css     # Main stylesheet
│   ├── js/main.js        # JavaScript
│   └── images/           # Images & icons
├── config/
│   ├── config.php        # Site configuration
│   ├── database.php      # Database connection
│   └── env-loader.php    # Environment loader
���── includes/
│   ├── header.php        # Global header
│   ├── footer.php        # Global footer
│   └── functions.php     # Utility functions
├── lang/                 # Language files
├── pages/                # Page templates
├── .env.example          # Environment variables template
├── .gitignore            # Git ignore rules
├── setup.php             # Database initialization
└── index.php             # Main router
```

---

## 🛠️ Troubleshooting

### Database Connection Error
- ✓ Check `DATABASE_URL` is correctly set
- ✓ Verify Neon PostgreSQL connection string format
- ✓ Ensure SSL mode is set to `require`
- ✓ Run setup.php to initialize tables

### Admin Login Not Working
- ✓ Verify `ADMIN_PASSWORD` is set correctly
- ✓ Password is case-sensitive
- ✓ Clear browser cookies and try again

### Email Not Sending
- ✓ Configure SMTP settings in Admin Panel
- ✓ Check firewall/port 587 is open
- ✓ Verify SMTP credentials are correct

---

## 📝 License & Support

For issues or questions, contact: `contact@vynara-finance.cfd`

---

## ✅ Checklist Before Going Live

- [ ] Update `.env` with production values
- [ ] Test database connection
- [ ] Configure SMTP for email
- [ ] Set WhatsApp number
- [ ] Change admin password
- [ ] Delete or secure `setup.php`
- [ ] Test all forms (contact, loan application)
- [ ] Verify all pages load correctly
- [ ] Check mobile responsiveness
- [ ] Enable HTTPS/SSL certificate
