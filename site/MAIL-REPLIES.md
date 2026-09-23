# Answering a lead by email

Enquiries are answered from the admin panel, so nobody has to open a mailbox to
send the first reply.

Open **Admin → Leads → Contact requests**, then use **Send reply** on the row or
on the request's own page. The dialog opens with a professional reply already
written for the service the lead asked about, plus their name, company and
service filled in. Edit anything, then **Send email**.

Every attempt is recorded under **Sent replies** on the request: the wording that
went out, the mailer that carried it, and any failure reason. The lead moves to
**Replied** once a message is accepted by the mailer.

## Templates — one professional reply per service

**Admin → Leads → Mail templates** holds the starting points: web app, mobile,
AI, SaaS, security, cloud/DevOps, content creation, marketing, and a general
fallback. Each is grouped by the service ("profession") it was written for, so
the reply dialog offers the right one first.

- Add as many as you like — several variants for one service, or a new service
  that does not exist yet. **Duplicate** copies an existing reply to rewrite.
- **Offer this when replying** controls whether a template appears in the dialog.
  Switch it off to park a draft.
- Placeholders are replaced from the enquiry before the message is shown:
  `{name}`, `{company}`, `{service}`, `{studio}`.
- Everything is plain text. Line breaks are kept, and both the message and the
  lead's own details are escaped, so nothing typed or submitted can break or
  inject into the email.

## Adding the mail credentials

The application ships with `MAIL_MAILER=log`, which accepts a message and writes
it to `storage/logs/laravel.log` instead of delivering it. While that is the
case the panel shows a warning, the reply is recorded as **Logged only**, and
the lead is never marked as answered.

Put the real credentials in the production `.env`
(`/var/www/vowlyn-shared/.env`), then run `php artisan config:cache`.

```
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_SCHEME=tls
MAIL_USERNAME=hello@vowlyn.com
MAIL_PASSWORD=<16-character app password>
MAIL_FROM_ADDRESS="hello@vowlyn.com"
MAIL_FROM_NAME="Vowlyn"
MAIL_REPLY_TO_ADDRESS="hello@vowlyn.com"
MAIL_REPLY_TO_NAME="Vowlyn"
```

- **`MAIL_PASSWORD` is an app password, not the account password** for Google
  Workspace or Gmail (requires 2-step verification). Other providers work the
  same way — only host, port and scheme change.
- **`MAIL_REPLY_TO_ADDRESS`** is where the lead's answer lands when they reply.
  It defaults to `MAIL_FROM_ADDRESS`, which is right for a normal company
  mailbox: sent mail then appears in that mailbox's Sent folder, and answers
  arrive in its inbox.
- A half-finished configuration (SMTP selected with no host or username) is
  treated as not configured, so the warning stays until it actually works.

Nothing else is needed: no code change, no migration, no server package.

## Notes and limits

- Replies are sent immediately while the administrator waits, so the panel can
  report the real outcome. A queued send could claim success while a stopped
  worker quietly dropped the message.
- The panel is outbound only. A lead's answer goes to
  `MAIL_REPLY_TO_ADDRESS`; it does not appear in the panel. Bringing replies
  back into the request through inbound email is a separate feature.
- Deleting a contact request deletes its reply history with it.
- `php artisan test` covers the send path, the failure path, the
  not-yet-configured path and the escaping of both the message and the lead's
  details, without contacting any mail server.
