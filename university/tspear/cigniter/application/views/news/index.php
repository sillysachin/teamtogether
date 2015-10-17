<h2><?php echo $title ?></h2>

<?php foreach ($news as $news_item): ?>

        <h3><?php echo $news_item['email'] ?></h3>
        <div class="main">
                <?php echo $news_item['gcm_regid'] ?>
        </div>
        <p><a href="<?php echo $news_item['email'] ?>">View USN</a></p>

<?php endforeach ?>