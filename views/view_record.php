<?php

defined('C5_EXECUTE') or die('Access Denied.');

/** @var array $record */
?>
<div class="search-text-view-record">
    <h3><?php echo t('Record details'); ?></h3>

    <table class="table">
        <?php
        foreach ($record as $column => $value) {
            echo '<tr>';

            echo '<td>' . h($column) . '</td>';

            // This value is escaped in the controller.
            echo '<td>' . $value . '</td>';

            echo '</tr>';
        }
        ?>
    </table>
</div>
