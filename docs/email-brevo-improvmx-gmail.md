# MTD Art email architecture: Brevo + ImprovMX + Gmail

This document describes the production email setup for `mtdart.ro`.

## Architecture

### Outgoing application email

Laravel -> database queue -> Laravel queue worker -> Brevo SMTP -> recipient

The application sends from:

- `MTD Art <contact@mtdart.ro>`

Brevo is responsible for transactional delivery (order confirmations, proforma attachments, paid-order notifications, custom request emails, contact notifications and admin MFA).

### Incoming human email

Sender -> `contact@mtdart.ro` -> ImprovMX -> existing Gmail inbox

`contact@mtdart.ro` is an ImprovMX alias, not an IONOS mailbox. This keeps the public/legal address stable while allowing the team to read messages in Gmail.

### Manual replies from Gmail

Gmail can be configured with "Send mail as" for `contact@mtdart.ro`, using the same Brevo SMTP credentials. This allows manual replies to keep the MTD Art sender identity.

## Laravel production variables

Keep real credentials only in the server `.env`. Never commit them.

```env
MAIL_MAILER=smtp
MAIL_SCHEME=null
MAIL_HOST=smtp-relay.brevo.com
MAIL_PORT=587
MAIL_USERNAME=BREVO_SMTP_LOGIN
MAIL_PASSWORD=BREVO_SMTP_KEY
MAIL_FROM_ADDRESS="contact@mtdart.ro"
MAIL_FROM_NAME="MTD Art"

QUEUE_CONNECTION=database
SHOP_LEGAL_EMAIL="contact@mtdart.ro"
```

For Brevo SMTP, use the SMTP login and an SMTP key, not a Brevo API key.

After changing `.env`:

```bash
php artisan optimize:clear
php artisan queue:restart
```

## DNS at IONOS

Do not copy placeholder Brevo authentication values from this document. Brevo generates account/domain-specific values; copy those exact records from the Brevo dashboard.

### ImprovMX inbound records

Unless another mail provider is intentionally receiving mail for `mtdart.ro`, remove old MX records and set:

| Type | Host | Value | Priority |
| --- | --- | --- | --- |
| MX | @ | mx1.improvmx.com | 10 |
| MX | @ | mx2.improvmx.com | 20 |

ImprovMX also recommends an SPF TXT record. If the domain has no SPF record already:

```text
v=spf1 include:spf.improvmx.com ~all
```

A domain must not have multiple SPF TXT records. If an SPF record already exists, merge the required mechanisms into the existing record instead of adding a second `v=spf1` record.

### Brevo sending-domain authentication

Authenticate `mtdart.ro` in Brevo. Add the exact records Brevo provides, normally:

- Brevo verification code (TXT)
- DKIM (TXT or two CNAME records, depending on the account)
- DMARC (TXT)

Only one DMARC record should exist for `_dmarc.mtdart.ro`. If one already exists, update/merge it rather than creating a second DMARC record.

Brevo's shared SMTP infrastructure does not require adding a second root SPF record for Brevo. DKIM/DMARC authentication must pass for the `mtdart.ro` sender domain.

## ImprovMX setup

Create the domain `mtdart.ro` in ImprovMX and create an alias:

```text
contact@mtdart.ro -> YOUR_EXISTING_GMAIL_ADDRESS
```

After DNS propagation, send a message from an unrelated email account to `contact@mtdart.ro` and verify that it arrives in Gmail.

## Gmail "Send mail as"

After inbound forwarding works:

1. Gmail -> Settings -> See all settings -> Accounts and Import.
2. Under "Send mail as", choose "Add another email address".
3. Name: `MTD Art`.
4. Address: `contact@mtdart.ro`.
5. SMTP server: `smtp-relay.brevo.com`.
6. Port: `587`.
7. Username: Brevo SMTP login.
8. Password: Brevo SMTP key.
9. Use TLS/secured connection offered by Gmail for port 587.
10. Gmail sends a verification message to `contact@mtdart.ro`; it should arrive through ImprovMX in the Gmail inbox. Confirm it.

## Production verification

First test Brevo SMTP synchronously without involving the queue:

```bash
php artisan mtd:mail-test your-real-inbox@example.com
```

Then check both the recipient inbox and Brevo transactional logs.

Next verify the queue worker is running. Transactional workflows such as order confirmation and contact/custom request notifications rely on the Laravel database queue:

```bash
php artisan queue:work --tries=3
```

In production the worker must be supervised by systemd/Supervisor or another process manager; do not rely on an SSH terminal session.

Finally test:

1. contact form -> arrives at `contact@mtdart.ro` and is forwarded to Gmail;
2. custom request -> customer acknowledgement + internal notification;
3. paid Stripe order -> customer confirmation/proforma + internal paid-order notification;
4. admin login -> MFA email;
5. direct external email to `contact@mtdart.ro` -> forwarded to Gmail;
6. manual Gmail reply sent as `contact@mtdart.ro`.

Shipping notification email is intentionally not part of the current workflow.
