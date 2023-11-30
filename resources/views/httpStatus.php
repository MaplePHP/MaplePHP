
<article class="article">
    <section class="wrapper max-w-screen-sm align-center ingress">

        <?php if ($obj->headline()->isset()) : ?>
        <h1 class="title"><?php echo $obj->headline; ?></h1>
        <?php else : ?>
        <h1 class="title"><?php echo $obj->statusCode; ?> <?php echo $obj->phraseMessage; ?></h1>
        <?php endif; ?>

        <?php echo $obj->content("Dom")->create("p"); ?>

    </section>
</article>
