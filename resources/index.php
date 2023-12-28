<!DOCTYPE html>
<html lang="<?php echo $this->provider()->lang()->prefix(); ?>">
<head>
    <?php echo $this->partial("head")->get(); ?>
</head>
<body>
    <?php echo $this->partial("navigation")->get(); ?>
    <main>
        <?php echo $this->view()->get($args); ?>
    </main>
    <?php echo $this->partial("footer")->get(); ?>
</body>
</html>