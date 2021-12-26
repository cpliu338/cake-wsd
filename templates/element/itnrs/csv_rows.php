<?php /*
$start_serial: 0 (default)
$rows: [
    ['colA', 'colB', ...],
    ['colA', 'colB', ...],
]
*/
$serial = $start_serial ?? 0;
?>
<?php foreach ($rows as $cols):?>
    <tr><?php /* data-serial="< =$serial? >"><td>Del</td> */?>
    <?php foreach ($cols as $col):?>
        <td><?=h($col)?></td>
    <?php endforeach;?>
    <?php $serial++;?>
    </tr>
<?php endforeach;?>