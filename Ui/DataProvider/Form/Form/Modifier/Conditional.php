<?php
/**
 * Cytracon
 *
 * This source file is subject to the Cytracon Software License, which is available at https://www.cytracon.com/license.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://www.cytracon.com for more information.
 *
 * @category  BlueFormBuilder
 * @package   BlueFormBuilder_ConditionalLogic
 * @copyright Copyright (C) 2019 Cytracon (https://www.cytracon.com)
 */

namespace BlueFormBuilder\ConditionalLogic\Ui\DataProvider\Form\Form\Modifier;

use Magento\Ui\Component\Form\Fieldset;
use Magento\Ui\Component\Form\Element\ActionDelete;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Cytracon\UiBuilder\Data\Form\Element\Factory;
use Cytracon\UiBuilder\Data\Form\Element\CollectionFactory;
use \Cytracon\BlueFormBuilderCore\Helper\Data as DataHelper;

class Conditional extends \Cytracon\UiBuilder\Ui\DataProvider\Form\AbstractModifier
{
    const FIELD_ENABLE                      = 'affect_conditional';
    const CONDITIONS                        = 'conditions';
    const GROUP_CONDITIONAL_CONTAINER_SCOPE = 'conditions_container';
    const GROUP_CONDITIONAL_SCOPE           = 'data';
    const CONTAINER_HEADER_NAME             = 'container_header';
    const CONTAINER_FOOTER_NAME             = 'container_footer';
    const CONTAINER_CONDITIONAL             = 'container_conditional';
    const BUTTON_ADD                        = 'button_add';
    const GROUP_CONDITIONAL_NAME            = 'conditional';
    const FIELD_VALUE_NAME                  = 'value';
    const FIELD_FIELD_NAME                  = 'field';
    const FIELD_OPERATOR_NAME               = 'operator';
    const FIELD_AGGREGATOR_NAME             = 'aggregator';
    const FIELD_ACTION_NAME                 = 'action';
    const FIELD_SORT_ORDER_NAME             = 'sort_order';
    const FIELD_IS_DELETE                   = 'is_delete';
    const FIELD_ACTION_FIELD_NAME           = 'action_field';
    const FIELD_APPLY_FIELD_NAME            = 'apply_field';
    const ACTION_SHOW_FIELDS                = 'sf';
    const ACTION_HIDE_FIELDS                = 'hf';
    const ACTION_SEND_EMAIL_TO              = 'set';
    const ACTION_REDIRECT_TO                = 'rt';
    const ACTION_SET_VALUE_OF               = 'svo';
    const AGGREGATOR_AND                    = 'and';
    const AGGREGATOR_OR                     = 'or';

    /**
     * @var array
     */
    protected $meta = [];

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Cytracon\BlueFormBuilderCore\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $layoutFactory;

    /**
     * @var \Cytracon\Core\Helper\Data
     */
    protected $coreHelper;

    /**
     * @param Factory                           $factoryElement    
     * @param CollectionFactory                 $factoryCollection 
     * @param \Magento\Framework\Registry       $registry          
     * @param \Cytracon\BlueFormBuilderCore\Helper\Data $dataHelper        
     */
    public function __construct(
        Factory $factoryElement,
        CollectionFactory $factoryCollection,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Cytracon\Core\Helper\Data $coreHelper,
        \Cytracon\BlueFormBuilderCore\Helper\Data $dataHelper
    ) {
        parent::__construct($factoryElement, $factoryCollection);
        $this->layoutFactory = $layoutFactory;
        $this->registry      = $registry;
        $this->coreHelper    = $coreHelper;
        $this->dataHelper    = $dataHelper;
    }

    /**
     * @return \Cytracon\BlueFormBuilderCore\Model\Form
     */
    public function getCurrentForm()
    {
        return $this->registry->registry('current_form');
    }

    public function modifyData(array $data)
    {
        if (isset($data['conditional']) && $data['conditional']) {
            $conditional = $this->coreHelper->unserialize($data['conditional']);
            if (is_array($conditional)) {
                foreach ($conditional as &$row) {
                    if (isset($row['conditions']) && is_array($row['conditions']) && count($row['conditions']) >= 1 && isset($row['conditions'][0]['aggregator'])) {
                        $row['aggregator'] = $row['conditions'][0]['aggregator'];
                    }
                }
                $data['conditional'] = $this->coreHelper->serialize($conditional);
            }
        }
        return $data;
    }

    public function modifyMeta(array $meta)
    {
        $this->meta = $meta;

        $this->prepareChildren();

        $this->createConditionaPanel();

        return $this->meta;
    }

    /**
     * Create Conditional panel
     *
     * @return $this
     */
    protected function createConditionaPanel()
    {
        $form = $this->getCurrentForm();
        $this->meta['plugins']['children'] = array_replace_recursive(
            $this->meta['plugins']['children'],
            [
                static::GROUP_CONDITIONAL_CONTAINER_SCOPE => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label'                           => __('Conditional Form Fields'),
                                'componentType'                   => Fieldset::NAME,
                                'dataScope'                       => static::GROUP_CONDITIONAL_SCOPE,
                                'collapsible'                     => true,
                                'initializeFieldsetDataByDefault' => false,
                                'opened'                          => false,
                                'sortOrder'                       => 0,
                                'additionalClasses'               => 'bfb-conditional'
                            ]
                        ]
                    ],
                    'children' => $this->getChildren()
                ]
            ]
        );

        return $this;
    }

    public function prepareChildren()
    {
            $this->addChildren(static::GROUP_CONDITIONAL_NAME, 'textarea', [
                'dataScope'         => static::GROUP_CONDITIONAL_NAME,
                'additionalClasses' => 'admin__field-wide',
                'sortOrder'         => 30,
                'component'         => 'BlueFormBuilder_ConditionalLogic/js/logic',
                'content'           => $this->layoutFactory->create()->createBlock('\BlueFormBuilder\ConditionalLogic\Block\Adminhtml\Logic')->setTemplate('BlueFormBuilder_ConditionalLogic::logic.phtml')->toHtml()
            ]);

        return;

        $this->addContainer(
            static::CONTAINER_HEADER_NAME,
            [
                'label'             => null,
                'sortOrder'         => 10,
                'template'          => 'ui/form/components/complex',
                'additionalClasses' => 'bfb-formbuilder-conditional-header',
                'content'           => '<div class="bfb-formbuilder-conditional-header-left"><span>Conditions</span><i class="fa fa-arrow-down" aria-hidden="true"></i></div><div class="bfb-formbuilder-conditional-header-right"><span>Actions</span><i class="fa fa-arrow-down" aria-hidden="true"></i></div>'
            ]
        );

        $container1 = $this->addChildren(static::GROUP_CONDITIONAL_NAME, 'dynamicRows', [
            'component'           => 'BlueFormBuilder_ConditionalLogic/js/conditional',
            'template'            => 'BlueFormBuilder_Core/conditional',
            'additionalClasses'   => 'admin__field-wide bfb-formbuilder-conditional',
            'deleteProperty'      => 'condition_delete',
            'deleteValue'         => '1',
            'addButton'           => false,
            'renderDefaultRecord' => false,
            'columnsHeader'       => false,
            'collapsibleHeader'   => true,
            'sortOrder'           => 20
        ]);

            $this->prepareConditionChildren($container1);

        $container2 = $this->addContainer(
            static::CONTAINER_FOOTER_NAME,
            [
                'label'             => null,
                'template'          => 'ui/form/components/complex',
                'sortOrder'         => 30,
                'additionalClasses' => 'bfb-formbuilder-conditional-footer'
            ]
        );

            $container2->addChildren(
                static::BUTTON_ADD,
                'button',
                [
                    'title'   => __('Add New Logic'),
                    'actions' => [
                        [
                            'targetName' => 'ns = ${ $.ns }, index = ' . static::GROUP_CONDITIONAL_NAME,
                            'actionName' => 'processingAddChild'
                        ]
                    ]
                ]
            );
            
        $this->addChildren(
            static::FIELD_ENABLE,
            'boolean', [
                'sortOrder' => 100,
                'visible'   => false
            ]
        );
    }

    protected function prepareConditionChildren($condition)
    {
        $container2 = $condition->addContainer('record', [
            'headerLabel'   => __('New Condition'),
            'component'     => 'Magento_Ui/js/dynamic-rows/record',
            'isTemplate'    => true,
            'is_collection' => true
        ]);

            $container3 = $container2->addFieldset(
                static::CONTAINER_CONDITIONAL,
                [
                    'label'         => null,
                    'sortOrder'     => 10,
                    'opened'        => true,
                    'visible'       => true,
                    'dataScope'     => ''
                ]
            );

                $container3->addFieldset(
                    'if',
                    [
                        'label'             => null,
                        'template'          => 'ui/form/components/complex',
                        'sortOrder'         => 10,
                        'content'           => 'if',
                        'additionalClasses' => 'bfb-formbuilder-conditional-text'
                    ]
                );

                $this->prepareConditions($container3, static::CONDITIONS);

                $container3->addFieldset(
                    'then',
                    [
                        'label'             => null,
                        'template'          => 'ui/form/components/complex',
                        'sortOrder'         => 30,
                        'content'           => 'then',
                        'additionalClasses' => 'bfb-formbuilder-conditional-text'
                    ]
                );

                $this->prepareActions($container3);
                
                $container3->addChildren('condition_delete', 'actionDelete', [
                    'fit'       => true,
                    'sortOrder' => 50
                ]);
    }

    public function prepareConditions($parent, $elementId, $additionalConfig = [])
    {
        $config = [
            'addButtonLabel'      => __('Add Condition Row'),
            'template'            => 'BlueFormBuilder_Core/conditional',
            'additionalClasses'   => 'bfb-formbuilder-conditional-layout-left',
            'deleteProperty'      => static::FIELD_IS_DELETE,
            'deleteValue'         => '1',
            'renderDefaultRecord' => false,
            'sortOrder'           => 20,
            'dataScope'           => '',
            'pageSize'            => 999
        ];

        $config = array_replace_recursive($config, $additionalConfig);

        $container1 = $parent->addChildren($elementId, 'dynamicRows', $config);

            $container2 = $container1->addContainer('record', [
                'component'        => 'Magento_Ui/js/dynamic-rows/record',
                'positionProvider' => static::FIELD_SORT_ORDER_NAME,
                'isTemplate'       => true,
                'is_collection'    => true
            ]);

                $container2->addChildren(static::FIELD_FIELD_NAME, 'select', [
                    'label'             => __('Field Condition'),
                    'component'         => 'BlueFormBuilder_Core/js/element/field',
                    'additionalClasses' => 'bfb-conditional-field',
                    'sortOrder'         => 10,
                    'selectedPlaceholders' => [
                        'defaultPlaceholder' => __('(field)')
                    ],
                    'validation' => [
                        'required-entry' => true
                    ]
                ]);

                $container2->addChildren(static::FIELD_OPERATOR_NAME, 'select', [
                    'label'                => __('Trigger'),
                    'sortOrder'            => 20,
                    'options'              => $this->dataHelper->getOperatorOptions(),
                    'selectedPlaceholders' => false,
                    'value'                => DataHelper::OPERATOR_EQUALS_TO,
                    'validation'           => [
                        'required-entry' => true
                    ],
                    'groupsConfig' => [
                        DataHelper::OPERATOR_EQUALS_TO => [
                            static::FIELD_VALUE_NAME
                        ],
                        DataHelper::OPERATOR_NOT_EQUALS_TO => [
                            static::FIELD_VALUE_NAME
                        ],
                        DataHelper::OPERATOR_GREATER_THAN => [
                            static::FIELD_VALUE_NAME
                        ],
                        DataHelper::OPERATOR_LESS_THAN => [
                            static::FIELD_VALUE_NAME
                        ],
                        DataHelper::OPERATOR_CONTAINS => [
                            static::FIELD_VALUE_NAME
                        ],
                        DataHelper::OPERATOR_DOES_NOT_CONTAIN => [
                            static::FIELD_VALUE_NAME
                        ],
                        DataHelper::OPERATOR_STARTS_WIDTH => [
                            static::FIELD_VALUE_NAME
                        ],
                        DataHelper::OPERATOR_ENDS_WIDTH => [
                            static::FIELD_VALUE_NAME
                        ]
                    ]
                ]);

                $container2->addChildren(static::FIELD_VALUE_NAME, 'text', [
                    'label'        => __('Value'),
                    'dataScope'    => static::FIELD_VALUE_NAME,
                    'sortOrder'    => 30,
                    'validation'   => [
                        'required-entry' => true
                    ]
                ]);

                $container2->addChildren(static::FIELD_IS_DELETE, 'actionDelete', [
                    'fit'       => true,
                    'sortOrder' => 40
                ]);

                $container2->addChildren(static::FIELD_AGGREGATOR_NAME, 'select', [
                    'label'                => __('Aggregator'),
                    'component'            => 'BlueFormBuilder_Core/js/element/aggregator',
                    'parentSelections'     => $elementId,
                    'sortOrder'            => 50,
                    'dataScope'            => static::FIELD_AGGREGATOR_NAME,
                    'options'              => $this->getAggregatorOptions(),
                    'value'                => 'and',
                    'selectedPlaceholders' => false,
                    'validation'           => [
                        'required-entry' => true
                    ]
                ]);
    }

    public function prepareActions($parent)
    {
        $container1 = $parent->addChildren('actions', 'dynamicRows', [
            'addButtonLabel'      => __('Add Action Row'),
            'template'            => 'BlueFormBuilder_Core/conditional',
            'additionalClasses'   => 'bfb-formbuilder-conditional-layout-right',
            'deleteProperty'      => static::FIELD_IS_DELETE,
            'deleteValue'         => '1',
            'renderDefaultRecord' => false,
            'visible'             => true,
            'sortOrder'           => 40,
            'dataScope'           => '',
            'pageSize'            => 999
        ]);

            $container2 = $container1->addContainer('record', [
                'component'        => 'Magento_Ui/js/dynamic-rows/record',
                'positionProvider' => static::FIELD_SORT_ORDER_NAME,
                'isTemplate'       => true,
                'is_collection'    => true,
                'config' => [
                    'labelVisible' => true
                ]
            ]);

                $container3 = $container2->addFieldset(
                    'left_container',
                    [
                        'label'         => null,
                        'sortOrder'     => 10,
                        'opened'        => true,
                        'visible'       => true,
                        'dataScope'     => ''
                    ]
                );

                    $container3->addChildren(static::FIELD_ACTION_NAME, 'select', [
                        'label'                => __('Action'),
                        'sortOrder'            => 10,
                        'options'              => $this->getActionOptions(),
                        'selectedPlaceholders' => [
                            'defaultPlaceholder' => __('(action)')
                        ],
                        'validation' => [
                            'required-entry' => true
                        ],
                        'groupsConfig' => [
                            static::ACTION_SHOW_FIELDS => [
                                static::FIELD_APPLY_FIELD_NAME
                            ],
                            static::ACTION_HIDE_FIELDS => [
                                static::FIELD_APPLY_FIELD_NAME
                            ],
                            static::ACTION_SEND_EMAIL_TO => [
                                static::FIELD_VALUE_NAME
                            ],
                            static::ACTION_REDIRECT_TO => [
                                static::FIELD_VALUE_NAME
                            ],
                            static::ACTION_SET_VALUE_OF => [
                                static::FIELD_ACTION_FIELD_NAME,
                                static::FIELD_VALUE_NAME
                            ]
                        ]
                    ]);

                    $container3->addChildren(static::FIELD_ACTION_FIELD_NAME, 'select', [
                        'label'                => __('Field Action'),
                        'component'            => 'BlueFormBuilder_Core/js/element/field',
                        'additionalClasses'    => 'bfb-conditional-field bfb-conditional-field-setvalue',
                        'sortOrder'            => 20,
                        'selectedPlaceholders' => [
                            'defaultPlaceholder' => __('(field)')
                        ],
                        'validation' => [
                            'required-entry' => true
                        ]
                    ]);

                    $container4 = $container2->addFieldset(
                        'right_container',
                        [
                        'label'         => null,
                        'sortOrder'     => 20,
                        'opened'        => true,
                        'visible'       => true,
                        'dataScope'     => ''
                        ]
                    );

                    $container4->addChildren(static::FIELD_VALUE_NAME, 'text', [
                        'label'         => __('Value'),
                        'dataScope'     => static::FIELD_VALUE_NAME,
                        'dataType'      => Text::NAME,
                        'sortOrder'     => 10,
                        'validation'    => [
                            'required-entry' => true
                        ]
                    ]);

                    $container4->addChildren(static::FIELD_APPLY_FIELD_NAME, 'select', [
                        'label'                => __('Field Apply'),
                        'component'            => 'BlueFormBuilder_Core/js/element/field',
                        'additionalClasses'    => 'bfb-conditional-field-right bfb-conditional-field',
                        'sortOrder'            => 20,
                        'multiple'             => true,
                        'visible'              => false,
                        'allFields'            => true,
                        'multipleLevel'        => true,
                        'selectedPlaceholders' => [
                            'defaultPlaceholder' => __('(field)')
                        ],
                        'validation' => [
                            'required-entry' => true
                        ]
                    ]);

                    $container2->addChildren(static::FIELD_IS_DELETE, 'actionDelete', [
                        'fit'       => true,
                        'sortOrder' => 30
                    ]);

                    $container2->addFieldset('signin', [
                        'label'             => null,
                        'template'          => 'ui/form/components/complex',
                        'sortOrder'         => 40,
                        'content'           => '&',
                        'additionalClasses' => 'bfb-formbuilder-conditional-text'
                    ]);
    }

    /**
     * @return array
     */
    public function getAggregatorOptions()
    {
        return [
            [
                'label' => __('And'),
                'value' => static::AGGREGATOR_AND
            ],
            [
                'label' => __('Or'),
                'value' => static::AGGREGATOR_OR
            ]
        ];
    }

    /**
     * @return array
     */
    public function getActionOptions()
    {
        return [
            [
                'label' => __('show fields'),
                'value' => static::ACTION_SHOW_FIELDS
            ],
            [
                'label' => __('hide fields'),
                'value' => static::ACTION_HIDE_FIELDS
            ],
            [
                'label' => __('send email to'),
                'value' => static::ACTION_SEND_EMAIL_TO
            ],
            [
                'label' => __('redirect to'),
                'value' => static::ACTION_REDIRECT_TO
            ],
            [
                'label' => __('set value of'),
                'value' => static::ACTION_SET_VALUE_OF
            ]
        ];
    }
}
