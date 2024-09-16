<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>MaplePHP email template</title>
<style type="text/css">
/* Yahoo, outlook, hotmail - Email client improvements */
.yshortcuts, a .yshortcuts, a .yshortcuts:hover, a .yshortcuts:active, a .yshortcuts:focus { background-color:transparent !important; border:none !important; color:inherit !important; }
#outlook a { padding: 0; }
.ReadMsgBody { width: 100%; }
.ExternalClass { width: 100%; }

body { margin: 0px; padding: 0px; }
body, td { color: #000; font-family:Arial,Helvetica,sans-serif; }
table, td { border-collapse:collapse !important; mso-table-lspace:0pt; mso-table-rspace:0pt; }
img { border: none; height: auto; line-height: 100%; outline: none; text-decoration: none; }
a, a[href^="tel"] { color: #0031C4; text-decoration: none; }
a:hover { text-decoration: underline; }
ul li, ol li { padding-bottom: 10px; }
strong, .h2, .h3 { font-weight: bold; }
#main td { width: 570px; }
.center { text-align: center; }
.block { display: block; }
.inline-block, .button  { display: inline-block; }
.h1, .h2, .h3, .h4, .para, .para-2, .para-2 { padding: 0 0 10px 0; }

.h1 {
    font-size: 36px;
    line-height: 40px;
    padding-top: 0;
}

.h2 {
    font-size: 28px;
    line-height: 34px;
}

.h3 {
    font-size: 22px;
    line-height: 26px;
}

.h4 {
    font-size: 12px;
    line-height: 18px;
    letter-spacing: 1px;
    color: #0031C4;
    text-transform: uppercase;
}

.para { font-size: 17px; line-height: 26px; }
.para-2, .button { font-size: 15px; line-height: 24px; }
.para-3, .legend { font-size: 12px; line-height: 20px; }


.title { padding-top: 0; }
.section { padding: 60px 15px; }
.section-2 { padding: 30px 15px; }
.spacer-5 { padding-top: 5px; }
.spacer-10 { padding-top: 10px; }
.spacer-15 { padding-top: 15px; }
.button {
    padding: 12px 20px;
    color: #0031C4;
    border: 1px solid #0031C4;
}
.button:hover { background-color: #0031C4; color: #FFF; text-decoration: none; }
.border-bottom { border-bottom: 1px solid #D3D3D3; }


#logo, #footer { background-color: #f0f0f0; }
#footer, .legend { color: #656565; }
</style>
</head>
<body bgcolor="#FFFFFF">
    <table width="100%" cellspacing="0" cellpadding="0" id="container" bgcolor="#FFFFFF">
        <tr>
            <td id="main" align="center" valign="top">
                
                <table width="600" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td id="logo" class="section-2 center">
                            <div class="h2">[YOUR LOGO HERE]</div>
                        </td>
                    </tr>

                    <?php foreach ($obj->section->fetch() as $row) : ?>
                    <tr>
                        <td class="section-2 border-bottom">
                            <table width="570" border="0" cellspacing="0" cellpadding="0">
                                <?php if ($tag = $row->tagline->isset()) : ?>
                                <tr>
                                    <td class="h4 title"><?php echo $tag; ?></td>
                                </tr>
                                <?php endif; ?>
                                <?php if ($headline = $row->headline->isset()) : ?>
                                <tr>
                                    <td class="h1 title"><?php echo $headline; ?></td>
                                </tr>
                                <?php endif; ?>

                                <?php if ($row->content->count() > 0) : ?>
                                    <?php foreach ($row->content->fetch() as $r) : ?>
                                <tr>
                                        <?php if ($this->isEl($r)) : ?>
                                            <?php echo $r->setElement("td"); ?>

                                        <?php else : ?>
                                    <td class="para-2">
                                            <?php echo $r->get(); ?>
                                    </td>
                                        <?php endif; ?>
                                </tr>
                                    <?php endforeach; ?>

                                <?php else : ?>
                                <tr>
                                    <td class="para-2">
                                        <?php echo $row->content->get("Fill in content"); ?>
                                    </td>
                                </tr>
                                <?php endif; ?>
                                <?php if (isset($row->button['url'])) : ?>
                                <tr>
                                    <td class="spacer-15">
                                        <a class="button" href="<?php echo $row->button['url']; ?>" target="_blank"><?php echo $row->button->title->get("Fill in button[title]"); ?></a>
                                        <?php echo $row->button->legend->sprint("<div class=\"legend spacer-10\">%s</div>")->get(); ?>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </table>
                        </td>
                    </tr>
                    <?php endforeach; ?>

                    <?php if ($obj->footer->count() > 0) : ?>
                    <tr>
                        <td id="footer" class="section-2">
                            <table width="570" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td class="para-3 center">
                                        <?php echo $obj->footer->headline->sprint("<strong>%s</strong><br>")->get(); ?>
                                        <?php echo $obj->footer->content->get(); ?>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <?php endif; ?>
                </table>                
            </td>
        </tr>
    </table>
</body>
</html>