<?php

ini_set('display_errors', 'on');
error_reporting(E_ALL);

require_once 'init.php';

$conn = DBConnection::get();
$res = $conn->query("select * from personer where LOWER(funktion) = 'pl' group by patruljenummer");

if (!$res) die("Der opstod en fejl!");

require_once 'pagehead.php';
?>

<script type="text/javascript">
    $(function () {
        $("#sorted").tablesorter();
    });
</script>

<table id="sorted" class="tablesorter">
    <thead>
    <tr>
        <th>
            Patruljenummer
        </th>
        <th>
            Patruljenavn
        </th>
        <th>
            PL
        </th>
        <th>
            PL mobilnummer
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
        echo '<td>' . $obj->patruljenummer . '</td>';
        echo '<td>' . $obj->patruljenavn . '</td>';
        echo '<td>' . $obj->personnavn . '</td>';
        echo '<td>' . $obj->mobilnummer . '</td>';
        echo '<td>' . $checkin . '</td>';
        echo '</tr>';

    }

    ?>
    </tbody>
</table>

</body>

</html>
