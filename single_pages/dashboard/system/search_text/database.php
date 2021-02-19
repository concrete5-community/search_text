<?php

defined('C5_EXECUTE') or die('Access Denied.');

/** @var array $tableOptions */
?>
<div class="ccm-dashboard-content-inner">
    <form id="search-text-form" method="post" action="<?php echo $this->action('execute'); ?>">
        <?php
        /** @var \Concrete\Core\Validation\CSRF\Token $token */
        echo $token->output('a3020.search_text');
        ?>

        <div class="form-group">
            <?php
            echo $form->label('searchFor', t('Search for') . ' *');
            echo $form->text('searchFor', null, [
                'autofocus' => 'autofocus',
                'required' => 'required',
                'minlength' => 2,
            ]);
            ?>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <?php
                    echo $form->label('tablesIncluded', t('Tables to search in'));
                    ?>
                    <div style="width: 100%">
                        <?php
                        echo $form->selectMultiple('tablesIncluded', $tableOptions, null, [
                            'style' => 'width: 100%',
                            'placeholder' => t('Leave empty to search in all tables'),
                        ]);
                        ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <?php
                    echo $form->label('tablesExcluded', t('Tables not to search in'));
                    ?>
                    <div style="width: 100%">
                        <?php
                        echo $form->selectMultiple('tablesExcluded', $tableOptions, null, [
                            'style' => 'width: 100%',
                        ]);
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="ccm-dashboard-form-actions">
            <button class="btn btn-primary" type="submit">
                <?php echo t('Search!') ?>
            </button>
        </div>
    </form>

    <div id="search-text-results-container" class="hide">
        <hr>

        <div id="search-text-progress"></div>
        <div id="search-text-results"></div>
    </div>
</div>

<script type="text/javascript">
$(function() {
    $('#tablesIncluded, #tablesExcluded')
        .removeClass('form-control')
        .select2();
});
</script>
