<?php
/**
 * WifiLEDControler ist die Klasse für das IPS-Modul 'SymconMHC'.
 * Erweitert IPSModule.
 */
class WifiLEDControler extends IPSModule
{
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
      [44, 'Rot Grün pulsierend', '', 0xF0F000],
      [45, 'Rot Blau pulsierend', '', 0xF000F0],
      [46, 'Grün Blau pulsierend', '', 0x00F0F0],
      [47, '7-stufig blitzend', '', 0xA0A0A0],
      [48, 'Rot blitzend', '', 0xFF0000],
      [49, 'Grün blitzend', '', 0x00FF00],
      [50, 'Blau blitzend', '', 0x0000FF],
      [51, 'Gelb blitzend', '', 0xFFFF00],
      [52, 'Türkis blitzend', '', 0x00FFFF],
      [53, 'Violett blitzend', '', 0xFF00FF],
      [54, 'Weiss blitzend', '', 0xFFFFFF],
      [55, '7-stufiger Farbwechsel', '', 0xA0A0A0]
    ];
    // BRG Profil array
    private $assoBRG = []
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
      [47, '7-stufiger Farbwechsel', '', 0xA0A0A0],
      [48, '7-stufig blitzend', '', 0xA0A0A0],
      [49, 'Rot blitzend', '', 0xFF0000],
      [50, 'Blau blitzend', '', 0x0000FF],
      [51, 'Grün blitzend', '', 0x00FF00],
      [52, 'Violett blitzend', '', 0xFF00FF],
      [53, 'Türkis blitzend', '', 0x00FFFF],
      [54, 'Gelb blitzend', '', 0xFFFF00],
      [55, 'Weiss blitzend', '', 0xFFFFFF]
    ];

    /**
     * Create.
     *
     * @access public
     */
    public function Create()
    {
      // Never delete this line!
      parent::Create();
      // Config Variablen
      $this->RegisterPropertyString("TCPIP", "127.0.0.1");
      $this->RegisterPropertyString("RGB", "012");
      $this->RegisterPropertyBoolean("LOG", false);
  
      // Variablen Profile einrichten
      $this->RegisterProfile(IPSVarType::vtInteger, "MHC.ModeGRB", "Bulb", "", "", 0, 0, 0, 0, $this->assoGRB);
      $this->RegisterProfile(IPSVarType::vtInteger, "MHC.ModeBRG", "Bulb", "", "", 0, 0, 0, 0, $this->assoBRG);
  
      // Variablen erzeugen
      $varID = $this->RegisterVariableBoolean("Power", "Aktiv", "~Switch", 0);
      $this->EnableAction("Power");
      $varID = $this->RegisterVariableInteger("Color", "Farbe", "~HexColor", 1);
      IPS_SetIcon($varID, "Paintbrush");
      $this->EnableAction("Color");
      $varID = $this->RegisterVariableInteger("Speed", "Geschwindigkeit", "~Intensity.100", 2);
      $this->EnableAction("Speed");
      $varID = $this->RegisterVariableInteger("Brightness", "Helligkeit", "~Intensity.100", 3);
      $this->EnableAction("Brightness");
      $varID = $this->RegisterVariableInteger("Mode", "Modus", "MHC.ModeGRB", 4);
      $this->EnableAction("Mode");
    }

  /**
   * Destroy.
   *
   * @access public
   */
  public function Destroy()
  {
      // Never delete this line!
      parent::Destroy();
  }

  /**
   * Apply Configuration Changes.
   *
   * @access public
   */
  public function ApplyChanges()
  {
    // Never delete this line!
    parent::ApplyChanges();

    // Debug message
    $tcpIP = $this->ReadPropertyString("TCPIP");
    $rgbID = $this->ReadPropertyString("RGB");
    $log = $this->ReadPropertyBoolean("LOG");

    $this->SendDebug("ApplyChanges", "IP=".$tcpIP.", RGB=".$rgbID.", LOG=".(int)$log, 0);
    // Debug to Loging
    if ($log) {
      IPS_LogMessage($this->moduleName,"ApplyChanges: IP=".$tcpIP.", RGB=".$rgbID.", LOG=".(int)$log);
    }

    // IP Check
    if (filter_var($this->ReadPropertyString("TCPIP"), FILTER_VALIDATE_IP) !== false) {
      $this->SetStatus(102);
    }
    else {
      $this->SetStatus(201);
    }

    // Setup variable profil
    $varID = $this->GetIDForIdent("Mode");
    if ($rgbID == "012") {
      IPS_SetVariableCustomProfile($varID, "MHC.ModeGRB");
    }
    else {
      IPS_SetVariableCustomProfile($varID, "MHC.ModeBRG");
    }
  }

  /**
   * Call by visual changes
   *
   * @access public
   */
  public function RequestAction($ident, $value)
  {
    // Debug & Logging
    $this->SendDebug("RequestAction", "RequestAction: ($ident,$value)", 0);
    if ($this->ReadPropertyBoolean("LOG")) {
      IPS_LogMessage($this->moduleName,"RequestAction: ($ident,$value)");
    }

    switch($ident) {
      // Switch Power On/Off
      case "Power":
        $on = array(0x71,0x23,0x0f);
        $off = array(0x71,0x24,0x0f);
        if ($value) {
          $this->SendData($on);
        }
        else {
          $this->SendData($off);
        }
        SetValue($this->GetIDForIdent($ident), $value);
        break;
      // Set Speed value
      case "Speed":
        SetValue($this->GetIDForIdent($ident), $value);
        $this->SendFunction();
        break;
      // Set Display Mode
      case "Mode":
        IPS_SetDisabled($this->GetIDForIdent("Speed"),!$value);
        IPS_SetDisabled($this->GetIDForIdent("Color"),$value);
        IPS_SetDisabled($this->GetIDForIdent("Brightness"),$value);
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
      case "Color":
      case "Brightness":
        SetValue($this->GetIDForIdent($ident), $value);
        $this->SendColor();
        break;
      default:
        throw new Exception("Invalid Ident");
    }
  }

  /**
   * This function will be available automatically after the module is imported with the module control.
   * Using the custom prefix this function will be callable from PHP and JSON-RPC through:
   *
   * MHC_SetBrightness(int $InstanzID, int $Brightness);
   *
   * @access public
   */
  public function SetBrightness(int $brightness) {
    $this->RequestAction("Brightness", $brightness);
  }

  /**
   * This function will be available automatically after the module is imported with the module control.
   * Using the custom prefix this function will be callable from PHP and JSON-RPC through:
   *
   * MHC_SetColor(int $InstanzID, int $Color);
   *
   * @access public
   */
  public function SetColor(int $color) {
    $this->RequestAction("Color", $color);
  }

  /**
   * This function will be available automatically after the module is imported with the module control.
   * Using the custom prefix this function will be callable from PHP and JSON-RPC through:
   *
   * MHC_SetMode(int $InstanzID, int $Mode);
   *
   * @access public
   */
  public function SetMode(int $mode) {
    $this->RequestAction("Mode", $mode);
  }

  /**
   * This function will be available automatically after the module is imported with the module control.
   * Using the custom prefix this function will be callable from PHP and JSON-RPC through:
   *
   * MHC_Power(int $InstanzID, bool $Power);
   *
   * @access public
   */
  public function SetPower(bool $power) {
    $this->RequestAction("Power", $power);
  }

  /**
   * Create the profile for the given associations.
   *
   * @access protected
   */
  protected function RegisterProfile($vartype, $name, $icon, $prefix = "", $suffix = "", $minvalue = 0, $maxvalue = 0, $stepsize = 0, $digits = 0, $associations = NULL)
  {
    if (!IPS_VariableProfileExists($name))
    {
      switch ($vartype)
      {
        case IPSVarType::vtBoolean:
          $this->RegisterProfileBoolean($name, $icon, $prefix, $suffix, $associations);
          break;
        case IPSVarType::vtInteger:
          $this->RegisterProfileInteger($name, $icon, $prefix, $suffix, $minvalue, $maxvalue, $stepsize, $digits, $associations);
          break;
        case IPSVarType::vtFloat:
          $this->RegisterProfileFloat($name, $icon, $prefix, $suffix, $minvalue, $maxvalue, $stepsize, $digits, $associations);
          break;
        case IPSVarType::vtString:
          $this->RegisterProfileString($name, $icon);
          break;
      }
    }
    return $name;
  }

  /**
   * RegisterProfileType
   *
   * @access protected
   */
  protected function RegisterProfileType($name, $type)
  {
    if(!IPS_VariableProfileExists($name)) {
      IPS_CreateVariableProfile($name, $type);
    }
    else {
      $profile = IPS_GetVariableProfile($name);
      if($profile['ProfileType'] != $type)
        throw new Exception("Variable profile type does not match for profile ".$name);
    }
  }

  /**
   * RegisterProfileBoolean
   *
   * @access protected
   */
  protected function RegisterProfileBoolean($name, $icon, $prefix, $suffix, $asso)
  {
    $this->RegisterProfileType($name, IPSVarType::vtBoolean);

    IPS_SetVariableProfileIcon($name, $icon);
    IPS_SetVariableProfileText($name, $prefix, $suffix);

    if(sizeof($asso) !== 0){
      foreach($asso as $ass) {
        IPS_SetVariableProfileAssociation($name, $ass[0], $ass[1], $ass[2], $ass[3]);
      }
    }
  }

  /**
   * RegisterProfileInteger
   *
   * @access protected
   */
  protected function RegisterProfileInteger($name, $icon, $prefix, $suffix, $minvalue, $maxvalue, $step, $digits, $asso)
  {
    $this->RegisterProfileType($name, IPSVarType::vtInteger);

    IPS_SetVariableProfileIcon($name, $icon);
    IPS_SetVariableProfileText($name, $prefix, $suffix);
    IPS_SetVariableProfileDigits($name, $digits);

    if(sizeof($asso) === 0){
      $minvalue = 0;
      $maxvalue = 0;
    }
    IPS_SetVariableProfileValues($name, $minvalue, $maxvalue, $step);

    if(sizeof($asso) !== 0){
      foreach($asso as $ass) {
        IPS_SetVariableProfileAssociation($name, $ass[0], $ass[1], $ass[2], $ass[3]);
      }
    }
  }

  /**
   * RegisterProfileFloat
   *
   * @access protected
   */
  protected function RegisterProfileFloat($name, $icon, $prefix, $suffix, $minvalue, $maxvalue, $step, $digits, $asso)
  {
    $this->RegisterProfileType($name, IPSVarType::vtFloat);

    IPS_SetVariableProfileIcon($name, $icon);
    IPS_SetVariableProfileText($name, $prefix, $suffix);
    IPS_SetVariableProfileDigits($name, $digits);

    if(sizeof($asso) === 0){
      $minvalue = 0;
      $maxvalue = 0;
    }
    IPS_SetVariableProfileValues($name, $minvalue, $maxvalue, $step);

    if(sizeof($asso) !== 0){
      foreach($asso as $ass) {
        IPS_SetVariableProfileAssociation($name, $ass[0], $ass[1], $ass[2], $ass[3]);
      }
    }
  }

  /**
   * RegisterProfileString
   *
   * @access protected
   */
  protected function RegisterProfileString($name, $icon, $prefix, $suffix)
  {
    $this->RegisterProfileType($name, IPSVarType::vtString);

    IPS_SetVariableProfileText($name, $prefix, $suffix);
    IPS_SetVariableProfileIcon($name, $icon);
  }

  /**
   * RegisterVariable
   *
   * @access protected
   */
  protected function RegisterVariable($vartype, $name, $ident, $profile, $position, $register)
  {
    if($register == true) {
      switch ($vartype) {
        case IPSVarType::vtBoolean:
          $objId = $this->RegisterVariableBoolean($ident, $name, $profile, $position);
          break;
        case IPSVarType::vtInteger:
          $objId = $this->RegisterVariableInteger($ident, $name, $profile, $position);
          break;
        case IPSVarType::vtFloat:
          $objId = $this->RegisterVariableFloat($ident, $name, $profile, $position);
          break;
        case IPSVarType::vtString:
          $objId = $this->RegisterVariableString($ident, $name, $profile, $position);
          break;
      }
    }
    else {
      $objId = @$this->GetIDForIdent($ident);
      if ($objId > 0) {
        $this->UnregisterVariable($ident);
      }
    }
    return $objId;
  }


  /**
   * Send function data
   *
   * @access privat
   */
  private function SendFunction()
  {
    $data = array(0x61,0x00,0x00,0x0F);
    $mode = GetValue($this->GetIDForIdent("Mode"));
    $speed = 100 - GetValue($this->GetIDForIdent("Speed"));

    if($speed > 100) {
      $speed = 100;
    }
    else if($speed < 1) {
      $speed = 1;
    }

    $data[1] = $mode;
    $data[2] = $speed;

    $this->SendData($data);
  }

  /**
   * Send color data
   *
   * @access privat
   */
  private function SendColor()
  {
    $data = array(0x31,0x00,0x00,0x00,0x00,0x00,0x0F);

    $brightness = GetValue($this->GetIDForIdent("Brightness")) / 100;
    $color = GetValue($this->GetIDForIdent("Color"));

    $rgb = array(0x00, 0x00, 0x00);
    $rgb[0] = (($color >> 16) & 0xFF); // red
    $rgb[1] = (($color >> 8) & 0xFF); // green
    $rgb[2] = ($color & 0xFF); // blue

    // map with brightness
    $rgb[0] *= $brightness;
    $rgb[1] *= $brightness;
    $rgb[2] *= $brightness;

    // map rgb channel
    $channel = $this->ReadPropertyString("RGB");
    $index = (int)$channel[0];
    $this->SendDebug("SendColor", "0 -> $index", 0);
    $data[1] = floor($rgb[$index]);
    $index = (int)$channel[1];
    $this->SendDebug("SendColor", "1 -> $index", 0);
    $data[2] = floor($rgb[$index]);
    $index = (int)$channel[2];
    $this->SendDebug("SendColor", "2 -> $index", 0);
    $data[3] = floor($rgb[$index]);

    // send data
    $this->SendData($data);
  }

  /**
   * Send data array to controller
   *
   * @access privat
   */
  private function SendData($values)
  {
    $path = "tcp://".$this->ReadPropertyString("TCPIP");
    $socket = @fsockopen($path, 5577, $errno, $errstr, 5);
    $log = $this->ReadPropertyBoolean("LOG");

    // Check Socket
    if(!$socket) {
      $this->SendDebug("SendData", $path." -> $errstr ($errno)", 0);
      if ($log) {
        IPS_LogMessage($this->moduleName,"SendData: ". $path." -> $errstr ($errno)");
      }
      return;
    }
    else {
      $this->SendDebug("SendData", "Verbindung aufgebaut '".$path."'", 0);
      if ($log) {
        IPS_LogMessage($this->moduleName,"SendData: Verbindung aufgebaut '".$path."'");
      }
    }

    $send = "";
    foreach($values as $value) {
      $send .= chr($value);
      $data[] = $value;
    }
    $check = $this->GetChecksum($values);

    $send .= chr($check);
    $data[] = $check;

    // send data
    fwrite($socket, $send);

    $this->SendDebug("SendData", "Sende Daten=".join(",",$data), 0);
    if ($log) {
      IPS_LogMessage($this->moduleName,"SendData: Sende Daten=".join(",",$data));
    }
    // close socket
    fclose($socket);
  }

  /**
   * Calculatue checksum for given data
   *
   * @access privat
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

/**
 * Helper class for IPS variable types.
 */
class IPSVarType extends stdClass
{
  const vtNone    = -1;
  const vtBoolean = 0;
  const vtInteger = 1;
  const vtFloat   = 2;
  const vtString  = 3;
}
?>