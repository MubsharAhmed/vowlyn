# Cold email outreach

**Admin → Outreach** is where the studio approaches businesses that have not been
in touch: a sequence of emails, sent to a list, with limits that keep the sending
domain healthy.

It is deliberately separate from **Leads**. A lead asked to be contacted and may
always be answered. A prospect did not ask for anything, so that side carries
rules the lead side does not need: a daily limit, a sending window, a cooldown, a
permanent opt-out list, and a record of every message including the ones that
were refused.

## The three screens

| Screen | What it is for |
| --- | --- |
| **Campaigns** | The sequences. A first email plus follow-ups, with a live count of who is waiting, who has been sent to, and how many replied. |
| **Prospects** | The people. Import a list from a spreadsheet, add them to campaigns, and mark what happened. |
| **Opt-out list** | Addresses that must never be emailed again. Consulted before *every single* send. |

## Building a sequence

A campaign is a draft until it is started. Nothing is sent while it is a draft or
paused — which is what makes it safe to write the copy, add people, and check the
numbers before anything reaches anybody.

Each step waits a number of days after the one before it:

```
Step 1 · sent immediately            Step 2 · sent 4 days after step 1
Step 3 · sent 10 days after step 2   → sequence complete
```

A sequence stops for good, mid-way, when any of these is true:

- **They reply.** Mark it with **They replied** on the prospect, and every
  scheduled follow-up for that person is cancelled at once. This is the rule that
  matters most: nothing is worse than a "just checking in" arriving after
  somebody has answered.
- **They opt out**, from the link in the email or on the button in the panel.
- **The address bounces.**
- **Somebody marks them as not interested.**

## The four limits, and where they come from

Set in `config/outreach.php`, overridable in `.env`:

| Setting | Default | Why it exists |
| --- | --- | --- |
| `OUTREACH_DAILY_LIMIT` | 40 | A domain that suddenly sends hundreds of messages a day gets filtered — which would break lead replies too. Raise it gradually. |
| `OUTREACH_PER_RUN` | 20 | One run of the sender stays bounded and predictable. |
| `OUTREACH_INTERVAL_SECONDS` | 3 | Spaces sends out inside a run. |
| `OUTREACH_COOLDOWN_DAYS` | 30 | The same person cannot be approached twice in a month, even by a second campaign. |
| `OUTREACH_WINDOW_START` / `_END` | 09:00–17:00 | Outreach that arrives at 3am reads as automated. |
| `OUTREACH_WEEKDAYS_ONLY` | true | Nobody wants a sales email on a Sunday. |
| `OUTREACH_TIMEZONE` | `APP_TIMEZONE` | **Set this to the business timezone**, not UTC, or the window is in the wrong place as soon as the clocks change. |
| `OUTREACH_POSTAL_ADDRESS` | empty | Required by CAN-SPAM, expected under GDPR. Appended to every email. |

A run that is refused does not lose work: it **defers** the due messages to the
next open window, so a queue held overnight still arrives at 9am.

## The pre-send check

While the copy is being written, each step is checked for the things that get
cold email filtered or ignored — phrases filters look for, a subject in capitals,
too many links, no personalisation, no question, a first email that opens with
`Re:`. It reports; it never blocks. An administrator who has read a warning may
still have a reason.

**Check the copy** on the campaign checks all steps at once, which is the only
place the step order is known.

The panel also warns when the copy cannot be sent properly at all: no mail
credentials, no postal address, or a reply-to that is not a mailbox somebody
reads.

## Importing a list

**Prospects → Import a list** takes a CSV from Excel, Google Sheets or a CRM. A
column named `email` is required; `company`, `contact_name`, `role`, `industry`,
`region`, `website`, `source` and `notes` are used when present. Commas,
semicolons and tabs are all understood, and a UTF-8 byte-order mark from Excel is
stripped.

The result is always reported in full: *imported 24, updated 3, already here 2,
on the opt-out list 1, invalid 4*. Addresses on the opt-out list are never
imported, invalid rows are listed, and each person already on the list is topped
up rather than duplicated or overwritten. The uploaded file is deleted after it
has been read.

## What the client does when the mail credentials arrive

Nothing in this section needs a code change. Add the `MAIL_*` values to `.env` as
described in `MAIL-REPLIES.md` (outreach uses the same mailer, from address and
reply-to), then:

```bash
php artisan config:cache
```

Until then the panel says so on the campaign screen and on the dashboard, and
every message is recorded as **Logged only** rather than pretending it was
delivered.

## The scheduled sender

Outreach is sent by a scheduled command, not by a queue worker — one cron entry
is easier to keep alive than a worker process, and a worker that is not running
would leave the panel showing a campaign going out while nothing left the
building.

```cron
* * * * * cd /var/www/vowlyn && php8.4 artisan schedule:run >> /dev/null 2>&1
```

The scheduler calls `outreach:send` every fifteen minutes. To run it by hand:

```bash
php artisan outreach:send                 # everything that is due
php artisan outreach:send --limit=5       # five messages at most
php artisan outreach:send --campaign=2    # one campaign only
```

It reports what it did, and says so out loud when mail is not configured yet.

## What is deliberately missing

- **No open or click tracking.** A tracking pixel is the single most common
  privacy complaint in cold email, it is unreliable by design, and it makes the
  message more likely to be filtered. Replies are the signal that matters, and
  they are recorded.
- **No inbound reply capture.** An answer lands in the mailbox set as
  `MAIL_REPLY_TO_ADDRESS`. Mark the person with **They replied** so the sequence
  stops; capturing it automatically would mean reading the studio's mailbox.
- **No bulk "send everything now" bypass.** The limits are the feature.
