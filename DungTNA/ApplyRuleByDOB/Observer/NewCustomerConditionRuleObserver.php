<?php
declare(strict_types=1);

namespace DungTNA\ApplyRuleByDOB\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Dispatcher for the `new_customer_condition_rule` event.
 */
class NewCustomerConditionRuleObserver implements ObserverInterface
{
    /**
     * @param Observer $observer
     * @return $this|void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $additional = $observer->getAdditional();
        $conditions = (array) $additional->getConditions();

        $conditions = array_merge_recursive($conditions, [
            $this->getCustomerDobCondition()
        ]);

        $additional->setConditions($conditions);
        return $this;
    }

    /**
     * @return array
     */
    private function getCustomerDobCondition()
    {
        return [
            'label'=> __('Customer DOB'),
            'value'=> \DungTNA\ApplyRuleByDOB\Model\Condition\AddNewCondition::class
        ];
    }
}
