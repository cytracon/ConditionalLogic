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

namespace Cytracon\ConditionalLogic\Block\Adminhtml;

class Logic extends \Magento\Backend\Block\Template
{
	const ACTION_SHOW_FIELDS   = 'sf';
	const ACTION_HIDE_FIELDS   = 'hf';
	const ACTION_SEND_EMAIL_TO = 'set';
	const ACTION_REDIRECT_TO   = 'rt';
	const ACTION_SET_VALUE_OF  = 'svo';
	const AGGREGATOR_AND       = 'and';
	const AGGREGATOR_OR        = 'or';


    /**
     * @return array
     */
    public function getActions()
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

    /**
     * @return array
     */
    public function getAggregators()
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
}