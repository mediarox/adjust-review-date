<?php
/**
 * @package Mediarox_AdjustReviewDate
 * @copyright Copyright 2019 (c) mediarox UG (haftungsbeschraenkt) (http://www.mediarox.de)
 * @author Kristian Ernst <kernst@mediarox.de>
 */
namespace Mediarox\AdjustReviewDate\Block\Adminhtml\Add;

use Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Review\Block\Adminhtml\Rating\Detailed;
use Magento\Review\Helper\Data;
use Magento\Store\Model\System\Store;

/**
 * Adminhtml add product review form
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Form extends Generic
{
    /**
     * Review data
     *
     * @var Data
     */
    protected $_reviewData = null;

    /**
     * Core system store model
     *
     * @var Store
     */
    protected $_systemStore;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Store $systemStore
     * @param Data $reviewData
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Store $systemStore,
        Data $reviewData,
        array $data = []
    ) {
        $this->_reviewData = $reviewData;
        $this->_systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare add review form
     *
     * @return void
     * @throws LocalizedException
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $fieldset = $form->addFieldset('add_review_form', ['legend' => __('Review Details')]);

        $fieldset->addField('product_name', 'note', ['label' => __('Product'), 'text' => 'product_name']);

        $fieldset->addField(
            'detailed-rating',
            'note',
            [
                'label' => __('Product Rating'),
                'required' => true,
                'text' => '<div id="rating_detail">' . $this->getLayout()->createBlock(
                    Detailed::class
                )->toHtml() . '</div>'
            ]
        );

        $fieldset->addField(
            'status_id',
            'select',
            [
                'label' => __('Status'),
                'required' => true,
                'name' => 'status_id',
                'values' => $this->_reviewData->getReviewStatusesOptionArray()
            ]
        );

        /**
         * Check is single store mode
         */
        if (!$this->_storeManager->isSingleStoreMode()) {
            $field = $fieldset->addField(
                'select_stores',
                'multiselect',
                [
                    'label' => __('Visibility'),
                    'required' => true,
                    'name' => 'select_stores[]',
                    'values' => $this->_systemStore->getStoreValuesForForm()
                ]
            );
            $renderer = $this->getLayout()->createBlock(
                Element::class
            );
            $field->setRenderer($renderer);
        }

        $fieldset->addField(
            'nickname',
            'text',
            [
                'name' => 'nickname',
                'title' => __('Nickname'),
                'label' => __('Nickname'),
                'maxlength' => '50',
                'required' => true
            ]
        );

        $fieldset->addField(
            'title',
            'text',
            [
                'name' => 'title',
                'title' => __('Summary of Review'),
                'label' => __('Summary of Review'),
                'maxlength' => '255',
                'required' => true
            ]
        );

        $fieldset->addField(
            'detail',
            'textarea',
            [
                'name' => 'detail',
                'title' => __('Review'),
                'label' => __('Review'),
                'required' => true
            ]
        );

        $dateFormat = $this->_localeDate->getDateFormat(\IntlDateFormatter::MEDIUM);
        $timeFormat = $this->_localeDate->getTimeFormat(\IntlDateFormatter::MEDIUM);
        $fieldset->addField(
            'created_at',
            'date',
            [
                'name' => 'date',
                'title' => __('Date'),
                'label' => __('Date') . __(' (GMT)'),
                'required' => false,
                'date_format' => $dateFormat,
                'time_format' => $timeFormat
            ]
        );

        $fieldset->addField('product_id', 'hidden', ['name' => 'product_id']);

        $form->setMethod('post');
        $form->setUseContainer(true);
        $form->setId('edit_form');
        $form->setAction($this->getUrl('review/product/post'));

        $this->setForm($form);
    }
}
