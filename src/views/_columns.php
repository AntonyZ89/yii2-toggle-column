<?php

/**
 * @package   yii2-toggle-column
 * @author    Antony Gabriel <antonyz.dev@gmail.com>
 * @version   1.0.0
 *
 * Column Selector View
 */

use yii\helpers\Html;
use yii\helpers\ArrayHelper;

/**
 * @var int $id
 * @var bool $notBs3
 * @var bool $isBs4
 * @var array $options
 * @var array $batchToggle
 * @var array $columnSelector
 * @var array $selectedColumns
 * @var array $disabledColumns
 * @var array $menuOptions
 */

$label = ArrayHelper::remove($options, 'label');
$icon = ArrayHelper::remove($options, 'icon');
$showToggle = ArrayHelper::remove($batchToggle, 'show', true);
if (!empty($icon)) {
    $label = $icon . ' ' . $label;
}
echo Html::beginTag('div', ['class' => 'btn-group', 'role' => 'group']);
echo Html::button($label . ' <span class="caret"></span>', $options);

$cbxContCss = 'checkbox';
$cbxCss = '';
$cbxLabelCss = '';
if ($notBs3) {
    $cbxContCss = $isBs4 ? 'custom-control custom-checkbox' : 'form-check';
    $cbxCss = $isBs4 ? 'custom-control-input' : 'form-check-input';
    $cbxLabelCss = $isBs4 ? 'custom-control-label' : 'form-check-label';
}
$cbxToggle = 'tc_columns_toggle';
$cbxToggleId = $cbxToggle . '_' . $id;
echo Html::beginTag('ul', $menuOptions);
?>

<?php
if ($showToggle) : ?>
    <?php
    $toggleOptions = ArrayHelper::remove($batchToggle, 'options', []);
    $toggleLabel = ArrayHelper::remove($batchToggle, 'label', Yii::t('tc', 'Select Columns'));
    Html::addCssClass($toggleOptions, 'kv-toggle-all');
    ?>
    <li>
        <?php
        echo Html::beginTag('div', ['class' => $cbxContCss]);
        $cbx = Html::checkbox($cbxToggle, true, ['class' => $cbxCss, 'id' => $cbxToggleId]);
        $lab = Html::tag('span', $toggleLabel, $toggleOptions);
        if ($notBs3) {
            echo $cbx . "\n" . Html::label($lab, $cbxToggleId, ['class' => $cbxLabelCss]);
        } else {
            echo Html::label($cbx . "\n" . $lab, $cbxToggleId, ['class' => $cbxLabelCss]);
        }
        echo Html::endTag('div');
        ?>
    </li>
    <li class="<?= $notBs3 ? 'dropdown-' : '' ?>divider"></li>
<?php
endif; ?>

<?php
$i = 1;
foreach ($columnSelector as $value => $label) {
    $checked = in_array($value, $selectedColumns);
    $disabled = in_array($value, $disabledColumns);
    $cbxId = $value;
    $labCss = $cbxLabelCss;
    if ($disabled) {
        $labCss .= ' disabled';
    }
    echo Html::beginTag('li');
    echo Html::beginTag('div', ['class' => $cbxContCss]);
    $cbx = Html::checkbox(
        'tc_selector[]',
        $checked,
        ['id' => $cbxId, 'class' => $cbxCss, 'data-key' => $value, 'disabled' => $disabled]
    );
    if ($notBs3) {
        echo $cbx . "\n" . Html::label($label, $cbxId, ['class' => $labCss]);
    } else {
        echo Html::label($cbx . "\n" . $label, $cbxId, ['class' => $labCss]);
    }
    echo Html::endTag('div');
    echo Html::endTag('li');
    $i++;
}
echo Html::endTag('ul');
echo Html::endTag('div');
?>