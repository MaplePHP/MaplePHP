
<footer id="footer" class="p-15 align-center legend" role="contentinfo">
    <?php echo $this->provider()->date()->format("Y"); ?> <?php echo $this->provider()->env("APP_NAME"); ?>
</footer>
<div id="loading"></div>

<?php 
// Provider foot will make it possible to communicate with the frontend code!
if ($this->provider()->has("foot")) {
    echo $this->provider()->get("foot")->execute();
}
// Get url to development or production javascript file
$src = $this->provider()->get("url")->getJs("main.js", $this->provider()->env("APP_ENV")->compare("production"));
?>
<script nonce="<?php echo getenv("NONCE"); ?>" type="module" src="<?php echo $src; ?>"></script>