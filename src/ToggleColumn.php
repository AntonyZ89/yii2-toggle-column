<?php

/**
 * @package   yii2-toggle-column
 * @author    Antony Gabriel <antonyz.dev@gmail.com>
 * @version   1.0.0
 */

namespace antonyz89\togglecolumn;

use antonyz89\togglecolumn\models\ToggleColumn as ModelsToggleColumn;
use Exception;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\grid\ActionColumn;
use yii\grid\DataColumn;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/**
 * Toggle column widget.
 *
 * @author Antony Gabriel <antonyz.dev@gmail.com>
 * @since 1.0.0
 */
class ToggleColumn extends Widget
{

    use BootstrapTrait;
    use ConfigTrait;

    /**
     * @var string
     */
    public $model;

    /**
     * @var string
     */
    public $table_id;

    public $options = [];
    public $disabledColumns = [];
    public $columns = [];
    public $columnSelectorOptions = [];
    public $columnSelectorMenuOptions = [];

    public $toggleColumnView;

    /**
     * @var array the settings for the toggle all checkbox to check / uncheck the columns as a batch. Should be setup as
     * an associative array which can have the following keys:
     * - `show`: _boolean_, whether the batch toggle checkbox is to be shown. Defaults to `true`.
     * - `label`: _string_, the label to be displayed for toggle all. Defaults to `Select Columns`.
     * - `options`: _array_, the HTML attributes for the toggle label text. Defaults to `['class'=>'tc-toggle-all']`
     */
    public $columnBatchToggleSettings = [
        'options' => ['class' => 'tc-toggle-all'],
    ];

    /**
     * @var array, HTML attributes for the container to wrap the widget. Defaults to:
     * `['class' => 'btn-group', 'role' => 'group']`
     */
    public $container = ['class' => 'btn-group', 'role' => 'group'];


    /**
     * @var boolean whether to show a column selector to select columns visible. Defaults to `true`.
     * This is applicable only if [[asDropdown]] is set to `true`. Else this property is ignored.
     */
    public $showColumnSelector = true;

    /**
     * @var array the configuration of the column names in the column selector. Note: column names will be generated
     * automatically by default. Any setting in this property will override the auto-generated column names. This
     * list should be setup as `$key => $value` where:
     * - `$key`: _integer_, is the zero based index of the column as set in `$columns`.
     * - `$value`: _string_, is the column name/label you wish to display in the column selector.
     */
    public $columnSelector = [];

    /**
     * @var array the selected column indexes for show. If not set this will default to all columns.
     */
    public $selectedColumns;

    public $dataColumnClass = '\yii\grid\DataColumn';

    /**
     * @var array the visible columns
     */
    protected $_visibleColumns;

    /**
     * @var boolean whether the column selector is enabled
     */
    protected $_columnSelectorEnabled;

    /**
     * @var ActiveRecord
     */
    protected $_model;

    /**
     * @inheritdoc
     */
    public function init()
    {
        if (!$this->model) {
            throw new InvalidConfigException("The 'model' property must be set.");
        }

        $this->initModule(Module::class);
        $this->initSettings();
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->initColumns();
        $this->initSelectedColumns();
        $this->initColumnSelector();
        $this->setVisibleColumns();
        $this->registerAssets();
    }

    /**
     * Initialize column selector list
     */
    protected function initColumnSelector()
    {
        $selector = [];
        Html::addCssClass($this->columnSelectorOptions, ['btn', $this->getDefaultBtnCss(), 'dropdown-toggle toggle-column-button']);
        $header = ArrayHelper::getValue($this->columnSelectorOptions, 'header', Yii::t('tc', 'Select Columns'));
        $this->columnSelectorOptions['header'] = (!isset($header) || $header === false) ? '' :
            '<li class="dropdown-header">' . $header . '</li><li class="kv-divider"></li>';
        $id = $this->options['id'] . '-cols';
        Html::addCssClass($this->columnSelectorMenuOptions, 'dropdown-menu kv-checkbox-list toggle-column-list');
        $this->columnSelectorMenuOptions = array_replace_recursive(
            [
                'id' => "$id-list",
                'role' => 'menu',
                'aria-labelledby' => $id,
            ],
            $this->columnSelectorMenuOptions
        );
        $this->columnSelectorOptions = array_replace_recursive(
            [
                'id' => $id,
                'icon' => !$this->isBs(3) ? '<i class="fas fa-list"></i>' : '<i class="glyphicon glyphicon-list"></i>',
                'title' => Yii::t('tc', 'Visible Columns'),
                'type' => 'button',
                'data-toggle' => 'dropdown',
                'aria-haspopup' => 'true',
                'aria-expanded' => 'false',
            ],
            $this->columnSelectorOptions
        );
        foreach ($this->columns as $column) {
            if (!isset($column->attribute)) {
                throw new InvalidConfigException("The 'attribute' property must be set for each column");
            }

            $selector[$column->attribute] = $this->getColumnLabel($column->attribute, $column);
        }

        $this->columnSelector = array_replace($selector, $this->columnSelector);

        if (!isset($this->selectedColumns)) {
            $keys = array_keys($this->columnSelector);
            $this->selectedColumns = array_combine($keys, $keys);
        }

        echo $this->renderColumnSelector();
    }

    /**
     * Sets visible columns
     */
    public function setVisibleColumns()
    {
        $columns = [];
        foreach ($this->columns as $key => $column) {
            $isActionColumn = $column instanceof ActionColumn;

            $isDisabled = in_array($key, $this->disabledColumns) ||
                ($this->showColumnSelector && is_array($this->selectedColumns) && !in_array(
                    $key,
                    $this->selectedColumns
                ));

            if ($isActionColumn && !$isDisabled) {
                $this->disabledColumns[] = $key;
            }

            if ($isActionColumn || $isDisabled) {
                continue;
            }

            $columns[] = $column;
        }
        $this->_visibleColumns = $columns;
    }

    /**
     * Initialize menu settings
     */
    public function initSettings()
    {
        if (empty($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }

        $this->_columnSelectorEnabled = $this->showColumnSelector;

        $path = '@antonyz89/togglecolumn/views';
        if (!isset($this->toggleColumnView)) {
            $this->toggleColumnView = "{$path}/_columns";
        }
    }


    /**
     * Fetches the column label
     *
     * @param  string  $key
     * @param  Column  $column
     *
     * @return string
     */
    protected function getColumnLabel($key, $column)
    {
        $label = $key;

        if (isset($column->label)) {
            $label = $column->label;
        } elseif (isset($column->header)) {
            $label = $column->header;
        } elseif (isset($column->attribute)) {
            $label = $this->getModel()->getAttributeLabel($column->attribute);
        } elseif (!$column instanceof DataColumn) {
            $label = $column;
        }
        return trim(strip_tags(str_replace(['<br>', '<br/>'], ' ', $label)));
    }

    /**
     * Creates column objects and initializes them.
     */
    protected function initColumns()
    {
        foreach ($this->columns as $i => $column) {
            if (is_string($column)) {
                $column = $this->createDataColumn($column);
            } else {
                $column = Yii::createObject(array_merge([
                    'class' => $this->dataColumnClass ?: DataColumn::class,
                    'grid' => $this,
                ], $column));
            }
            if (!$column->visible) {
                unset($this->columns[$i]);
                continue;
            }
            $this->columns[$i] = $column;
        }
    }

    /**
     * Creates a [[DataColumn]] object based on a string in the format of "attribute:format:label".
     * @param string $text the column specification string
     * @return DataColumn the column instance
     * @throws InvalidConfigException if the column specification is invalid
     */
    protected function createDataColumn($text)
    {
        if (!preg_match('/^([^:]+)(:(\w*))?(:(.*))?$/', $text, $matches)) {
            throw new InvalidConfigException('The column must be specified in the format of "attribute", "attribute:format" or "attribute:format:label"');
        }

        return Yii::createObject([
            'class' => $this->dataColumnClass ?: DataColumn::class,
            'grid' => $this,
            'attribute' => $matches[1],
            'format' => isset($matches[3]) ? $matches[3] : 'text',
            'label' => isset($matches[5]) ? $matches[5] : null,
        ]);
    }

    /**
     * Renders the columns selector
     *
     * @return string the column selector markup
     * @throws Exception
     */
    public function renderColumnSelector()
    {
        if (!$this->_columnSelectorEnabled) {
            return '';
        }

        return $this->render(
            $this->toggleColumnView,
            [
                'id' => $this->options['id'],
                'notBs3' => !$this->isBs(3),
                'isBs4' => $this->isBs(4),
                'options' => $this->columnSelectorOptions,
                'menuOptions' => $this->columnSelectorMenuOptions,
                'columnSelector' => $this->columnSelector,
                'batchToggle' => $this->columnBatchToggleSettings,
                'selectedColumns' => $this->selectedColumns,
                'disabledColumns' => $this->disabledColumns,
            ]
        );
    }

    /**
     * Registers client assets needed for Toggle Column widget
     * @throws Exception
     */
    protected function registerAssets()
    {
        $url = Url::to($this->_module->saveAction);

        $tableTarget = $this->table_id ? '#' . $this->table_id : 'table';

        $js = <<< JS
            let debounceSave = null;
            const tcId = '{$this->options['id']}';
            const table = $('{$tableTarget}');

            const dropdown = $(`#\${tcId}-cols-list`);
            const toggleCb = dropdown.find(`#tc_columns_toggle_\${tcId}`);
            const columns = dropdown.find('[name="tc_selector[]"]');

            dropdown.off('click').click(e => e.stopPropagation());

            dropdown.find(`#tc_columns_toggle_\${tcId}`).click(function () {
                const checked = $(this).prop('checked');

                columns.prop('checked', checked).change();
            });

            // TODO: resize table when change() is trigged
            columns.change(function () {
                const selectedRow = $(this).closest('li');
                const selectedRowIndex = selectedRow.index();
                const dividerIndex = selectedRow.parent().find('li.dropdown-divider, li.divider').index();

                const index = selectedRowIndex - dividerIndex;

                const header = table.find(`thead > tr > th:nth-child(\${index})`).toggle(this.checked);
                const row = $(`tr > td:nth-child(\${index})`);

                !row.find('div.empty').length && row.toggle(this.checked);

                const allChecked = columns.toArray().every(el => el.checked);

                toggleCb.prop('checked', Number(allChecked));
            });

            // trigger change to hide columns
            columns.change();

            // add change event to save columns on db
            columns.change(function () {
                const selectedColumns = columns.filter((index, el) => !el.id.includes('toggle') && el.checked).map((i, el) => el.id)

                saveColumns([...selectedColumns]);
            })

            function saveColumns(selectedColumns) {
                clearTimeout(debounceSave);
                debounceSave = setTimeout(() => {
                    $.post('$url', {
                        columns: selectedColumns,
                        table: '{$this->getTable()}'
                    });
                }, 100);
            }
        JS;

        $this->view->registerJs($js, View::POS_READY);

        $css = <<< CSS
            .toggle-column-button {
                padding: 0.4rem 1.1rem;
                margin-left: 0.5rem;
            }

            .toggle-column-list {
                padding: 0.6rem;
            }
        CSS;

        $this->view->registerCss($css);
    }

    /**
     * @return string
     */
    public function getTable()
    {
        return $this->model ? preg_replace('/{{%?(.+)%?}}/', '$1', $this->model::tableName()) : str_replace('-', '_', Yii::$app->controller->id);
    }

    /**
     * @return ActiveRecord
     */
    public function getModel()
    {
        if (!$this->_model) {
            $this->_model = new $this->model;
        }

        return $this->_model;
    }

    public function initSelectedColumns()
    {
        $db = ModelsToggleColumn::find()->whereUser(Yii::$app->user->id)->whereTable($this->getTable())->one();
        $db = $db ? json_decode(stripslashes($db->columns)) : null;

        if ($db !== null) {
            $this->selectedColumns = $db;
        }
    }
}
