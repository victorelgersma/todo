# To-Do

A minimal, list-based to-do app. No due dates — just lists in a sidebar
and tasks inside them, magic-link auth, own SQLite database, deployed
at todo.vjbe.net alongside (but entirely separate from) Penny Post.

## Local setup

See the "Laravel commands to run" section below for the full sequence
from a fresh clone, including how these generated files fit into a new
`laravel new` skeleton.

## Seed local database

```sh
php artisan migrate:fresh --seed
php artisan db:seed --class=DemoDataSeeder
```

## Run tests

```sh
composer test
```

If you hit `Allowed memory size of 134217728 bytes exhausted`, that's PHP's
CLI `memory_limit` (128M default in some installs, including Herd Lite),
not a bug in the tests. Fix permanently in `php --ini`'s "Loaded
Configuration File" — set `memory_limit = 512M` — rather than passing
`-d memory_limit=-1` each time, since `artisan test` shells out to a
fresh `pest` process that won't inherit a one-off CLI flag anyway.

## Run locally

```sh
php artisan serve
```

## To deploy

Deployed to the same Hetzner VPS as Penny Post, but as its own app with
its own SQLite database — the two never share a database connection or
file. Only Victor Elgersma can deploy this app (SSH keys).

Note to self: commit build files, since `npm run build` doesn't run on
the server.

```sh
ssh hetzner
cd /var/www/todo.vjbe.net
git pull
php artisan migrate --force
composer dump-autoload --optimize
```

Caddy config lives in `deploy/Caddyfile.todo.vjbe.net` — add that block
to the server's main Caddyfile once, then `sudo systemctl reload caddy`.
