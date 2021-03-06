<?php

    namespace jeyofdev\php\blog\Form;


    use jeyofdev\php\blog\Form\generateForm\AbstractBuilderBootstrapForm;
    use jeyofdev\php\blog\Form\generateForm\FormInterface;


    /**
     * Build the registration form
     * 
     * @author jeyofdev <jgregoire.pro@gmail.com>
     */
    class RegisterForm extends AbstractBuilderBootstrapForm implements FormInterface
    {
        /**
         * {@inheritDoc}
         */
        public function build (string $url, string $labelSubmit, array $categories = [], $createdAt = false, $updatedAt = false) : string
        {
            $this
                ->formStart($url, "post", "my-5")
                ->input("text", "username", "Username :", [], ["tag" => "div"])
                ->input("password", "password", "Password :", [], ["tag" => "div"])
                ->input("password", "passwordConfirm", "Password Confirmation :", [], ["tag" => "div"])
                ->submit($labelSubmit)
                ->reset("reset")
                ->formEnd();

            return implode("\n", $this->extract());
        }



        /**
         * {@inheritDoc}
         */
        public function extract () : array
        {
            extract($this->form);

            $buttons = implode("\n", $buttons);
            $fields = implode("\n", $fields);

            $this->form = [
                "start" => $start,
                "fields" => $fields,
                "buttons" => $buttons,
                "end" => $end,
            ];

            return $this->form;
        }
    }