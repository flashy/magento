<?php
namespace Flashy\Integration\Controller\Adminhtml;

use Magento\Backend\App\Action;

/**
 * Export controller
 */
abstract class Productexport extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Flashy_Integration::export';
}
