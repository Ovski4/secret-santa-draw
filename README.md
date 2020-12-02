Secret Santa draw
=================

A super quick implementation of drawing lots for secret santa using Symfony.

Many things to improve but who cares as long as everyone is happy for chistmas ?

Installation
------------

Create a `.env.local` file with the MAILER_DSN env defined (used to send the emails)

```
MAILER_DSN=smtp://user@gmail.com:app_password@smtp.gmail.com:465
```

Install dependencies :

```
docker-compose run php composer install
```

Usage
-----

Update the Command in `src/Command/DrawCommand.php`.
 - create participants
 - create the exclusion groups if some participants should not draw each other
 - update the email content

Then run :
```
docker-compose run php bin/console app:draw-secret-santa
```
