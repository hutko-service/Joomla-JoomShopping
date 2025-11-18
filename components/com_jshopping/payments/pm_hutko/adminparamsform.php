<?php
/*
 * @version      1.2.0
 * @author       DM
 * @package      Jshopping
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
//direct access protection
defined('_JEXEC') or die();
?>
<div class="col100">
  <fieldset class="adminform">
    <table class="admintable" width="100%">
	    <tr>
        <td class="key" width="300">
          <?=ADMIN_CFG_HUTKO_hutko_redirect?>:
        </td>
        <td>
             <?php print \JHTML::_('select.booleanlist', 'pm_params[hutko_redirect]', 'class = "inputbox" size = "1"', $params['hutko_redirect']);?>
        </td>
      </tr>
		<tr>
			<td class="key" width="300">
				<?=ADMIN_CFG_HUTKO_hutko_popup?>:
			</td>
			<td>
				<?php print \JHTML::_('select.booleanlist', 'pm_params[hutko_popup]', 'class = "inputbox" size = "1"', $params['hutko_popup']);?>
			</td>
		</tr>
		
      <tr>
        <td class="key" width="300">
          <?=ADMIN_CFG_HUTKO_MERCHANT_ID?>:
        </td>
        <td>
          <input type="number" name="pm_params[hutko_merchant_id]" class="inputbox" value="<?=$params['hutko_merchant_id']?>">
        </td>
        <td>
          <?php echo \JSHelperAdmin::tooltip(\JText::_(ADMIN_CFG_HUTKO_MERCHANT_ID_DESCRIPTION)); ?>
        </td>
      </tr>
	  
      <tr>
        <td class="key">
          <?=ADMIN_CFG_HUTKO_SECRET_KEY?>:
        </td>
        <td>
          <input type="text" name="pm_params[hutko_secret_key]" class="inputbox" value="<?=$params['hutko_secret_key']?>">
        </td>
        <td>
        <?php echo \JSHelperAdmin::tooltip(\JText::_(ADMIN_CFG_HUTKO_SECRET_KEY_DESCRIPTION))?>
        </td>
      </tr>
	   <tr>
        <td class="key">
          <?=ADMIN_CFG_HUTKO_CURRENCY?>:
        </td>
        <td>
          <input type="text" name="pm_params[hutko_cur]" class="inputbox" value="<?=$params['hutko_cur']?>">
        </td>
        <td>
        <?php echo \JSHelperAdmin::tooltip(\JText::_(ADMIN_CFG_HUTKO_CURRENCY_DESCRIPTION))?>
        </td>
      </tr>
      <tr>
        <td class="key">
          <?php echo _JSHOP_TRANSACTION_END;?>:
        </td>
        <td>
          <?php print \JHTML::_('select.genericlist', $orders->getAllOrderStatus(), 'pm_params[transaction_end_status]', 'class = "inputbox" size = "1"', 'status_id', 'name', $params['transaction_end_status'] );?>
        </td>
      </tr>
      <tr>
        <td class="key">
          <?php echo _JSHOP_TRANSACTION_FAILED;?>:
        </td>
        <td>
          <?php echo \JHTML::_('select.genericlist',$orders->getAllOrderStatus(), 'pm_params[transaction_failed_status]', 'class = "inputbox" size = "1"', 'status_id', 'name', $params['transaction_failed_status']);?>
        </td>
      </tr>
		<tr>
			<td class="key">
				<?php echo ADMIN_CFG_HUTKO_hutko_styles;?>:
			</td>
			<td>
				  <input type="text" rows="50"  cols="50"  name="pm_params[hutko_style]" value="<?=$params['hutko_style']?>" >
			</td>
		</tr>
    </table>
  </fieldset>
</div>
<div class="clr"></div>