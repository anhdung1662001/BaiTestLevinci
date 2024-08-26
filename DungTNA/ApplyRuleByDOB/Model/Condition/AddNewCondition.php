<?php

namespace DungTNA\ApplyRuleByDOB\Model\Condition;

use Magento\Framework\App\ResourceConnection;
use Magento\Rule\Model\Condition\Context;

class AddNewCondition extends \Magento\Rule\Model\Condition\AbstractCondition
{
    /**
     * @param ResourceConnection $resourceConnection
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        protected ResourceConnection $resourceConnection,
        Context                      $context,
        array                        $data = []
    )
    {
        parent::__construct($context, $data);
    }

    /**
     * @return $this|AddNewCondition
     */
    public function loadAttributeOptions()
    {
        $this->setAttributeOption([
            'customer_dob' => __('Customer DOB')
        ]);
        return $this;
    }

    /**
     * @return string
     */
    public function getInputType()
    {
        return 'date';
    }

    /**
     * @return string
     */
    public function getValueElementType()
    {
        return 'date';
    }

    /**
     * @return AddNewCondition
     */
    public function getValueElement()
    {
        $element = parent::getValueElement();

        switch ($this->getInputType()) {
            case 'date':
                $element->setClass('hasDatepicker');
                $element->setExplicitApply(true);
                break;
        }

        return $element;
    }


    /**
     * @param \Magento\Framework\Model\AbstractModel $model
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function validate(\Magento\Framework\Model\AbstractModel $model)
    {
        $isApplyRule = false;
        if (!empty($model->getData('customer_id'))){
            $isApplyRule = $this->handleApplyRule($model->getData('customer_id'));
        }
        $model->setData('customer_dob', $isApplyRule);
        return parent::validate($model);
    }


    /**
     * @param $telephone
     * @return bool
     */
    protected function handleApplyRule($customerId)
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()
            ->from(
                $this->resourceConnection->getTableName('customer_entity'),
                'dob'
            )->where('entity_id = ?', $customerId)->limit(1);
        $dateValue = strtotime($connection->fetchOne($select));
        $conditionValue = strtotime($this->getValue());
        return match ($this->getOperator()) {
            '==' => $dateValue == $conditionValue,
            '!=' => $dateValue != $conditionValue,
            '>=' => $dateValue >= $conditionValue,
            '<=' => $dateValue <= $conditionValue,
            '>' => $dateValue > $conditionValue,
            '<' => $dateValue < $conditionValue,
            default => false,
        };
    }

}
