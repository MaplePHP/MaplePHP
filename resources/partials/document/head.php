<meta charset="<?php echo $this->provider()->env("APP_CHARSET"); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php if ($this->provider()->has("head")) {
    echo $this->provider()->get("head")->execute();
} ?>
<link rel="stylesheet" media="all" href="<?php echo $this->provider()->get("url")->getCss("style.css?bundle=" . getenv("APP_BUNDLE")); ?>">