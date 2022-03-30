 <?php

    use Phalcon\Di\FactoryDefault;
    use Phalcon\Loader;
    use Phalcon\Mvc\View;
    use Phalcon\Mvc\Application;
    use Phalcon\Url;
    use Phalcon\Http\Response;
    use Phalcon\Db\Adapter\Pdo\Mysql;
    use Phalcon\Events\Manager;


    define('BASE_PATH', dirname(__DIR__));
    define('APP_PATH', BASE_PATH . '/app');

    // Register an autoloader
    $loader = new Loader();

    $loader->registerDirs(
        [
            APP_PATH . "/controllers/",
            APP_PATH . "/models/",
        ]
    );
    $loader->registerNamespaces(
        [
            'App\Components' => APP_PATH . '/components/',
            'App\Handler' => APP_PATH . '/handler/'
        ]
    );

    $loader->register();

    $container = new FactoryDefault();
    $application = new Application($container);

    $container->set(
        'view',
        function () {
            $view = new View();
            $view->setViewsDir(APP_PATH . '/views/');
            return $view;
        }
    );


    $container->set(
        'url',
        function () {
            $url = new Url();
            $url->setBaseUri('/');
            return $url;
        }
    );

    $eventsManager = new Manager();
    $application->setEventsManager($eventsManager);
    $container->set('EventsManager', $eventsManager);
    $eventsManager->attach('order', new \App\Handler\EventHandler());
    $eventsManager->attach('application:beforeHandleRequest', new \App\Handler\EventHandler());

    $container->set(
        'response',
        function () {
            return
                $response = new Response();
        }
    );

    $container->set(
        'db',
        function () {
            global $config;
            return new Mysql(
                [
                    'host'  => 'mysql-server',
                    'username' => 'root',
                    'password' => 'secret',
                    'dbname'   => 'store',
                ]
            );
        }
    );


    try {
        // Handle the request
        $response = $application->handle(
            $_SERVER["REQUEST_URI"]
        );
        $response->send();
    } catch (\Exception $e) {
        echo 'Exception: ', $e->getMessage();
    }
