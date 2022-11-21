# Install using composer #

    composer require stilmark/database

## Basic usage ##

    require('/vendor/autoload.php');

    use Symfony\Component\Dotenv\Dotenv;
    use Stilmark\Parse\Dba;

    $dotenv = new Dotenv();
    $dotenv->load(ROOT.'/.env');

    # Build sql queries
    $users = Dba::instance()->table('users')->list();

    # Extend your models on Dbi()
    $users = User::list();

# Documentation #

https://stilmark-projects.gitbook.io/database
