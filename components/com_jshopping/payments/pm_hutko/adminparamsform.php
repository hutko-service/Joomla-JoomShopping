<?php
/**
 * @package     JoomShopping
 * @subpackage  Payment.pm_hutko
 *
 * @copyright   (C) 2026 Hutko Service
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;
?>
<div class="col100">
    <fieldset class="adminform">
        <table class="admintable" width="100%">
            <tr>
                <td class="key" width="300">
                    <?php echo ADMIN_CFG_HUTKO_EMBEDDED_CHECKOUT; ?>:
                </td>
                <td>
                    <?php echo \JHTML::_('select.booleanlist', 'pm_params[hutko_redirect]', 'class="inputbox" size="1"', $params['hutko_redirect']); ?>
                </td>
            </tr>
            <tr>
                <td class="key" width="300">
                    <?php echo ADMIN_CFG_HUTKO_MERCHANT_ID; ?>:
                </td>
                <td>
                    <input type="number" name="pm_params[hutko_merchant_id]" class="inputbox" value="<?php echo htmlspecialchars((string) $params['hutko_merchant_id'], ENT_QUOTES, 'UTF-8'); ?>">
                </td>
                <td>
                    <?php echo \JSHelperAdmin::tooltip(\JText::_(ADMIN_CFG_HUTKO_MERCHANT_ID_DESCRIPTION)); ?>
                </td>
            </tr>
            <tr>
                <td class="key">
                    <?php echo ADMIN_CFG_HUTKO_SECRET_KEY; ?>:
                </td>
                <td>
                    <input type="text" name="pm_params[hutko_secret_key]" class="inputbox" value="<?php echo htmlspecialchars((string) $params['hutko_secret_key'], ENT_QUOTES, 'UTF-8'); ?>">
                </td>
                <td>
                    <?php echo \JSHelperAdmin::tooltip(\JText::_(ADMIN_CFG_HUTKO_SECRET_KEY_DESCRIPTION)); ?>
                </td>
            </tr>
            <tr>
                <td class="key">
                    <?php echo ADMIN_CFG_HUTKO_CURRENCY; ?>:
                </td>
                <td>
                    <input type="text" name="pm_params[hutko_cur]" class="inputbox" value="<?php echo htmlspecialchars((string) $params['hutko_cur'], ENT_QUOTES, 'UTF-8'); ?>">
                </td>
                <td>
                    <?php echo \JSHelperAdmin::tooltip(\JText::_(ADMIN_CFG_HUTKO_CURRENCY_DESCRIPTION)); ?>
                </td>
            </tr>
            <tr>
                <td class="key">
                    <?php echo _JSHOP_TRANSACTION_END; ?>:
                </td>
                <td>
                    <?php echo \JHTML::_('select.genericlist', $orders->getAllOrderStatus(), 'pm_params[transaction_end_status]', 'class="inputbox" size="1"', 'status_id', 'name', $params['transaction_end_status']); ?>
                </td>
            </tr>
            <tr>
                <td class="key">
                    <?php echo _JSHOP_TRANSACTION_FAILED; ?>:
                </td>
                <td>
                    <?php echo \JHTML::_('select.genericlist', $orders->getAllOrderStatus(), 'pm_params[transaction_failed_status]', 'class="inputbox" size="1"', 'status_id', 'name', $params['transaction_failed_status']); ?>
                </td>
            </tr>
        </table>
    </fieldset>
</div>
<div class="clr"></div>
