<?php

    namespace jeyofdev\php\blog\Form\generateForm;


    /**
     * Set the elements of the form
     * 
     * @author jeyofdev <jgregoire.pro@gmail.com>
     */
    interface BuilderFormInterface
    {
        /**
         * Set the opening tag of the form
         *
         * @return self
         */
        public function formStart (string $action = "#", string $method = "post", ?string $class = null);



        /**
         * Set the closing tag of the form
         *
         * @return self
         */
        public function formEnd ();



        /**
         * Set the input
         *
         * @return self
         */
        public function input (string $type, string $name, string $label, array $options, array $surround = [], ?string $errorClass = null);



        /**
         * Set the textarea
         *
         * @return self
         */
        public function textarea (string $name, string $label, array $options, array $surround = [], ?string $errorClass = null);



        /**
         * Set a submit button
         *
         * @return self
         */
        public function submit (string $label, ?string $class = null);



        /**
         * set a reset button
         *
         * @return self
         */
        public function reset (string $label, ?string $class = null);
    }