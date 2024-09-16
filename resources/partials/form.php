<?php
/**
 * MaplePHP Template file
 * @psalm-suppress UnusedParam
 * @noinspection PhpUndefinedVariableInspection
 */
?>
<article class="article">
    <div class="wrapper max-w-screen-md">
        
        <header class="mb-50 sm:mb-30">
            <?php echo $tagline->dom()->create("h6")->attr("class", "title"); ?>
            <h1 class="h2 title"><?php echo $name; ?></h1>
            <p><?php echo $content; ?></p>
        </header>

        <div id="form">
            <form action="<?php echo $form->action; ?>" method="<?php echo $form->method->get("post"); ?>">
                <div class="holder-30">
                    <?php
                    if (is_string($obj->form->form)) {
                        echo $obj->form->form;
                    } else {
                        echo $obj->form->form->getForm();
                    }
                    ?>
                </div>
                <input class="inp-csrf-token" type="hidden" name="csrfToken" value="<?php echo $obj->form->token->get(((!is_string($obj->form->form)) ? $obj->form->form->getToken() : null)); ?>">
                <input class="button wa-xhrpost-btn" type="submit" value="<?php echo $obj->form->submit->get("Send"); ?>">
            </form>
        </div>
    </div>
</article>
