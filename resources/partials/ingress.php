<?php
/**
 * MaplePHP Template file
 * @psalm-suppress UnusedParam
 * @noinspection PhpUndefinedVariableInspection
 */
?>
<div class="article bg-secondary border-bottom">
    <header class="wrapper max-w-screen-md align-center text-lg">
        <?php echo $obj->tagline->dom()->create("h6")->attr("class", "title"); ?>
        <h1 class="title"><?php echo $name; ?></h1>
        <?php echo $obj->content->domCreate("p"); ?>
    </header>
</div>
