<?php
/**
 * @package     JoomShopping
 * @subpackage  Payment.pm_hutko
 *
 * @copyright   (C) 2026 Hutko Service
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Installer\InstallerAdapter;
use Joomla\CMS\Installer\InstallerScriptInterface;
use Joomla\Database\DatabaseInterface;

return new class () implements InstallerScriptInterface {
    private const PAYMENT_CLASS = 'pm_hutko';

    public function install(InstallerAdapter $adapter): bool
    {
        return $this->copyPaymentFiles($adapter) && $this->ensurePaymentMethod();
    }

    public function update(InstallerAdapter $adapter): bool
    {
        return $this->copyPaymentFiles($adapter) && $this->ensurePaymentMethod();
    }

    public function uninstall(InstallerAdapter $adapter): bool
    {
        $target = JPATH_ROOT . '/components/com_jshopping/payments/' . self::PAYMENT_CLASS;

        if (is_dir($target)) {
            Folder::delete($target);
        }

        $this->removePaymentMethod();

        return true;
    }

    public function preflight(string $type, InstallerAdapter $adapter): bool
    {
        if (!is_dir(JPATH_ROOT . '/components/com_jshopping')) {
            Factory::getApplication()->enqueueMessage(
                'JoomShopping must be installed before installing hutko for JoomShopping.',
                'error'
            );

            return false;
        }

        return true;
    }

    public function postflight(string $type, InstallerAdapter $adapter): bool
    {
        if ($type === 'uninstall') {
            return true;
        }

        return $this->copyPaymentFiles($adapter) && $this->ensurePaymentMethod();
    }

    private function copyPaymentFiles(InstallerAdapter $adapter): bool
    {
        $source = $adapter->getParent()->getPath('source') . '/components/com_jshopping/payments/' . self::PAYMENT_CLASS;
        $targetRoot = JPATH_ROOT . '/components/com_jshopping/payments';
        $target = $targetRoot . '/' . self::PAYMENT_CLASS;

        if (!is_dir($source)) {
            Factory::getApplication()->enqueueMessage('The payment files were not found in the installation package.', 'error');

            return false;
        }

        if (!is_dir($targetRoot) && !Folder::create($targetRoot)) {
            Factory::getApplication()->enqueueMessage('Unable to create the JoomShopping payments directory.', 'error');

            return false;
        }

        if (!Folder::copy($source, $target, '', true)) {
            Factory::getApplication()->enqueueMessage('Unable to copy the hutko payment files into JoomShopping.', 'error');

            return false;
        }

        return true;
    }

    private function ensurePaymentMethod(): bool
    {
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true)
            ->select('COUNT(*)')
            ->from($db->quoteName('#__jshopping_payment_method'))
            ->where($db->quoteName('payment_class') . ' = ' . $db->quote(self::PAYMENT_CLASS));

        $db->setQuery($query);

        if ((int) $db->loadResult() > 0) {
            return true;
        }

        $columns = [
            'payment_code',
            'payment_class',
            'payment_publish',
            'payment_ordering',
            'payment_type',
            'price',
            'price_type',
            'tax_id',
            'show_descr_in_email',
            'name_en-GB',
            'name_de-DE',
        ];

        $values = [
            $db->quote('hutko'),
            $db->quote(self::PAYMENT_CLASS),
            0,
            0,
            2,
            0,
            1,
            -1,
            0,
            $db->quote('hutko'),
            $db->quote('hutko'),
        ];

        $query = $db->getQuery(true)
            ->insert($db->quoteName('#__jshopping_payment_method'))
            ->columns(array_map([$db, 'quoteName'], $columns))
            ->values(implode(',', $values));

        $db->setQuery($query)->execute();

        return true;
    }

    private function removePaymentMethod(): void
    {
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true)
            ->delete($db->quoteName('#__jshopping_payment_method'))
            ->where($db->quoteName('payment_class') . ' = ' . $db->quote(self::PAYMENT_CLASS));

        $db->setQuery($query)->execute();
    }
};
