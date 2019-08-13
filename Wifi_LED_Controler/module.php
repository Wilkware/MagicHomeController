<?php

require_once __DIR__.'/../libs/traits.php';  // Allgemeine Funktionen

class WifiLEDControler extends IPSModule
{
    use ProfileHelper, DebugHelper;
    // Modul
    private $moduleName = 'Magic Home Control';
    // GRB Profil array
    private $assoGRB = [
      [0, 'Manuell', '', 0x000000],
      [37, '7-stufiger Farbdurchlauf', '', 0x000000],
      [38, 'Rot pulsierend', '', 0xFF0000],
      [39, 'Grün pulsierend', '', 0x00FF00],
      [40, 'Blau pulsierend', '', 0x0000FF],
      [41, 'Gelb pulsierend', '', 0xFFFF00],
      [42, 'Türkis pulsierend', '', 0x00FFFF],
      [43, 'Violett pulsierend', '', 0xFF00FF],
      [44, 'Weiss pulsierend', '', 0xFFFFFF],
      [45, 'Rot Grün pulsierend', '', 0xF0F000],
      [46, 'Rot Blau pulsierend', '', 0xF000F0],
      [47, 'Grün Blau pulsierend', '', 0x00F0F0],
      [48, '7-stufig blitzend', '', 0xA0A0A0],
      [49, 'Rot blitzend', '', 0xFF0000],
      [50, 'Grün blitzend', '', 0x00FF00],
      [51, 'Blau blitzend', '', 0x0000FF],
      [52, 'Gelb blitzend', '', 0xFFFF00],
      [53, 'Türkis blitzend', '', 0x00FFFF],
      [54, 'Violett blitzend', '', 0xFF00FF],
      [55, 'Weiss blitzend', '', 0xFFFFFF],
    ];
    // BRG Profil array
    private $assoBRG = [
      [0, 'Manuell', '', 0x000000],
      [37, '7-stufiger Farbdurchlauf', '', 0x000000],
      [38, 'Rot pulsierend', '', 0xFF0000],
      [39, 'Blau pulsierend', '', 0x0000FF],
      [40, 'Grün pulsierend', '', 0x00FF00],
      [41, 'Violett pulsierend', '', 0xFF00FF],
      [42, 'Türkis pulsierend', '', 0x00FFFF],
      [43, 'Gelb pulsierend', '', 0xFFFF00],
      [44, 'Weiss pulsierend', '', 0xFFFFFF],
      [45, 'Rot Blau pulsierend', '', 0xF000F0],
      [46, 'Rot Grün pulsierend', '', 0xF0F000],
      [47, 'Grün Blau pulsierend', '', 0x00F0F0],
      [48, '7-stufig blitzend', '', 0xA0A0A0],
      [49, 'Rot blitzend', '', 0xFF0000],
      [50, 'Blau blitzend', '', 0x0000FF],
      [51, 'Grün blitzend', '', 0x00FF00],
      [52, 'Violett blitzend', '', 0xFF00FF],
      [53, 'Türkis blitzend', '', 0x00FFFF],
      [54, 'Gelb blitzend', '', 0xFFFF00],
      [55, 'Weiss blitzend', '', 0xFFFFFF],
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
        $varID = $this->RegisterVariableBoolean('Power', 'Aktiv', '~Switch', 0);
        $this->EnableAction('Power');
        $varID = $this->RegisterVariableInteger('Color', 'Farbe', '~HexColor', 1);
        $this->EnableAction('Color');
        $varID = $this->RegisterVariableInteger('Speed', 'Geschwindigkeit', '~Intensity.100', 2);
        $this->EnableAction('Speed');
        $varID = $this->RegisterVariableInteger('Brightness', 'Helligkeit', '~Intensity.100', 3);
        $this->EnableAction('Brightness');
        $varID = $this->RegisterVariableInteger('Mode', 'Modus', 'MHC.ModeGRB', 4);
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
            $this->RegisterVariableInteger('Mode', 'Modus', 'MHC.ModeGRB', 4);
        } else {
            $this->RegisterVariableInteger('Mode', 'Modus', 'MHC.ModeBRG', 4);
        }
        $this->SendDebug('ApplyChanges', 'IP='.$tcpIP.', RGB='.$rgbID, 0);
    }

    /**
     * Call by visual changes.
     */
    public function RequestAction($ident, $value)
    {
        // Debug
        $this->SendDebug('RequestAction', 'RequestAction: ($ident,$value)', 0);

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
                SetValue($this->GetIDForIdent($ident), $value);
                break;
            // Set Speed value
            case 'Speed':
                SetValue($this->GetIDForIdent($ident), $value);
                $this->SendFunction();
                break;
            // Set Display Mode
            case 'Mode':
                IPS_SetDisabled($this->GetIDForIdent('Speed'), !$value);
                IPS_SetDisabled($this->GetIDForIdent('Color'), $value);
                IPS_SetDisabled($this->GetIDForIdent('Brightness'), $value);
                SetValue($this->GetIDForIdent($ident), $value);
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
                SetValue($this->GetIDForIdent($ident), $value);
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
        $mode = GetValue($this->GetIDForIdent('Mode'));
        $speed = 100 - GetValue($this->GetIDForIdent('Speed'));
        if ($speed > 100) {
            $speed = 100;
        } elseif ($speed < 1) {
            $speed = 1;
        }
        $data[1] = $mode;
        $data[2] = $speed;
        $this->SendData($data);
    }

    /**
     * Send color data.
     */
    private function SendColor()
    {
        $data = [0x31, 0x00, 0x00, 0x00, 0x00, 0x00, 0x0F];
        $brightness = GetValue($this->GetIDForIdent('Brightness')) / 100;
        $color = GetValue($this->GetIDForIdent('Color'));
        $rgb = [0x00, 0x00, 0x00];
        $rgb[0] = (($color >> 16) & 0xFF); // red
        $rgb[1] = (($color >> 8) & 0xFF); // green
        $rgb[2] = ($color & 0xFF); // blue
        // map with brightness
        $rgb[0] *= $brightness;
        $rgb[1] *= $brightness;
        $rgb[2] *= $brightness;
        // map rgb channel
        $channel = $this->ReadPropertyString('RGB');
        $index = (int) $channel[0];
        $this->SendDebug('SendColor', "0 -> $index", 0);
        $data[1] = floor($rgb[$index]);
        $index = (int) $channel[1];
        $this->SendDebug('SendColor', "1 -> $index", 0);
        $data[2] = floor($rgb[$index]);
        $index = (int) $channel[2];
        $this->SendDebug('SendColor', "2 -> $index", 0);
        $data[3] = floor($rgb[$index]);
        // send data
        $this->SendData($data);
    }

    /**
     * Send data array to controller.
     */
    private function SendData($values)
    {
        $path = 'tcp://'.$this->ReadPropertyString('TCPIP');
        $socket = @fsockopen($path, 5577, $errno, $errstr, 5);
        // Check Socket
        if (!$socket) {
            $this->SendDebug('SendData', $path." -> $errstr ($errno)", 0);

            return;
        } else {
            $this->SendDebug('SendData', "Verbindung aufgebaut '".$path."'", 0);
        }
        $send = '';
        foreach ($values as $value) {
            $send .= chr($value);
            $data[] = $value;
        }
        $check = $this->GetChecksum($values);
        $send .= chr($check);
        $data[] = $check;
        // send data
        fwrite($socket, $send);
        $this->SendDebug('SendData', 'Sende Daten='.implode(',', $data), 0);
        // close socket
        fclose($socket);
    }

    /**
     * Calculatue checksum for given data.
     */
    private function GetChecksum($values)
    {
        $checksum = array_sum($values);
        $checksum = dechex($checksum);
        $checksum = substr($checksum, -2);
        $checksum = hexdec($checksum);

        return $checksum;
    }
}
