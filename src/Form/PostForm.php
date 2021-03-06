<?php

    namespace jeyofdev\php\blog\Form;


    use jeyofdev\php\blog\Form\generateForm\AbstractBuilderBootstrapForm;
    use jeyofdev\php\blog\Form\generateForm\FormInterface;


    /**
     * Build the form related to posts
     * 
     * @author jeyofdev <jgregoire.pro@gmail.com>
     */
    class PostForm extends AbstractBuilderBootstrapForm implements FormInterface
    {
        /**
         * {@inheritDoc}
         */
        public function build (string $url, string $labelSubmit, array $categories = [], $createdAt = false, $updatedAt = false) : string
        {
            $this
                ->formStart($url, "post", "my-5", null, true)
                ->input("text", "name", "Title :", [], ["tag" => "div"])
                ->input("text", "slug", "Slug :", [], ["tag" => "div"])
                ->select("categoriesIds", "Categories :", ["multiple" => true], $categories, ["tag" => "div"])
                ->textarea("content", "Content :", ["rows" => 8], ["tag" => "div"])
                ->file("image", "Image :", [], ["tag" => "div"]);

            if ($createdAt) {
                $this->input("text", "created_at", "Creation date :", ["disabled" => true], ["tag" => "div"]);
            }

            if ($updatedAt) {
                $this->input("text", "updated_at", "Last modified date :", ["disabled" => true], ["tag" => "div"]);
            }

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