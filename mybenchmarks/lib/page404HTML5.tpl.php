<?php
ob_start();
header("HTTP/1.1 404 Not Found");
?>
<!DOCTYPE html>
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="robots" content="noindex,nofollow,noarchive"/>
<title>404 Not Found | My Benchmarks</title>
<link rel="shortcut icon" href="./favicon.ico" />
<style><!-- 
a{color:black;text-decoration:none}article,footer,header{margin:auto;max-width:31em;width:92%}body{font:100% Droid Sans,Ubuntu,Verdana,sans-serif;margin:0;-webkit-text-size-adjust:100%}h1,h2{font-family:Ubuntu Mono,Consolas,Menlo,monospace}footer{padding:2.6em 0 0}h1{font-size:1.4em;font-weight:bold;margin:0;padding:.4em}h1,h1 a{color:white}h2{margin:1.5em 0 0}h2{font-size:1.4em;font-weight:normal}p{color:#333;line-height:1.4;margin:.3em 0 0}p a{border-bottom:.1em solid #333;padding-bottom:.1em}#core{background-color:blue}#linux{background-color:green}#macbook{background-color:brown}@media only screen and (min-width:60em){article,footer,header{font-size:1.25em}}
--></style>
<header id="top">
  <h1 id="<?=SITE_NAME;?>"><a href="<?=CORE_SITE;?>"><?=PLATFORM_NAME;?> | My Benchmarks</h1>
</header>
<article>
  <header>
    <h1>404 NOT FOUND</h1>
  </header>
  <p>SORRY, THE REQUESTED INFORMATION IS NOT AVAILABLE.
</article> 
<?php
 ob_end_flush();
?>
