<?php

declare(strict_types=1);

// Generell funktions
require_once __DIR__ . '/../libs/_traits.php';

// CLASS MagicHomeController
class MagicHomeController extends IPSModule
{
    use DebugHelper;
    use MagicHelper;
    use ProfileHelper;
    use VariableHelper;

    // Socket constants
    private const SOCKET_PORT = 5577;
    private const SOCKET_TIME = 2;

    // Effect Map Profil array (0, 0x25[37] .. 0x38[56])
    private $assoPreset = [
        [0x00, 'Manually', '', 0x000000],
        [0x25, '7-step color sequence', '', 0x000000],
        [0x26, 'pulsing red', '', 0xFF0000],
        [0x27, 'pulsing green', '', 0x00FF00],
        [0x28, 'pulsing blue', '', 0x0000FF],
        [0x29, 'pulsing yellow', '', 0xFFFF00],
        [0x2A, 'pulsing cyan', '', 0x00FFFF],
        [0x2B, 'pulsing purple', '', 0xFF00FF],
        [0x2C, 'pulsing white', '', 0xFFFFFF],
        [0x2D, 'pulsing red + green', '', 0xF0F000],
        [0x2E, 'pulsing red + blue', '', 0xF000F0],
        [0x2F, 'ulsing green + blue', '', 0x00F0F0],
        [0x30, '7-step flashing', '', 0xA0A0A0],
        [0x31, 'flashing red', '', 0xFF0000],
        [0x32, 'flashing green', '', 0x00FF00],
        [0x33, 'flashing blue', '', 0x0000FF],
        [0x34, 'flashing yellow', '', 0xFFFF00],
        [0x35, 'flashing cyan', '', 0x00FFFF],
        [0x36, 'flashing purple', '', 0xFF00FF],
        [0x37, 'flashing white', '', 0xFFFFFF],
        [0x38, '7-step color jumping', '', 0x000000],
    ];

    private $assoOriginal = [
        [0, 'Manually', '', 0x000000],
        [1, 'Circulate all modes', '', 0x000000],
        [2, '7 colors change gradually', '', 0x000000],
        [3, '7 colors run in olivary', '', 0x000000],
        [4, '7 colors change quickly', '', 0x000000],
        [5, '7 colors strobe-flash', '', 0x000000],
        [6, '7 colors running, 1 point from start to end and return back', '', 0x000000],
        [7, '7 colors running, multi points from start to end and return back', '', 0x000000],
        [8, '7 colors overlay, multi points from start to end and return back', '', 0x000000],
        [9, '7 colors overlay, multi points from the middle to the both ends and return back', '', 0x000000],
        [10, '7 colors flow gradually, from start to end and return back', '', 0x000000],
        [11, 'Fading out run, 7 colors from start to end and return back', '', 0x000000],
        [12, 'Runs in olivary, 7 colors from start to end and return back', '', 0x000000],
        [13, 'Fading out run, 7 colors start with white color from start to end and return back', '', 0x000000],
        [14, 'Run circularly, 7 colors with black background, 1point from start to end', '', 0x000000],
        [15, 'Run circularly, 7 colors with red background, 1point from start to end', '', 0x000000],
        [16, 'Run circularly, 7 colors with green background, 1point from start to end', '', 0x000000],
        [17, 'Run circularly, 7 colors with blue background, 1point from start to end', '', 0x000000],
        [18, 'Run circularly, 7 colors with yellow background, 1point from start to end', '', 0x000000],
        [19, 'Run circularly, 7 colors with purple background, 1point from start to end', '', 0x000000],
        [20, 'Run circularly, 7 colors with cyan background, 1point from start to end', '', 0x000000],
        [21, 'Run circularly, 7 colors with white background, 1point from start to end', '', 0x000000],
        [22, 'Run circularly, 7 colors with black background, 1point from end to start', '', 0x000000],
        [23, 'Run circularly, 7 colors with red background, 1point from end to start', '', 0x000000],
        [24, 'Run circularly, 7 colors with green background, 1point from end to start', '', 0x000000],
        [25, 'Run circularly, 7 colors with blue background, 1point from end to start', '', 0x000000],
        [26, 'Run circularly, 7 colors with yellow background, 1point from end to start', '', 0x000000],
        [27, 'Run circularly, 7 colors with purple background, 1point from end to start', '', 0x000000],
        [28, 'Run circularly, 7 colors with cyan background, 1point from end to start', '', 0x000000],
        [29, 'Run circularly, 7 colors with white background, 1point from end to start', '', 0x000000],
        [30, 'Run circularly, 7 colors with black background, 1point from start to end and return back', '', 0x000000],
        [31, 'Run circularly, 7 colors with red background, 1point from start to end and return back', '', 0x000000],
        [32, 'Run circularly, 7 colors with green background, 1point from start to end and return back', '', 0x000000],
        [33, 'Run circularly, 7 colors with blue background, 1point from start to end and return back', '', 0x000000],
        [34, 'Run circularly, 7 colors with yellow background, 1point from start to end and return back', '', 0x000000],
        [35, 'Run circularly, 7 colors with purple background, 1point from start to end and return back', '', 0x000000],
        [36, 'Run circularly, 7 colors with cyan background, 1point from start to end and return back', '', 0x000000],
        [37, 'Run circularly, 7 colors with white background, 1point from start to end and return back', '', 0x000000],
        [38, 'Run circularly, 7 colors with black background, 1point from middle to both ends', '', 0x000000],
        [39, 'Run circularly, 7 colors with red background, 1point from middle to both ends', '', 0x000000],
        [40, 'Run circularly, 7 colors with green background, 1point from middle to both ends', '', 0x000000],
        [41, 'Run circularly, 7 colors with blue background, 1point from middle to both ends', '', 0x000000],
        [42, 'Run circularly, 7 colors with yellow background, 1point from middle to both ends', '', 0x000000],
        [43, 'Run circularly, 7 colors with purple background, 1point from middle to both ends', '', 0x000000],
        [44, 'Run circularly, 7 colors with cyan background, 1point from middle to both ends', '', 0x000000],
        [45, 'Run circularly, 7 colors with white background, 1point from middle to both ends', '', 0x000000],
        [46, 'Run circularly, 7 colors with black background, 1point from both ends to middle', '', 0x000000],
        [47, 'Run circularly, 7 colors with red background, 1point from both ends to middle', '', 0x000000],
        [48, 'Run circularly, 7 colors with green background, 1point from both ends to middle', '', 0x000000],
        [49, 'Run circularly, 7 colors with blue background, 1point from both ends to middle', '', 0x000000],
        [50, 'Run circularly, 7 colors with yellow background, 1point from both ends to middle', '', 0x000000],
        [51, 'Run circularly, 7 colors with purple background, 1point from both ends to middle', '', 0x000000],
        [52, 'Run circularly, 7 colors with cyan background, 1point from both ends to middle', '', 0x000000],
        [53, 'Run circularly, 7 colors with white background, 1point from both ends to middle', '', 0x000000],
        [54, 'Run circularly, 7 colors with black background, 1point from middle to both ends and return back', '', 0x000000],
        [55, 'Run circularly, 7 colors with red background, 1point from middle to both ends and return back', '', 0x000000],
        [56, 'Run circularly, 7 colors with green background, 1point from middle to both ends and return back', '', 0x000000],
        [57, 'Run circularly, 7 colors with blue background, 1point from middle to both ends and return back', '', 0x000000],
        [58, 'Run circularly, 7 colors with yellow background, 1point from middle to both ends and return back', '', 0x000000],
        [59, 'Run circularly, 7 colors with purple background, 1point from middle to both ends and return back', '', 0x000000],
        [60, 'Run circularly, 7 colors with cyan background, 1point from middle to both ends and return back', '', 0x000000],
        [61, 'Run circularly, 7 colors with white background, 1point from middle to both ends and return back', '', 0x000000],
        [203, 'Fading out run circularly, 7 colors each in red fading from start to end', '', 0x000000],
        [204, 'Fading out run circularly, 7 colors each in green fading from start to end', '', 0x000000],
        [205, 'Fading out run circularly, 7 colors each in blue fading from start to end', '', 0x000000],
        [206, 'Fading out run circularly, 7 colors each in yellow fading from start to end', '', 0x000000],
        [207, 'Fading out run circularly, 7 colors each in purple fading from start to end', '', 0x000000],
        [208, 'Fading out run circularly, 7 colors each in cyan fading from start to end', '', 0x000000],
        [209, 'Fading out run circularly, 7 colors each in white fading from start to end', '', 0x000000],
        [210, 'Fading out run circularly, 7 colors each in red fading from end to start', '', 0x000000],
        [211, 'Fading out run circularly, 7 colors each in green fading from end to start', '', 0x000000],
        [212, 'Fading out run circularly, 7 colors each in blue fading from end to start', '', 0x000000],
        [213, 'Fading out run circularly, 7 colors each in yellow fading from end to start', '', 0x000000],
        [214, 'Fading out run circularly, 7 colors each in purple fading from end to start', '', 0x000000],
        [215, 'Fading out run circularly, 7 colors each in cyan fading from end to start', '', 0x000000],
        [216, 'Fading out run circularly, 7 colors each in white fading from end to start', '', 0x000000],
        [217, 'Fading out run circularly, 7 colors each in red fading from start to end and return back', '', 0x000000],
        [218, 'Fading out run circularly, 7 colors each in green fading from start to end and return back', '', 0x000000],
        [219, 'Fading out run circularly, 7 colors each in blue fading from start to end and return back', '', 0x000000],
        [220, 'Fading out run circularly, 7 colors each in yellow fading from start to end and return back', '', 0x000000],
        [221, 'Fading out run circularly, 7 colors each in purple fading from start to end and return back', '', 0x000000],
        [222, 'Fading out run circularly, 7 colors each in cyan fading from start to end and return back', '', 0x000000],
        [223, 'Fading out run circularly, 7 colors each in white fading from start to end and return back', '', 0x000000],
        [224, '7 colors each in red run circularly, multi points from start to end', '', 0x000000],
        [225, '7 colors each in green run circularly, multi points from start to end', '', 0x000000],
        [226, '7 colors each in blue run circularly, multi points from start to end', '', 0x000000],
        [227, '7 colors each in yellow run circularly, multi points from start to end', '', 0x000000],
        [228, '7 colors each in purple run circularly, multi points from start to end', '', 0x000000],
        [229, '7 colors each in cyan run circularly, multi points from start to end', '', 0x000000],
        [230, '7 colors each in white run circularly, multi points from start to end', '', 0x000000],
        [231, '7 colors each in red run circularly, multi points from end to start', '', 0x000000],
        [232, '7 colors each in green run circularly, multi points from end to start', '', 0x000000],
        [233, '7 colors each in blue run circularly, multi points from end to start', '', 0x000000],
        [234, '7 colors each in yellow run circularly, multi points from end to start', '', 0x000000],
        [235, '7 colors each in purple run circularly, multi points from end to start', '', 0x000000],
        [236, '7 colors each in cyan run circularly, multi points from end to start', '', 0x000000],
        [237, '7 colors each in white run circularly, multi points from end to start', '', 0x000000],
        [238, '7 colors each in red run circularly, multi points from start to end and return back', '', 0x000000],
        [239, '7 colors each in green run circularly, multi points from start to end and return back', '', 0x000000],
        [240, '7 colors each in blue run circularly, multi points from start to end and return back', '', 0x000000],
        [241, '7 colors each in yellow run circularly, multi points from start to end and return back', '', 0x000000],
        [242, '7 colors each in purple run circularly, multi points from start to end and return back', '', 0x000000],
        [243, '7 colors each in cyan run circularly, multi points from start to end and return back', '', 0x000000],
        [244, '7 colors each in white run circularly, multi points from start to end and return back', '', 0x000000],
        [266, '7 colors run with black background from start to end', '', 0x000000],
        [267, '7 colors run with red background from start to end', '', 0x000000],
        [268, '7 colors run with green background from start to end', '', 0x000000],
        [269, '7 colors run with blue background from start to end', '', 0x000000],
        [270, '7 colors run with yellow background from start to end', '', 0x000000],
        [271, '7 colors run with purple background from start to end', '', 0x000000],
        [272, '7 colors run with cyan background from start to end', '', 0x000000],
        [273, '7 colors run with white background from start to end', '', 0x000000],
        [274, '7 colors run with black background from end to start', '', 0x000000],
        [275, '7 colors run with red background from end to start', '', 0x000000],
        [276, '7 colors run with green background from end to start', '', 0x000000],
        [277, '7 colors run with blue background from end to start', '', 0x000000],
        [278, '7 colors run with yellow background from end to start', '', 0x000000],
        [279, '7 colors run with purple background from end to start', '', 0x000000],
        [280, '7 colors run with cyan background from end to start', '', 0x000000],
        [281, '7 colors run with white background from end to start', '', 0x000000],
        [291, '7 colors run gradually + 7 colors change quickly', '', 0x000000],
        [292, '7 colors run gradually + 7 colors flash', '', 0x000000],
        [295, '7 colors change quickly + 7 colors flash', '', 0x000000],
        [298, '7 colors run gradually + 7 colors change quickly + 7 colors flash', '', 0x000000],
        [299, '7 colors run in olivary + 7 colors change quickly + 7 colors flash', '', 0x000000],
        [300, '7 colors run gradually + 7 colors run in olivary + 7 colors change quickly + 7 color flash', '', 0x000000],
    ];

    private $assoAddressable = [
        [0, 'Manually', '', 0x000000],
        [1, 'RBM 1', '', 0x000000],
        [2, 'RBM 2', '', 0x000000],
        [3, 'RBM 3', '', 0x000000],
        [4, 'RBM 4', '', 0x000000],
        [5, 'RBM 5', '', 0x000000],
        [6, 'RBM 6', '', 0x000000],
        [7, 'RBM 7', '', 0x000000],
        [8, 'RBM 8', '', 0x000000],
        [9, 'RBM 9', '', 0x000000],
        [10, 'RBM 10', '', 0x000000],
        [11, 'RBM 11', '', 0x000000],
        [12, 'RBM 12', '', 0x000000],
        [13, 'RBM 13', '', 0x000000],
        [14, 'RBM 14', '', 0x000000],
        [15, 'RBM 15', '', 0x000000],
        [16, 'RBM 16', '', 0x000000],
        [17, 'RBM 17', '', 0x000000],
        [18, 'RBM 18', '', 0x000000],
        [19, 'RBM 19', '', 0x000000],
        [20, 'RBM 20', '', 0x000000],
        [21, 'RBM 21', '', 0x000000],
        [22, 'RBM 22', '', 0x000000],
        [23, 'RBM 23', '', 0x000000],
        [24, 'RBM 24', '', 0x000000],
        [25, 'RBM 25', '', 0x000000],
        [26, 'RBM 26', '', 0x000000],
        [27, 'RBM 27', '', 0x000000],
        [28, 'RBM 28', '', 0x000000],
        [29, 'RBM 29', '', 0x000000],
        [30, 'RBM 30', '', 0x000000],
        [31, 'RBM 31', '', 0x000000],
        [32, 'RBM 32', '', 0x000000],
        [33, 'RBM 33', '', 0x000000],
        [34, 'RBM 34', '', 0x000000],
        [35, 'RBM 35', '', 0x000000],
        [36, 'RBM 36', '', 0x000000],
        [37, 'RBM 37', '', 0x000000],
        [38, 'RBM 38', '', 0x000000],
        [39, 'RBM 39', '', 0x000000],
        [40, 'RBM 40', '', 0x000000],
        [41, 'RBM 41', '', 0x000000],
        [42, 'RBM 42', '', 0x000000],
        [43, 'RBM 43', '', 0x000000],
        [44, 'RBM 44', '', 0x000000],
        [45, 'RBM 45', '', 0x000000],
        [46, 'RBM 46', '', 0x000000],
        [47, 'RBM 47', '', 0x000000],
        [48, 'RBM 48', '', 0x000000],
        [49, 'RBM 49', '', 0x000000],
        [50, 'RBM 50', '', 0x000000],
        [51, 'RBM 51', '', 0x000000],
        [52, 'RBM 52', '', 0x000000],
        [53, 'RBM 53', '', 0x000000],
        [54, 'RBM 54', '', 0x000000],
        [55, 'RBM 55', '', 0x000000],
        [56, 'RBM 56', '', 0x000000],
        [57, 'RBM 57', '', 0x000000],
        [58, 'RBM 58', '', 0x000000],
        [59, 'RBM 59', '', 0x000000],
        [60, 'RBM 60', '', 0x000000],
        [61, 'RBM 61', '', 0x000000],
        [62, 'RBM 62', '', 0x000000],
        [63, 'RBM 63', '', 0x000000],
        [64, 'RBM 64', '', 0x000000],
        [65, 'RBM 65', '', 0x000000],
        [66, 'RBM 66', '', 0x000000],
        [67, 'RBM 67', '', 0x000000],
        [68, 'RBM 68', '', 0x000000],
        [69, 'RBM 69', '', 0x000000],
        [70, 'RBM 70', '', 0x000000],
        [71, 'RBM 71', '', 0x000000],
        [72, 'RBM 72', '', 0x000000],
        [73, 'RBM 73', '', 0x000000],
        [74, 'RBM 74', '', 0x000000],
        [75, 'RBM 75', '', 0x000000],
        [76, 'RBM 76', '', 0x000000],
        [77, 'RBM 77', '', 0x000000],
        [78, 'RBM 78', '', 0x000000],
        [79, 'RBM 79', '', 0x000000],
        [80, 'RBM 80', '', 0x000000],
        [81, 'RBM 81', '', 0x000000],
        [82, 'RBM 82', '', 0x000000],
        [83, 'RBM 83', '', 0x000000],
        [84, 'RBM 84', '', 0x000000],
        [85, 'RBM 85', '', 0x000000],
        [86, 'RBM 86', '', 0x000000],
        [87, 'RBM 87', '', 0x000000],
        [88, 'RBM 88', '', 0x000000],
        [89, 'RBM 89', '', 0x000000],
        [90, 'RBM 90', '', 0x000000],
        [91, 'RBM 91', '', 0x000000],
        [92, 'RBM 92', '', 0x000000],
        [93, 'RBM 93', '', 0x000000],
        [94, 'RBM 94', '', 0x000000],
        [95, 'RBM 95', '', 0x000000],
        [96, 'RBM 96', '', 0x000000],
        [97, 'RBM 97', '', 0x000000],
        [98, 'RBM 98', '', 0x000000],
        [99, 'RBM 99', '', 0x000000],
        [100, 'RBM 100', '', 0x000000],
        [101, 'RBM 101', '', 0x000000],  # Not in the Magic Home App (only set by remote)
        [102, 'RBM 102', '', 0x000000],  # Not in the Magic Home App (only set by remote)
        [255, 'Circulate all modes', '', 0x000000],  # Cycles all
    ];

    /**
     * Create.
     */
    public function Create()
    {
        // Never delete this line!
        parent::Create();

        // Device Variablen
        $this->RegisterPropertyInteger('TYPE', 51);
        $this->RegisterPropertyString('MODEL', 'Unknown');
        $this->RegisterPropertyString('TCPIP', '127.0.0.1');
        $this->RegisterPropertyString('MAC', '');
        $this->RegisterPropertyString('RGB', '012');

        // Variablen Profile einrichten
        $this->RegisterProfile(vtInteger, 'MHC.Preset', 'Bulb', '', '', 0, 0, 0, 0, $this->assoPreset);
        $this->RegisterProfile(vtInteger, 'MHC.Original', 'Bulb', '', '', 0, 0, 0, 0, $this->assoOriginal);
        $this->RegisterProfile(vtInteger, 'MHC.Addressable', 'Bulb', '', '', 0, 0, 0, 0, $this->assoAddressable);

        // Variablen erzeugen
        $varID = $this->RegisterVariableBoolean('Power', $this->Translate('Power'), '~Switch', 0);
        $this->EnableAction('Power');
        $varID = $this->RegisterVariableInteger('Color', $this->Translate('Color'), '~HexColor', 1);
        $this->EnableAction('Color');
        $varID = $this->RegisterVariableInteger('Speed', $this->Translate('Speed'), '~Intensity.100', 2);
        $this->EnableAction('Speed');
        $varID = $this->RegisterVariableInteger('Brightness', $this->Translate('Brightness'), '~Intensity.100', 3);
        $this->EnableAction('Brightness');
        $varID = $this->RegisterVariableInteger('Mode', $this->Translate('Mode'), 'MHC.Preset', 4);
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

        // Values
        $type = $this->ReadPropertyInteger('TYPE');
        $tcpip = $this->ReadPropertyString('TCPIP');
        $rgb = $this->ReadPropertyString('RGB');

        // IP Check
        if (filter_var($tcpip, FILTER_VALIDATE_IP) !== false) {
            $this->SetStatus(102);
        } else {
            $this->SetStatus(201);
        }

        // Setup variable profil
        $this->RegisterVariableInteger('Mode', $this->Translate('Mode'), $this->GetPatternProfile($type), 4);

        // Debug message
        $this->SendDebug(__FUNCTION__, 'TYPE=0x' . dechex($type) . ', IP=' . $tcpip . ', RGB=' . $rgb, 0);
    }

    /**
     * Call by visual changes.
     */
    public function RequestAction($ident, $value)
    {
        // Debug
        $this->SendDebug(__FUNCTION__, $ident . ' => ' . $value);
        switch ($ident) {
            // Switch Power On/Off
            case 'Power':
                $this->SetValueBoolean($ident, $value);
                $this->SendPower($value);
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
                // Brightness depend on protocol
                $type = $this->ReadPropertyInteger('TYPE');
                $prot = MAGIC_HOME_CONTROLLER[$type][1];
                if (in_array($prot, BRIGHTNESS_EFFECTS_PROTOCOLS)) {
                    $this->SetVariableDisabled('Brightness', false);
                } else {
                    $this->SetVariableDisabled('Brightness', $disabled);
                }
                $this->SetValueInteger($ident, $value);
                // Manual or Functional mode
                if ($value == 0) {
                    $this->SendColor();
                } else {
                    $this->SendFunction();
                }
                break;
            case 'Color':
            case 'Brightness':
                $this->SetValueInteger($ident, $value);
                $mode = $this->GetValue('Mode');
                // Manual or Functional mode
                if ($mode == 0) {
                    $this->SendColor();
                } else {
                    $this->SendFunction();
                }
                break;
            case 'Syncronize':
                $this->SendSync();
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
     * This function will be available automatically after the module is imported with the module control.
     * Using the custom prefix this function will be callable from PHP and JSON-RPC through:.
     *
     * MHC_SetSpeed(int $InstanzID, int $Speed);
     */
    public function SetSpeed(int $speed)
    {
        $this->RequestAction('Speed', $speed);
    }

    /**
     * Send power state data.
     */
    private function SendPower($value)
    {
        $type = $this->ReadPropertyInteger('TYPE');
        $class = CLASS_PROTOCOL[MAGIC_HOME_CONTROLLER[$type][1]];
        $protocol = new $class();
        $state = $protocol->ConstructStateChange($value);
        $this->SendData($state);
    }

    /**
     * Send function data.
     */
    private function SendFunction()
    {
        // Get function code
        $pattern = $this->GetValue('Mode');
        if ($pattern == 0) {
            return; // Only if code not manually
        }
        $speed = $this->GetValue('Speed');
        $brightness = $this->GetValue('Brightness'); // / 100;
        // protocol
        $type = $this->ReadPropertyInteger('TYPE');
        $class = CLASS_PROTOCOL[MAGIC_HOME_CONTROLLER[$type][1]];
        $protocol = new $class();
        $data = $protocol->ConstructPresetPattern($pattern, $speed, $brightness);
        // send data
        $this->SendData($data);
    }

    /**
     * Send color data.
     */
    private function SendColor()
    {
        // Check mode
        $mode = $this->GetValue('Mode');
        if ($mode != 0) {
            return; // Only if code not manually
        }
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
        $r = floor($rgb[$index]);
        $index = (int) $channel[1];
        $this->SendDebug(__FUNCTION__, "1 -> $index", 0);
        $g = floor($rgb[$index]);
        $index = (int) $channel[2];
        $this->SendDebug(__FUNCTION__, "2 -> $index", 0);
        $b = floor($rgb[$index]);
        // protocol
        $type = $this->ReadPropertyInteger('TYPE');
        $class = CLASS_PROTOCOL[MAGIC_HOME_CONTROLLER[$type][1]];
        $protocol = new $class();
        $data = $protocol->ConstructLevelsChange(true, $r, $g, $b, 0x00, 0x00, 0x00);
        // send data
        $this->SendData($data);
    }

    /**
     * Sync controller state to variables.
     */
    private function SendSync()
    {
        // Send Message ***************************************************************
        $type = $this->ReadPropertyInteger('TYPE');
        $class = CLASS_PROTOCOL[MAGIC_HOME_CONTROLLER[$type][1]];
        $protocol = new $class();
        $query = $protocol->ConstructStateQuery();
        $data = $this->SendData($query, 2);
        $this->SendDebug(__FUNCTION__, bin2hex($data), 0);
        if (strlen($data) != $protocol->StateResponseLength()) {
            $this->SendDebug(__FUNCTION__, 'No sync possible!');
            return;
        }

        // Convert to array (ONE based)
        $rx = unpack('C*', $data);
        $this->SendDebug(__FUNCTION__, $rx);
        // Check controler type
        if ($rx[2] != $type) {
            $this->LogMessage('Wrong controller type:' . $rx[2], KL_ERROR);
        }

        // Check power state ***********************************************************
        $this->SetValueBoolean('Power', ($rx[3] == 0x23));

        // Check mode ******************************************************************
        $pattern = $rx[4];
        $this->SendDebug(__FUNCTION__, 'Pattern: ' . $pattern);
        // Switches
        if (in_array($protocol->Id(), MAGIC_HOME_SWITCHES)) {
            $this->SendDebug(__FUNCTION__, 'Sync for switches not full supported!');
            return;
        }
        // Custom Effects
        if ($pattern == EFFECT_CUSTOM_CODE) {
            $this->SendDebug(__FUNCTION__, 'Controler im custom effect mode - not supported!');
            return;
        }
        // Custom Effects
        if ($pattern == PRESET_MUSIC_MODE) {
            $this->SendDebug(__FUNCTION__, 'Controler im music mode - not supported!');
            return;
        }
        // Color Mode
        if (in_array($pattern, [0x41, 0x61])) {
            $mode = 0; // Manuel
        } elseif (($pattern >= 0x25) && ($pattern <= 0x38)) {
            $mode = $rx[5];
            if (in_array($protocol->Id(), ORIGINAL_EFFECTS_PROTOCOLS)) {
                $mode = ($pattern << 8) + $mode - 99;
            } elseif (in_array($protocol->Id(), ADDRESSABLE_EFFECTS_PROTOCOLS)) {
                if ($pattern == 0x25) {
                    // mode == $mode
                }
                if ($pattern == 0x24) {
                    $this->SendDebug(__FUNCTION__, 'Controler in multi color effect mode - not supported!');
                    return;
                }
            } else {
                $mode = $pattern;
            }
        } elseif (in_array($protocol->Id(), ADDRESSABLE_PROTOCOLS)) {
            $mode = $rx[5];
            if ($mode == 0x61) {
                $mode = 0;
            } else {
                $mode = $mode - 99; // or ($pattern << 8) + $mode - 99;
            }
        }
        $disabled = ($mode > 0) ? true : false;
        $this->SetVariableDisabled('Speed', !$disabled);
        $this->SetVariableDisabled('Color', $disabled);
        if (in_array($protocol->Id(), BRIGHTNESS_EFFECTS_PROTOCOLS)) {
            $this->SetVariableDisabled('Brightness', false);
        } else {
            $this->SetVariableDisabled('Brightness', $disabled);
        }
        $this->SendDebug(__FUNCTION__, 'Mode = ' . $mode);
        $this->SetValueInteger('Mode', $mode);

        // Check speed ******************************************************************
        $speed = $rx[6];
        if (!in_array($protocol->Id(), ADDRESSABLE_PROTOCOLS)) {
            $speed = $protocol::DelayToSpeed($rx[6]);
        }
        $this->SendDebug(__FUNCTION__, 'Speed = ' . $speed);
        $this->SetValueInteger('Speed', $speed);

        // Check brithness *************************************************************
        $update = false;
        if($mode == 0) {
            // (grössten finden; wenn 0 = weiß mit Helligkeit 0; sonst (max-wert/255) * 100)
            $max = max($rx[7], $rx[8], $rx[9]);
            $this->SendDebug(__FUNCTION__, 'Max = ' . $max);
            $div = $max / 255;
            $this->SendDebug(__FUNCTION__, 'Div = ' . $div);
            $brightness = $div * 100;
            $update = true;
        }
        elseif(in_array($protocol->Id(), BRIGHTNESS_EFFECTS_PROTOCOLS)) {
            // the red byte holds the brightness during an effect
            $brightness = $rx[7];
            $update = true;
        }
        if($update) {
            $this->SendDebug(__FUNCTION__, 'Brightness = ' . $brightness);
            if ($brightness > 100) {
                $brightness = 100;
            }
            $this->SetValueInteger('Brightness', intval($brightness));
        }
        // Check color *****************************************************************
        if($mode == 0) {
            $channel = $this->ReadPropertyString('RGB');
            $red = $rx[7 + $channel[0]] / $div;
            if ($red < 0) {
                $red = 0;
            }
            if ($red > 255) {
                $red = 255;
            }
            $color = intval($red) << 16;
            $green = $rx[7 + $channel[1]] / $div;
            if ($green < 0) {
                $green = 0;
            }
            if ($green > 255) {
                $green = 255;
            }
            $color += intval($green) << 8;
            $blue = $rx[7 + $channel[2]] / $div;
            if ($blue < 0) {
                $blue = 0;
            }
            if ($blue > 255) {
                $blue = 255;
            }
            $color += intval($blue);
            $this->SendDebug(__FUNCTION__, 'Color = 0x' . dechex($red) . dechex($green) . dechex($blue) . ' (' . $color . ')');
            $this->SetValueInteger('Color', $color);
        }
    }

    /**
     * Send data array to controller.
     *
     * @param array $values Configuration Data
     */
    private function SendData(array $values, int $read = 0): string
    {
        $data = '';
        $path = 'tcp://' . $this->ReadPropertyString('TCPIP');
        $socket = @fsockopen($path, self::SOCKET_PORT, $errno, $errstr, self::SOCKET_TIME);
        // Check Socket
        if (!$socket) {
            $this->SendDebug(__FUNCTION__, $path . " -> $errstr ($errno)", 0);
            return $data;
        } else {
            $this->SendDebug(__FUNCTION__, 'Connection etablished: ' . $path, 0);
        }
        $send = '';
        //$this->SendDebug(__FUNCTION__, 'Values=' . print_r($values, true));
        foreach ($values as $value) {
            $send .= chr(intval($value));
        }
        // send data
        fwrite($socket, $send);
        $this->SendDebug(__FUNCTION__, 'Send=' . bin2hex($send), 0);

        // read data
        if ($read != 0) {
            stream_set_timeout($socket, self::SOCKET_TIME);
            while (true) {
                $recv = fread($socket, $read);
                if (($recv === false) || (strlen($recv) != $read)) {
                    break;
                }
                $this->SendDebug(__FUNCTION__, 'Read=' . bin2hex($recv), 0);
                $data .= $recv;
            }
        }

        // close socket
        fclose($socket);

        // return rad data
        return $data;
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
}
