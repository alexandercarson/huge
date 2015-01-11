<?php

/**
 * This is the "base controller class". All other "real" controllers extend this class.
 * Whenever a controller is created, we also
 * 1. initialize a session
 * 2. check if the user is not logged in anymore (session timeout) but has a cookie
 * 3. create a database connection (that will be passed to all models that need a database connection)
 * 4. create all the model objects (TODO auto-loading !?)
 * 5. create a view object
 */
class Controller
{
    /** @var object Database The database connection */
    private $database;

    /**
     * Construct the (base) controller. This happens when a real controller is constructed, like in
     * the constructor of IndexController when it says: parent::__construct();
     */
    function __construct()
    {
        // always initialize a session
        Session::init();

        // user is not logged in but has remember-me-cookie ? then try to login with cookie ("remember me" feature)
        // TODO encapsulate COOKIE super-global
        if (!Session::userIsLoggedIn() AND isset($_COOKIE['remember_me'])) {
            header('location: ' . URL . 'login/loginWithCookie');
        }

        // create database connection
        // TODO put the try/catch part inside the Database class ?
        try {
            $this->database = new Database();
        } catch (PDOException $e) {
            exit('Database connection could not be established.');
        }

        // TODO it's not a good idea to load ALL models by default, or ? let's discuss this.
        // TODO check performance vs. usability when pre-loading ALL models
        // TODO replace this with some kind of "model"-autoloader
        // TODO as "model" is just a layer in the application there cannot be multiple "models", so maybe rename this ?
        // NoteModel construction & db injection has been removed as this is not done by static method calls
        //$this->NoteModel = new NoteModel($this->database);
        $this->LoginModel = new LoginModel($this->database);
        $this->ProfileModel = new ProfileModel($this->database);

        // create a view object to be able to use it inside a controller, like $this->View->render();
        $this->View = new View();
    }
}
