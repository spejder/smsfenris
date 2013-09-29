<?php

ini_set('display_errors', 'on');
error_reporting(E_ALL);

require_once 'init.php';

$conn = DBConnection::get();
$res = $conn->query("select * from personer where patruljenummer < 1 order by personnavn");

if (!$res) die("Der opstod en fejl!");

require_once 'pagehead.php';
?>

<script type="text/javascript">
    $(function () {
        $("#sorted").tablesorter();
    });
</script>

<p>
    <h1>Team</h1>
</p>

<table id="sorted" class="tablesorter">
    <thead>
    <tr>
        <th>
            Navn
        </th>
        <th>
            Teamnavn
        </th>
        <th>
            Funktion
        </th>
        <th>
            Mobilnummer
        </th>
        <th>
            Madvalg
        </th>
        <th>
            Tjek ind tidspunkt
        </th>
    </tr>
    </thead>

    <tbody>

    <?php
    while ($obj = $res->fetch_object()) {

        $checkin = $obj->tjekket ? date("Y.m.d H:i:s", strtotime($obj->tjekket)) : ' - ';

        echo '<tr>';
        echo '<td>' . $obj->personnavn . '</td>';
        echo '<td>' . $obj->teamnavn . '</td>';
        echo '<td>' . $obj->funktion . '</td>';
        echo '<td>' . $obj->mobilnummer . '</td>';
        echo '<td>' . $obj->madvalg . '</td>';
        echo '<td>' . $checkin . '</td>';
        echo '</tr>';

    }

    ?>
    </tbody>
</table>

</body>

</html>
