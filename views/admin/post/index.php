<h1 class="text-center"><?= $title; ?></h1>


<!-- flash message -->
<?= $flash; ?>


<a class="btn btn-outline-primary my-5" href="<?= $router->url('admin_post_new'); ?>">Add a new post</a>


<!-- list of posts -->
<?php if (!empty($posts)) : ?>
    <table class="table">
        <thead class="thead-dark">
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Name</th>
                <th scope="col">Created_at</th>
                <th scope="col">Updated_at</th>
                <th scope="col">Action</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($posts as $post) : ?>
                <tr>
                    <th scope="row"><?= $post->getId(); ?></th>
                    <td>
                        <a href="<?= $router->url('post', ["id" => $post->getId()]); ?>"><?= $post->getName(); ?></a>
                    </td>
                    <td><?= $post->getCreated_at()->format("m/d/y \a\\t H:i:s"); ?></td>
                    <td><?= $post->getUpdated_at()->format("m/d/y \a\\t H:i:s"); ?></td>
                    <td>
                        <?php if ($post->getPublished() === 1) : ?>
                            <a class="btn btn-outline-info rounded" href="<?= $router->url('post', ['id' => $post->getId()]); ?>">View</a>
                        <?php elseif ($post->getPublished() === 0) : ?>
                            <a class="btn btn-outline-info rounded" href="<?= $router->url('admin_posts_publish', ['id' => $post->getId()]); ?>">Publish</a>
                        <?php endif; ?>
                        <a class="btn btn-outline-success rounded" href="<?= $router->url('admin_post', ['id' => $post->getId()]); ?>">edit</a>
                        <form style="display:inline;" action="<?= $router->url('admin_post_delete', ['id' => $post->getId()]); ?>" method="post" onsubmit="return confirm('Do you really want to delete this post')">
                            <input type="hidden" name="id" value="<?= $post->getId(); ?>">
                            <input type="hidden" name="token" value="<?= $session->read('token'); ?>">
                            <button type="submit" class="btn btn-outline-danger rounded">delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else : ?>
    <div class="text-center alert alert-danger">
        <h4 class="text-center">No posts available</h4>
    </div>
<?php endif; ?>


<!-- pagination -->
<div class="d-flex justify-content-between my-4">
    <?= $pagination->previousLink($link); ?>
    <?= $pagination->nextLink($link); ?>
</div>