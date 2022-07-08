<?php

/**
 * WebhookHelper.php
 *
 * Part of the Trait-Libraray for IP-Symcon Modules.
 *
 * @package       traits
 * @author        Heiko Wilknitz <heiko@wilkware.de>
 * @copyright     2021 Heiko Wilknitz
 * @link          https://wilkware.de
 * @license       https://creativecommons.org/licenses/by-nc-sa/4.0/ CC BY-NC-SA 4.0
 */

declare(strict_types=1);

/**
 * Helper class for web hooks.
 */
trait WebhookHelper
{
    /**
     * Register a new web hook, if not already existing.
     *
     * @param string $hook path of the web hook.
     */
    protected function RegisterHook($hook)
    {
        $ids = IPS_GetInstanceListByModuleID('{015A6EB8-D6E5-4B93-B496-0D3F77AE9FE1}');
        if (count($ids) > 0) {
            $hooks = json_decode(IPS_GetProperty($ids[0], 'Hooks'), true);
            $found = false;
            foreach ($hooks as $key => $value) {
                if ($value['Hook'] == $hook) {
                    if ($value['TargetID'] == $this->InstanceID) {
                        return;
                    }
                    $hooks[$key]['TargetID'] = $this->InstanceID;
                    $found = true;
                    $this->SendDebug('RegisterHook', 'Update hook:' . $hook . $this->InstanceID);
                }
            }
            // Neww Hook?
            if ($found == false) {
                $hooks[] = ['Hook' => $hook, 'TargetID' => $this->InstanceID];
                $this->SendDebug('RegisterHook', 'New hook:' . $hook . $this->InstanceID);
            }
            // Update or Register
            IPS_SetProperty($ids[0], 'Hooks', json_encode($hooks));
            IPS_ApplyChanges($ids[0]);
        }
    }

    /**
     * Unregister a web hook, if not already existing.
     *
     * @param string $hook path of the web hook.
     */
    protected function UnregisterHook($hook)
    {
        $ids = IPS_GetInstanceListByModuleID('{015A6EB8-D6E5-4B93-B496-0D3F77AE9FE1}');
        if (count($ids) > 0) {
            $hooks = json_decode(IPS_GetProperty($ids[0], 'Hooks'), true);
            $found = false;
            foreach ($hooks as $key => $value) {
                if ($value['Hook'] == $hook) {
                    $found = true;
                    $this->SendDebug('UnregisterHook', $hook . $this->InstanceID);
                    break;
                }
            }
            // Unregister
            if ($found == true) {
                array_splice($hooks, $key, 1);
                IPS_SetProperty($ids[0], 'Hooks', json_encode($hooks));
                IPS_ApplyChanges($ids[0]);
            }
        }
    }
}
