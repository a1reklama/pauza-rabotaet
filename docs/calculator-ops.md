# Calculator Operations

This document records where the standalone calculator lives and how the
2026-06-30 self-score hotfix was applied.

## Project Boundary

- Local site project: `E:\Codex\Сайт 12 шагов`
- Live WordPress root in REG.RU file manager:
  `/www/pauzarabotaet.ru/`
- Live standalone calculator:
  `/www/pauzarabotaet.ru/calculator/`
- Public URL:
  `https://pauzarabotaet.ru/calculator/`
- This calculator is not part of `max-four-step-bot` or
  `telegram-four-step-bot`.

## Access Files

Do not commit secrets. The local-only access files are:

- `hosting-access.local.txt`
  - `FTP_HOST`
  - `FTP_USER`
  - `FTP_PASSWORD`
  - database fields
- `wordpress-admin.local.txt`
  - `WP_URL`
  - `WP_ADMIN_USER`
  - `WP_ADMIN_PASSWORD`

If FTP is unavailable from the current machine, WordPress admin can still be
used for one-time maintenance plugins. Remove temporary plugins after the
operation is verified.

## 2026-06-30 Hotfix And Correction

Initial request: in calculator stage `Kachestvo otnosheniy s samim soboy`, set
the yellow "always / yes" fixed result to `0` for these questions:

- `Играю ли я в азартные игры?`
- `Делаю ли я ставки?`
- `Изменяю ли я любимому человеку?`
- `Применяю ли я насилие над кем-либо - эмоциональное или физическое?`
- `Присваиваю ли я чужие деньги?`

Live files changed:

- `/www/pauzarabotaet.ru/calculator/assets/js/config.js`
- `/www/pauzarabotaet.ru/calculator/index.html`

What changed in `config.js`:

- group `["Играю ли я в азартные игры?", "Делаю ли я ставки?"]`
  changed from `score: 1` to `score: 0`;
- group beginning with `"Изменяю ли я любимому человеку?"`
  changed from `score: 2` to `score: 0`.

What changed in `index.html`:

- `assets/js/config.js` now loads as
  `assets/js/config.js?v=20260630-selfscore0` to bypass browser/cache headers.

Server backups created by the hotfix plugin:

- `/var/www/u3480856/data/www/pauzarabotaet.ru/calculator/assets/js/config.js.bak-20260630-120703`
- `/var/www/u3480856/data/www/pauzarabotaet.ru/calculator/index.html.bak-20260630-120703`

Verification on 2026-06-30:

- `https://pauzarabotaet.ru/calculator/assets/js/config.js?verify2=<ts>`
  returned `200`;
- old fixed groups `score: 1` and `score: 2` for the target questions were
  absent;
- target fixed groups were present with `score: 0`;
- calculator page loaded `config.js?v=20260630-selfscore0`.

Follow-up correction on 2026-06-30:

- final decision: gambling and bets stay at `score: 0`;
- the group beginning with `"Изменяю ли я любимому человеку?"` must be
  `score: 3`, not `score: 0`;
- `/www/pauzarabotaet.ru/calculator/assets/js/config.js` was corrected from
  `score: 0` to `score: 3` for:
  - `Изменяю ли я любимому человеку?`
  - `Применяю ли я насилие над кем-либо - эмоциональное или физическое?`
  - `Присваиваю ли я чужие деньги?`
- `/www/pauzarabotaet.ru/calculator/index.html` now loads
  `assets/js/config.js?v=20260630-selfscore-final`.

Correction backups created on the server:

- `/var/www/u3480856/data/www/pauzarabotaet.ru/calculator/assets/js/config.js.bak-20260630-122955`
- `/var/www/u3480856/data/www/pauzarabotaet.ru/calculator/index.html.bak-20260630-122955`

Verification after correction:

- `https://pauzarabotaet.ru/calculator/assets/js/config.js?correction_verify=<ts>`
  returned `200`;
- calculator page loaded `config.js?v=20260630-selfscore-final`;
- group `["Играю ли я в азартные игры?", "Делаю ли я ставки?"]` was present
  with `score: 0`;
- group beginning with `"Изменяю ли я любимому человеку?"` was present with
  `score: 3`;
- temporary correction WordPress plugin was deactivated and deleted.

## Temporary Maintenance Scripts

These scripts are kept only as an auditable record of how the hotfix was
performed:

- `scripts/wp-calculator-selfscore-hotfix/pauza-calculator-selfscore-hotfix.php`
- `scripts/wp-calculator-selfscore-cleanup/pauza-calculator-selfscore-cleanup.php`
- `scripts/wp-calculator-selfscore-correction/pauza-calculator-selfscore-correction.php`

They are not active on production. The temporary WordPress plugins were
deactivated and deleted after verification.
