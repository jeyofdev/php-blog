<?php
   // get the categories of each posts
    $categories = array_map(function ($category) use ($router) {
        $url = $router->url('category', ['id' => $category->getId(), 'slug' => $category->getslug()]); 
        return '<a href="' . $url . '">' . $category->getName() . '</a>';
    }, $post->getCategories());

    $categories = implode(", ", $categories);
?>


<!-- cards for the list of posts  -->
<div class="col-md-6 mb-5">
    <div class="card">
        <div class="card-body">
            <a href="<?= $router->url('post', ['id' => $post->getID(), 'slug' => $post->getSlug()]); ?>">
                <h5 class="card-title"><?= $post->getName(); ?></h5>
            </a>
            <p><?= $categories; ?></p>
            <p class="card-text"><?= $post->getExcerpt(); ?></p>
            <p class="card-muted">written on <?= $post->getCreated_at()->format("d F Y"); ?></p>
            <a href="<?= $router->url('post', ['id' => $post->getID(), 'slug' => $post->getSlug()]); ?>" class="btn btn-primary">see more</a>
        </div>
    </div>
</div>