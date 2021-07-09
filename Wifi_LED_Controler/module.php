<?php

declare(strict_types=1);

// Generell funktions
require_once __DIR__ . '/../libs/_traits.php';

// CLASS WifiLEDControler
class WifiLEDControler extends IPSModule
{
    use ProfileHelper;
    use DebugHelper;

    // GRB Profil array
    private $assoGRB = [
        [0, 'Manually', '', 0x000000],
        [37, '7-step color sequence', '', 0x000000],
        [38, 'pulsing red', '', 0xFF0000],
        [39, 'pulsing green', '', 0x00FF00],
        [40, 'pulsing blue', '', 0x0000FF],
        [41, 'pulsing yellow', '', 0xFFFF00],
        [42, 'pulsing cyan', '', 0x00FFFF],
        [43, 'pulsing purple', '', 0xFF00FF],
        [44, 'pulsing white', '', 0xFFFFFF],
        [45, 'pulsing red + green', '', 0xF0F000],
        [46, 'pulsing red + blue', '', 0xF000F0],
        [47, 'ulsing green + blue', '', 0x00F0F0],
        [48, '7-step flashing', '', 0xA0A0A0],
        [49, 'flashing red', '', 0xFF0000],
        [50, 'flashing green', '', 0x00FF00],
        [51, 'flashing blue', '', 0x0000FF],
        [52, 'flashing yellow', '', 0xFFFF00],
        [53, 'flashing cyan', '', 0x00FFFF],
        [54, 'flashing purple', '', 0xFF00FF],
        [55, 'flashing white', '', 0xFFFFFF],
    ];
    // BRG Profil array
    private $assoBRG = [
        [0, 'Manuell', '', 0x000000],
        [37, '7-step color sequence', '', 0x000000],
        [38, 'pulsing red', '', 0xFF0000],
        [39, 'pulsing blue', '', 0x0000FF],
        [40, 'pulsing green', '', 0x00FF00],
        [41, 'pulsing purple', '', 0xFF00FF],
        [42, 'pulsing cyan', '', 0x00FFFF],
        [43, 'pulsing yellow', '', 0xFFFF00],
        [44, 'pulsing white', '', 0xFFFFFF],
        [45, 'pulsing red + blue', '', 0xF000F0],
        [46, 'pulsing red + green', '', 0xF0F000],
        [47, 'ulsing green + blue', '', 0x00F0F0],
        [48, '7-step flashing', '', 0xA0A0A0],
        [49, 'flashing red', '', 0xFF0000],
        [50, 'flashing blue', '', 0x0000FF],
        [51, 'flashing green', '', 0x00FF00],
        [52, 'flashing purple', '', 0xFF00FF],
        [53, 'flashing cyan', '', 0x00FFFF],
        [54, 'flashing yellow', '', 0xFFFF00],
        [55, 'flashing white', '', 0xFFFFFF],
    ];

    /**
     * Create.
     */
    public function Create()
    {
        // Never delete this line!
        parent::Create();
        // Config Variablen
        $this->RegisterPropertyString('TCPIP', '127.0.0.1');
        $this->RegisterPropertyString('RGB', '012');
        // Variablen Profile einrichten
        $this->RegisterProfile(vtInteger, 'MHC.ModeGRB', 'Bulb', '', '', 0, 0, 0, 0, $this->assoGRB);
        $this->RegisterProfile(vtInteger, 'MHC.ModeBRG', 'Bulb', '', '', 0, 0, 0, 0, $this->assoBRG);
        // Variablen erzeugen
        $varID = $this->RegisterVariableBoolean('Power', $this->Translate('Power'), '~Switch', 0);
        $this->EnableAction('Power');
        $varID = $this->RegisterVariableInteger('Color', $this->Translate('Color'), '~HexColor', 1);
        $this->EnableAction('Color');
        $varID = $this->RegisterVariableInteger('Speed', $this->Translate('Speed'), '~Intensity.100', 2);
        $this->EnableAction('Speed');
        $varID = $this->RegisterVariableInteger('Brightness', $this->Translate('Brightness'), '~Intensity.100', 3);
        $this->EnableAction('Brightness');
        $varID = $this->RegisterVariableInteger('Mode', $this->Translate('Mode'), 'MHC.ModeGRB', 4);
        $this->EnableAction('Mode');
    }

    /**
     * Destroy.
     */
    public function Destroy()
    {
        // Never delete this line!
        parent::Destroy();
    }

    /**
     * Apply Configuration Changes.
     */
    public function ApplyChanges()
    {
        // Never delete this line!
        parent::ApplyChanges();
        // Debug message
        $tcpIP = $this->ReadPropertyString('TCPIP');
        $rgbID = $this->ReadPropertyString('RGB');
        // IP Check
        if (filter_var($this->ReadPropertyString('TCPIP'), FILTER_VALIDATE_IP) !== false) {
            $this->SetStatus(102);
        } else {
            $this->SetStatus(201);
        }
        // Setup variable profil
        if ($rgbID == '012') {
            $this->RegisterVariableInteger('Mode', $this->Translate('Mode'), 'MHC.ModeGRB', 4);
        } else {
            $this->RegisterVariableInteger('Mode', $this->Translate('Mode'), 'MHC.ModeBRG', 4);
        }
        $this->SendDebug(__FUNCTION__, 'IP=' . $tcpIP . ', RGB=' . $rgbID, 0);
    }

    /**
     * Call by visual changes.
     */
    public function RequestAction($ident, $value)
    {
        // Debug
        $this->SendDebug(__FUNCTION__, 'RequestAction: ($ident,$value)', 0);

        switch ($ident) {
            // Switch Power On/Off
            case 'Power':
                $on = [0x71, 0x23, 0x0f];
                $off = [0x71, 0x24, 0x0f];
                if ($value) {
                    $this->SendData($on);
                } else {
                    $this->SendData($off);
                }
                $this->SetValueBoolean($ident, $value);
                break;
            // Set Speed value
            case 'Speed':
                $this->SetValueInteger($ident, $value);
                $this->SendFunction();
                break;
            // Set Display Mode
            case 'Mode':
                $disabled = ($value > 0) ? true : false;
                $this->SetVariableDisabled('Speed', !$disabled);
                $this->SetVariableDisabled('Color', $disabled);
                $this->SetVariableDisabled('Brightness', $disabled);
                $this->SetValueInteger($ident, $value);
                // Manual mode.
                if ($value == 0) {
                    $this->SendColor();
                }
                // Functional mode
                else {
                    $this->SendFunction();
                }
                break;
            case 'Color':
            case 'Brightness':
                $this->SetValueInteger($ident, $value);
                $this->SendColor();
                break;
            default:
                throw new Exception('Invalid Ident');
        }
    }

    /**
     * This function will be available automatically after the module is imported with the module control.
     * Using the custom prefix this function will be callable from PHP and JSON-RPC through:.
     *
     * MHC_SetBrightness(int $InstanzID, int $Brightness);
     */
    public function SetBrightness(int $brightness)
    {
        $this->RequestAction('Brightness', $brightness);
    }

    /**
     * This function will be available automatically after the module is imported with the module control.
     * Using the custom prefix this function will be callable from PHP and JSON-RPC through:.
     *
     * MHC_SetColor(int $InstanzID, int $Color);
     */
    public function SetColor(int $color)
    {
        $this->RequestAction('Color', $color);
    }

    /**
     * This function will be available automatically after the module is imported with the module control.
     * Using the custom prefix this function will be callable from PHP and JSON-RPC through:.
     *
     * MHC_SetMode(int $InstanzID, int $Mode);
     */
    public function SetMode(int $mode)
    {
        $this->RequestAction('Mode', $mode);
    }

    /**
     * This function will be available automatically after the module is imported with the module control.
     * Using the custom prefix this function will be callable from PHP and JSON-RPC through:.
     *
     * MHC_SetPower(int $InstanzID, bool $Power);
     */
    public function SetPower(bool $power)
    {
        $this->RequestAction('Power', $power);
    }

    /**
     * Send function data.
     */
    private function SendFunction()
    {
        $data = [0x61, 0x00, 0x00, 0x0F];
        $mode = $this->GetValue('Mode');
        $speed = 100 - $this->GetValue('Speed');
        if ($speed > 100) {
            $speed = 100;
        } elseif ($speed < 1) {
            $speed = 1;
        }
        $data[1] = $mode;
        $data[2] = $speed;
        $this->SendDebug(__FUNCTION__, $data);
        $this->SendData($data);
    }

    /**
     * Send color data.
     */
    private function SendColor()
    {
        $data = [0x31, 0x00, 0x00, 0x00, 0x00, 0x00, 0x0F];
        $brightness = $this->GetValue('Brightness') / 100;
        $color = $this->GetValue('Color');
        $rgb = [0x00, 0x00, 0x00];
        $rgb[0] = (($color >> 16) & 0xFF); // red
        $rgb[1] = (($color >> 8) & 0xFF); // green
        $rgb[2] = ($color & 0xFF); // blue
        $this->SendDebug(__FUNCTION__, $rgb);
        // map with brightness
        $rgb[0] *= $brightness;
        $rgb[1] *= $brightness;
        $rgb[2] *= $brightness;
        $this->SendDebug(__FUNCTION__, $rgb);
        // map rgb channel
        $channel = $this->ReadPropertyString('RGB');
        $index = (int) $channel[0];
        $this->SendDebug(__FUNCTION__, "0 -> $index", 0);
        $data[1] = floor($rgb[$index]);
        $index = (int) $channel[1];
        $this->SendDebug(__FUNCTION__, "1 -> $index", 0);
        $data[2] = floor($rgb[$index]);
        $index = (int) $channel[2];
        $this->SendDebug(__FUNCTION__, "2 -> $index", 0);
        $data[3] = floor($rgb[$index]);
        // send data
        $this->SendData($data);
    }

    /**
     * Send data array to controller.
     *
     * @param array $values Configuration Data
     */
    private function SendData(array $values)
    {
        $path = 'tcp://' . $this->ReadPropertyString('TCPIP');
        $socket = @fsockopen($path, 5577, $errno, $errstr, 5);
        // Check Socket
        if (!$socket) {
            $this->SendDebug(__FUNCTION__, $path . " -> $errstr ($errno)", 0);
            return;
        } else {
            $this->SendDebug(__FUNCTION__, "Connection etablished '" . $path . "'", 0);
        }
        $send = '';
        $this->SendDebug(__FUNCTION__, 'Values=' . print_r($values, true));
        foreach ($values as $value) {
            $send .= chr(intval($value));
            $data[] = $value;
        }
        $check = $this->GetChecksum($values);
        $send .= chr($check);
        $data[] = $check;
        // send data
        fwrite($socket, $send);
        $this->SendDebug(__FUNCTION__, 'Data=' . implode(',', $data), 0);
        // close socket
        fclose($socket);
    }

    /**
     * Calculatue checksum for given data.
     * 
     * @param array $values Values over which the checksum is to be formed
     * @return integer Checksum
     */
    private function GetChecksum(array $values)
    {
        $checksum = array_sum($values);
        $checksum = dechex($checksum);
        $checksum = substr($checksum, -2);
        $checksum = hexdec($checksum);
        // Return checksum
        return $checksum;
    }

    /**
     * Update a boolean value.
     *
     * @param string $ident Ident of the boolean variable
     * @param bool   $value Value of the boolean variable
     */
    private function SetValueBoolean(string $ident, bool $value)
    {
        $id = $this->GetIDForIdent($ident);
        SetValueBoolean($id, $value);
    }

    /**
     * Update a string value.
     *
     * @param string $ident Ident of the string variable
     * @param string $value Value of the string variable
     */
    private function SetValueString(string $ident, string $value)
    {
        $id = $this->GetIDForIdent($ident);
        SetValueString($id, $value);
    }

    /**
     * Update a integer value.
     *
     * @param string $ident Ident of the integer variable
     * @param int    $value Value of the integer variable
     */
    private function SetValueInteger(string $ident, int $value)
    {
        $id = $this->GetIDForIdent($ident);
        SetValueInteger($id, $value);
    }

    /**
     * Sets the variable inactive.
     *
     * @param string $ident Ident of the integer variable
     * @param bool   $value Enable or disable value the variable
     */
    private function SetVariableDisabled(string $ident, bool $value)
    {
        $id = $this->GetIDForIdent($ident);
        IPS_SetDisabled($id, $value);
    }
}
