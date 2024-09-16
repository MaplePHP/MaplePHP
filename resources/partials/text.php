
<div class="article border-bottom">
    <section class="wrapper max-w-screen-md">
        <?php echo $obj->tagline->dom()->create("h6")->attr("class", "title"); ?>
        <h1 class="title"><?php echo $obj->name; ?></h1>
        <p><?php echo $obj->content; ?></p>
    </section>
</div>
