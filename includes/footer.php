<footer>
</footer>
<!-- Compiled and minified Javascript for jQuery -->
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<!-- Compiled and minified JavaScript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/js/materialize.min.js"></script>
<!-- Compiled and minified Javascript for ChartJS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js"></script>
<script type="text/javascript" src="scripts/materialize.min.js"></script>
<script src="scripts/common.js"></script>
<script src="scripts/caja.js"></script>
<?php
if (isset($scripts) && is_array($scripts)) {
  foreach ($scripts as $script) {
    if (file_exists("scripts/" . $script . ".js")) {
      print '<script defer src="scripts/' . $script . '.js"></script>';
    }
  }
}
?>
</body>

</html>