# Tracker Application

## Installation instructions

[Composer][1] is used to manage package dependencies. Make sure that it's installed on your system.

- If you don't have Composer yet, download it and follow the instructions on http://getcomposer.org/
or just run the following command:

```bash
curl -s https://getcomposer.org/installer | php
```

- Clone Tracker application https://github.com/olegmack/tracker.git with:

```bash
git clone https://github.com/olegmack/tracker.git
```

- Install dependencies with composer.

```bash
php composer.phar install 
```

- Create DB
```bash
php app/console doctrine:database:create 
```

- Create Tables
```bash
php app/console doctrine:schema:create
```

- Load Fixtures
```bash
php app/console doctrine:fixtures:load
```

- Load Tests
```bash
phpunit -c app/ src/Oro/
```

[1]:  http://getcomposer.org/
