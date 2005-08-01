<h3><strong><?php echo $last_article; ?></strong> subjects parsed</h3>

<table border="0" cellspacing="10">
  <tr valign="top">
    <td valign="top">
      <table border='0'>
        <tr>
          <th colspan="5" align="center">Total Editors Stats</th>
        </tr>
        <tr>
          <th>user</th>
          <th>deleted</th>
          <th>rejected</th>
          <th>modified</th>
          <th>total</th>
        </tr>
        <?php
        $bg = '#EBEBEB';
        foreach ($data as $id => $c) {
          ?>
          <tr bgcolor="<?php echo $bg = ($bg == '#EBEBEB') ? '#BEBEBE' : '#EBEBEB'; ?>">
            <td><?php echo $id ?></td>
            <td><?php echo isset($c['deleted']) ? $c['deleted'] : '0'; ?></td>
            <td><?php echo isset($c['rejected']) ? $c['rejected'] : '0'; ?></td>
            <td><?php echo isset($c['modified']) ? $c['modified'] : '0'; ?></td>
            <td><?php echo $total[$id]; ?></td>
          </tr>
          <?php
        }
        ?>
      </table>
    </td>
    <td valign="top">
      Last half year (with more than <?php echo $minact; ?> actions counted)
      <table border='0'>
        <tr>
          <th colspan="5" align="center">Recent Editors stats</th>
        </tr>
        <tr>
          <th>user</th>
          <th>deleted</th>
          <th>rejected</th>
          <th>modified</th>
          <th>total</th>
        </tr>
        <?php
        $bg = '#EBEBEB';
        foreach ($data_new as $id => $c) {
          if($c['total'] >= $minact) {
            ?>
            <tr bgcolor="<?php echo  $bg = ($bg == '#EBEBEB') ? '#BEBEBE' : '#EBEBEB'; ?>">
              <td><?php echo $id; ?></td>
              <td><?php echo isset($c['deleted']) ? $c['deleted'] : '0'; ?></td>
              <td><?php echo isset($c['rejected']) ? $c['rejected'] : '0'; ?></td>
              <td><?php echo isset($c['modified']) ? $c['modified'] : '0'; ?></td>
              <td><?php echo $c['total']; ?></td>
            </tr>
            <?php
          }
        }
        ?>
      </table>
      <br />
      <table border='0'>
        <tr>
          <th colspan="3" align="center">Editors top 15</th>
        </tr>
        <tr>
          <th>rank</th>
          <th>user</th>
          <th>total</th>
        </tr>
        <?php
        $i = 0;
        foreach($total as $id => $val) {
          ?>
          <tr bgcolor="<?php echo $bg = ($bg == '#EBEBEB') ? '#BEBEBE' : '#EBEBEB'; ?>">
            <td><?php echo ++$i; ?></td>
            <td><?php echo $id; ?></td>
            <td><?php $val; ?></td>
          </tr>
          <?php
          if ($i == 15) {
            break;
          }
        }
        ?>
      </table>
      <br />
      <table border='0'>
        <tr>
          <th colspan="3" align="center">Manual pages most active top 20</th>
        </tr>
        <tr>
          <th>rank</th>
          <th>page</th>
          <th>total</th>
        </tr>
        <?php
        $i = 0;
        foreach ($manual as $id => $c) {
          ?>
          <tr bgcolor="<?php echo $bg = ($bg == '#EBEBEB') ? '#BEBEBE' : '#EBEBEB'; ?>">
            <td><?php echo ++$i ?></td>
            <td><?php echo $id ?></td>
            <td><?php $c; ?></td>
          </tr>
          <?php
          if ($i == 20) {
            break;
          }
        }       
        ?>
      </table>
    </td>
  </tr>
</table>
&docweb.notes-stats.last-updated; <?php echo $build_date; ?><br />
<br />
