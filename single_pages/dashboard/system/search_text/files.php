<?php

defined('C5_EXECUTE') or die('Access Denied.');

/** @var string $includedDirectories */
/** @var string $excludedDirectories */
/** @var array $fileTypeOptions */
?>
<div class="ccm-dashboard-content-inner">
    <form id="search-text-form" method="post" action="<?php echo $this->action('execute'); ?>">
        <?php
        /** @var \Concrete\Core\Validation\CSRF\Token $token */
        echo $token->output('a3020.search_text.files');
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
                    echo $form->label('excludedDirectories', t('Directories to exclude'));
                    echo $form->textarea('excludedDirectories', $excludedDirectories, [
                        'placeholder' => t('One directory per line'),
                        'rows' => 5,
                    ]);
                    ?>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <?php
                    echo $form->label('fileType', t('Only search file type'));
                    echo $form->select('fileType', $fileTypeOptions);
                    ?>
                </div>

                <div class="form-group">
                    <?php
                    echo $form->label('limitResults', t('Limit number of results to'));
                    echo $form->number('limitResults', 50);
                    ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="checkbox">
                <label>
                    <?php
                    echo $form->checkbox('dummy', 1, 1, [
                        'disabled' => 'disabled',
                    ]);
                    ?>
                    <?php echo t('Search in file name'); ?>
                </label>
            </div>

            <div class="checkbox">
                <label>
                    <?php
                    echo $form->checkbox('searchIn', 1);
                    ?>
                    <?php echo t('Search in file content'); ?>
                </label>
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
