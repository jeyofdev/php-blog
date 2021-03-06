<?php

    namespace jeyofdev\php\blog\Controller\Admin;


    use jeyofdev\php\blog\App;
    use jeyofdev\php\blog\Controller\AbstractController;
    use jeyofdev\php\blog\Auth\Auth;


    /**
     * Manage the controller of the admin home page
     * 
     * @author jeyofdev <jgregoire.pro@gmail.com>
     */
    class HomeController extends AbstractController
    {
        public function index () : void
        {
            // check that the user is logged in
            Auth::isConnect($this->router);

            // flash message
            $flash = $this->session->generateFlash();

            $title = App::getInstance()
                ->setTitle("Administration of the blog")
                ->getTitle();

            $this->render("admin.home.index", $this->router, $this->session, compact("title", "flash"));
        }
    }