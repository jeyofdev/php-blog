<?php

    namespace jeyofdev\php\blog\Form;


    use jeyofdev\php\blog\Form\generateForm\AbstractBuilderBootstrapForm;
    use jeyofdev\php\blog\Form\generateForm\FormInterface;


    /**
     * Build the form related to comments
     * 
     * @author jeyofdev <jgregoire.pro@gmail.com>
     */
    class CommentForm extends AbstractBuilderBootstrapForm implements FormInterface
    {
        /**
         * {@inheritDoc}
         */
        public function build (string $url, string $labelSubmit, array $categories = [], $createdAt = false, $updatedAt = false) : string
        {
            $this
                ->formStart($url, "post", "my-5", "formComment")
                ->input("hidden", "id", null, [])
                ->input("text", "username", "Username :", [], ["tag" => "div"])
                ->textarea("content", "Content :", ["rows" => 8], ["tag" => "div"]);

            $this
                ->submit($labelSubmit)
                ->reset("Reset")
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