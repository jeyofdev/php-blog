<?php

    namespace jeyofdev\php\blog\Hydrate;


    use jeyofdev\php\blog\Entity\Category;
    use jeyofdev\php\blog\Entity\Post;
    use jeyofdev\php\blog\Entity\User;
    use jeyofdev\php\blog\Image\Image;
    use jeyofdev\php\blog\Table\CategoryTable;
    use jeyofdev\php\blog\Table\ImageTable;
    use jeyofdev\php\blog\Table\PostImageTable;
    use jeyofdev\php\blog\Table\RoleTable;
    use jeyofdev\php\blog\Table\UserTable;


    /**
     * Manage the hydration of posts
     * 
     * @author jeyofdev <jgregoire.pro@gmail.com>
     */
    class PostHydrate
    {

        public static function hydrateAllPostsPaginated (array $posts, Post $firstPost, CategoryTable $tableCategory, UserTable $tableUser, RoleTable $tableRole, ImageTable $tableImage, PostImageTable $tablePostImage) : array
        {
            PostHydrate::addCategoriesToPost($tableCategory, $firstPost);
            PostHydrate::addUserToPost($tableUser, $tableRole, $firstPost);
            PostHydrate::addImageToPost($tablePostImage, $tableImage, $firstPost);

            if (!empty($posts)) {
                PostHydrate::addCategoriesToAllPosts($tableCategory, $posts);
                PostHydrate::addUserToAllPosts($tableUser, $posts);
                PostHydrate::addImageToAllPosts($tableImage, $posts);
            }

            return [$firstPost, $posts];
        }



        public static function hydratePost (Post $post, CategoryTable $tableCategory, UserTable $tableUser, RoleTable $tableRole, ImageTable $tableImage, PostImageTable $tablePostImage) : Post
        {
            PostHydrate::addCategoriesToPost($tableCategory, $post);
            PostHydrate::addUserToPost($tableUser, $tableRole, $post);
            PostHydrate::addImageToPost($tablePostImage, $tableImage, $post);

            return $post;
        }



        /**
         * Add the associated categories to a post
         *
         * @param CategoryTable $tableCategory
         * @param Post $post
         * @return void
         */
        public static function addCategoriesToPost (CategoryTable $tableCategory, Post $post) : void
        {
            /**
             * @var Category[]
             */
            $categories = $tableCategory->findCategories(['id' => $post->getId()]);

            foreach ($categories as $category) {
                $post->addCategory($category);
            }
        }



        /**
         * Add the associated categories to a post and 
         * get the value of a column of each associated category
         *
         * @param CategoryTable $tableCategory
         * @param Post $post
         * @param string $column A column of the table Category
         * @return void
         */
        public static function addCategoriesToPostBy (CategoryTable $tableCategory, Post $post, string $column) : void
        {
            self::addCategoriesToPost($tableCategory, $post);

            $method = "get" . ucfirst($column);

            $postCategories = [];
            foreach ($post->getCategories() as $category) {
                $postCategories[] = $category->$method();
            }
        }



        /**
         * Add the associated categories on each post
         *
         * @param CategoryTable $tableCategory
         * @return void
         */
        public static function addCategoriesToAllPosts (CategoryTable $tableCategory, array $posts) : void
        {
            // get the ids of each items
            $postsById = [];
            foreach ($posts as $post) {
                $post->setCategories();
                $postsById[$post->getId()] = $post;
            }
            $ids = implode(", ", array_keys($postsById));

            /**
             * @var Categories[]
             */
            $categories = $tableCategory->findCategoriesByPosts($ids);

            foreach ($categories as $category) {
                $postsById[$category->getPost_Id()]->addCategory($category);
            }
        }



        /**
         * Add the associated user to a post
         *
         * @param UserTable $tableUser
         * @param Post $post
         * @return void
         */
        public static function addUserToPost (UserTable $tableUser, RoleTable $tableRole, Post $post) : void
        {
            /**
             * @var User
             */
            $user = $tableUser->findUser(['id' => $post->getId()]);
            UserHydrate::AddRole($tableRole, $user);
            $post->addUser($user);
        }



        /**
         * Add the associated user on each post
         *
         * @param UserTable $tableUser
         * @param array $posts
         * @return void
         */
        public static function addUserToAllPosts (UserTable $tableUser, array $posts) : void
        {
            // get the ids of each items
            $postsById = [];
            foreach ($posts as $post) {
                $postsById[$post->getId()] = $post;
            }
            $ids = implode(", ", array_keys($postsById));

            /**
             * @var User[]
             */
            $users = $tableUser->findUserByPosts($ids);

            foreach ($users as $user) {
                $postsById[$user->getPost_Id()]->addUser($user);
            }
        }



        /**
         * Add the associated image to a post
         *
         * @param PostImageTable $tablePostImage
         * @param ImageTable $tableImage
         * @param Post $post
         * @return void
         */
        public static function addImageToPost (PostImageTable $tablePostImage, ImageTable $tableImage, Post $post) : void
        {
            $imageId = Image::getImageIdOfPost($tablePostImage, $post);
            $image = $tableImage->find(["id" => $imageId]);
            $post->addImage($image);
        }



        /**
         * Add the associated image on each post
         *
         * @param ImageTable $tableImage
         * @return void
         */
        public static function addImageToAllPosts (ImageTable $tableImage, array $posts) : void
        {
            // get the ids of each items
            $postsById = [];
            foreach ($posts as $post) {
                $postsById[$post->getId()] = $post;
            }
            $ids = implode(", ", array_keys($postsById));

            /**
             * @var Image[]
             */
            $images = $tableImage->findImageByPosts($ids);

            foreach ($images as $image) {
                $postsById[$image->getPost_Id()]->addImage($image);
            }
        }
    }