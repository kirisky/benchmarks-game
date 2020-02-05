<?   // Copyright (c) Isaac Gouy 2004-2020 ?>
<article>
  <div>
    <h1><?=$Title;?></h1>
  </div>
  <section>
    <div>
      <h2>source code</h2>
    </div>
    <pre>
<?=$Code;?>
    </pre>
  </section>
  <section>
    <div>
      <h2 id="log">notes, command-line, and program output</h2>
    </div>
    <pre>
NOTES:
<?=PLATFORM_NAME;?>

<?=$Version;?>

<?=$Log;?>
    </pre>
  </section>
</article>
<footer>
  <nav>
    <ul>
      <li><a href="../license.html"><span>License</span></a>
    </ul>
  </nav>
</footer>
