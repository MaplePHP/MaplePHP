<!DOCTYPE html>
<html lang="<?php echo getenv("APP_LANG"); ?>">
<head>
    <meta charset="<?php echo getenv("APP_CHARSET"); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php if ($obj->container->has("head")) {
        echo $obj->container->get("head")->execute();
    } ?>
    <link rel="stylesheet" media="all" href="<?php echo $obj->container->get("url")->getCss("style.css?bundle=" . getenv("APP_BUNDLE")); ?>">
</head>
<body>
    <?php echo $this->partial("navigation")->get(); ?>
    <main>
        <div id="ingress"></div>
        <div id="ingress2"></div>
        <?php echo $this->view()->get($args); ?>
    </main>
    <footer></footer>
    <div id="loading"></div>
    <?php if ($obj->container->has("foot")) {
        echo $obj->container->get("foot")->execute();
    } ?>
    
    <script nonce="<?php echo getenv("NONCE"); ?>" type="module" src="<?php echo $obj->container->get("url")->getJs("main.js", (bool)(getenv("APP_ENV") === "production")); ?>"></script>
</body>
</html>