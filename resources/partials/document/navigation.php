
<header id="header" class="flex items-center p-40 lg:p-15 z-50 w-12 border-box bg-white border-bottom">
    <figure class="logo pr-40 scale">
        <a class="headline-4 m-0 color-primary" href="#" title="Skrolla till toppen">
            MaplePHP
        </a>
    </figure>
    <nav id="nav" class="ml-auto border-box trans-1 ease-out">
        <div class="scroller border-box">
            <?php echo $obj->nav->select("main")->get("nav"); ?>
        </div>
    </nav>
    <a id="wa-smart-btn" class="smart-btn ml-auto items rounded-full hide xl:flex relative z-50" href="#" title="Visa navigation">
        <span class="lines block">
            <span class="line block rounded abs"></span>
            <span class="line block rounded abs"></span>
        </span>
    </a>
</header>
