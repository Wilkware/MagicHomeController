<?php

declare(strict_types=1);

/** General functions */
require_once __DIR__ . '/../libs/_traits.php';

/** Namespaced traits */
use Wilkware\MagicHomeController\DebugHelper;
use Wilkware\MagicHomeController\FormHelper;
use Wilkware\MagicHomeController\MagicHelper;
use Wilkware\MagicHomeController\ProtocolBase;
use Wilkware\MagicHomeController\VariableHelper;

/**
 * CLASS MagicHomeController
 */
class MagicHomeController extends IPSModuleStrict
{
    // -------------------------------------------------------------------------
    // Traits
    // -------------------------------------------------------------------------

    use DebugHelper;
    use FormHelper;
    use MagicHelper;
    use VariableHelper;

    /**
     * Mode presentations which differ from the default preset presentation, keyed by controller type.
     *
     * @var array<string,array{presentation:array<string,mixed>,protocols:int[]}>
     */
    public const MHC_PRESENTATION_PATTERN = [
        'Original' => [
            'presentation' => self::MHC_PRESENTATION_ORIGINAL,
            'protocols'    => [0xA1],
        ],
        'Addressable' => [
            'presentation' => self::MHC_PRESENTATION_ADDRESSABLE,
            'protocols'    => [0xA2, 0xA3],
        ],
    ];

    // -------------------------------------------------------------------------
    // Socket Constants
    // -------------------------------------------------------------------------

    /** @var int Socket Port */
    private const SOCKET_PORT = 5577;

    /** @var int Socket Time */
    private const SOCKET_TIME = 2;

    // -------------------------------------------------------------------------
    // Status Constants
    // -------------------------------------------------------------------------

    /** @var int Controller not reachable */
    private const STATUS_UNREACHABLE = 200;

    /** @var int Wrong IP format */
    private const STATUS_WRONG_IP = 201;

    /** @var int Unknown controller type */
    private const STATUS_WRONG_TYPE = 202;

    // -------------------------------------------------------------------------
    // Presentations
    // -------------------------------------------------------------------------

    /**
     * @var array<string,mixed> Switch Presentation (Switch)
     */
    private const MHC_PRESENTATION_SWITCH = [
        'GLOW_COLOR'     => 16771899,
        'GLOW_INTENSITY' => 50,
        'ICON_FALSE'     => 'lightbulb',
        'ICON_TRUE'      => 'lightbulb-on',
        'PRESENTATION'   => VARIABLE_PRESENTATION_SWITCH,
        'USAGE_TYPE'     => 0,
        'USE_ICON_FALSE' => true,
    ];

    /**
     * @var array<string,mixed> HexColor Presentation (Color)
     */
    private const MHC_PRESENTATION_COLOR = [
        'COLOR_CURVE'   => 0,
        'COLOR_SPACE'   => 1,
        'ENCODING'      => 0,
        'PRESENTATION'  => VARIABLE_PRESENTATION_COLOR,
        'PRESET_VALUES' => '[{"Color":16007990},{"Color":16761095},{"Color":10233776},{"Color":48340},{"Color":2201331},{"Color":15277667}]',
        'SELECTION'     => 0,
    ];

    /**
     * @var array<string,mixed> Brightness Presentation (Slider)
     */
    private const MHC_PRESENTATION_BRIGHTNESS = [
        'ICON'         => 'sun',
        'MAX'          => 100,
        'MIN'          => 0,
        'PERCENTAGE'   => true,
        'PRESENTATION' => VARIABLE_PRESENTATION_SLIDER,
        'STEP_SIZE'    => 1.0,
        'SUFFIX'       => ' %',
        'USAGE_TYPE'   => 2,
    ];

    /**
     * @var array<string,mixed> Speed Presentation (Slider)
     */
    private const MHC_PRESENTATION_SPEED = [
        'ICON'         => 'gauge-high',
        'MAX'          => 100,
        'MIN'          => 0,
        'PERCENTAGE'   => true,
        'PRESENTATION' => VARIABLE_PRESENTATION_SLIDER,
        'STEP_SIZE'    => 1.0,
        'SUFFIX'       => ' %',
        'USAGE_TYPE'   => 5,
    ];

    private const MHC_PRESENTATION_PRESET = [
        'ICON'         => 'wand-magic-sparkles',
        'OPTIONS'      => '[{"Caption":"Manually","Color":-1,"IconActive":false,"IconValue":"","Value":0},{"Caption":"7-step color sequence","Color":-1,"IconActive":false,"IconValue":"","Value":37},{"Caption":"pulsing red","Color":16711680,"IconActive":false,"IconValue":"","Value":38},{"Caption":"pulsing green","Color":65280,"IconActive":false,"IconValue":"","Value":39},{"Caption":"pulsing blue","Color":255,"IconActive":false,"IconValue":"","Value":40},{"Caption":"pulsing yellow","Color":16776960,"IconActive":false,"IconValue":"","Value":41},{"Caption":"pulsing cyan","Color":65535,"IconActive":false,"IconValue":"","Value":42},{"Caption":"pulsing purple","Color":16711935,"IconActive":false,"IconValue":"","Value":43},{"Caption":"pulsing white","Color":16777215,"IconActive":false,"IconValue":"","Value":44},{"Caption":"pulsing red + green","Color":15790080,"IconActive":false,"IconValue":"","Value":45},{"Caption":"pulsing red + blue","Color":15728880,"IconActive":false,"IconValue":"","Value":46},{"Caption":"pulsing green + blue","Color":61680,"IconActive":false,"IconValue":"","Value":47},{"Caption":"7-step flashing","Color":10526880,"IconActive":false,"IconValue":"","Value":48},{"Caption":"flashing red","Color":16711680,"IconActive":false,"IconValue":"","Value":49},{"Caption":"flashing green","Color":65280,"IconActive":false,"IconValue":"","Value":50},{"Caption":"flashing blue","Color":255,"IconActive":false,"IconValue":"","Value":51},{"Caption":"flashing yellow","Color":16776960,"IconActive":false,"IconValue":"","Value":52},{"Caption":"flashing cyan","Color":65535,"IconActive":false,"IconValue":"","Value":53},{"Caption":"flashing purple","Color":16711935,"IconActive":false,"IconValue":"","Value":54},{"Caption":"flashing white","Color":16777215,"IconActive":false,"IconValue":"","Value":55},{"Caption":"7-step color jumping","Color":-1,"IconActive":false,"IconValue":"","Value":56}]',
        'PRESENTATION' => VARIABLE_PRESENTATION_ENUMERATION,
    ];

    /**
     * @var array<string,mixed> Original Presentation (Enumeration)
     */
    private const MHC_PRESENTATION_ORIGINAL = [
        'ICON'         => 'wand-magic-sparkles',
        'OPTIONS'      => '[{"Caption":"Manually","Color":-1,"IconActive":false,"IconValue":"","Value":0},{"Caption":"Circulate all modes","Color":-1,"IconActive":false,"IconValue":"","Value":1},{"Caption":"7 colors change gradually","Color":-1,"IconActive":false,"IconValue":"","Value":2},{"Caption":"7 colors run in olivary","Color":-1,"IconActive":false,"IconValue":"","Value":3},{"Caption":"7 colors change quickly","Color":-1,"IconActive":false,"IconValue":"","Value":4},{"Caption":"7 colors strobe-flash","Color":-1,"IconActive":false,"IconValue":"","Value":5},{"Caption":"7 colors running, 1 point from start to end and return back","Color":-1,"IconActive":false,"IconValue":"","Value":6},{"Caption":"7 colors running, multi points from start to end and return back","Color":-1,"IconActive":false,"IconValue":"","Value":7},{"Caption":"7 colors overlay, multi points from start to end and return back","Color":-1,"IconActive":false,"IconValue":"","Value":8},{"Caption":"7 colors overlay, multi points from the middle to the both ends and return back","Color":-1,"IconActive":false,"IconValue":"","Value":9},{"Caption":"7 colors flow gradually, from start to end and return back","Color":-1,"IconActive":false,"IconValue":"","Value":10},{"Caption":"Fading out run, 7 colors from start to end and return back","Color":-1,"IconActive":false,"IconValue":"","Value":11},{"Caption":"Runs in olivary, 7 colors from start to end and return back","Color":-1,"IconActive":false,"IconValue":"","Value":12},{"Caption":"Fading out run, 7 colors start with white color from start to end and return back","Color":-1,"IconActive":false,"IconValue":"","Value":13},{"Caption":"Run circularly, 7 colors with black background, 1point from start to end","Color":-1,"IconActive":false,"IconValue":"","Value":14},{"Caption":"Run circularly, 7 colors with red background, 1point from start to end","Color":-1,"IconActive":false,"IconValue":"","Value":15},{"Caption":"Run circularly, 7 colors with green background, 1point from start to end","Color":-1,"IconActive":false,"IconValue":"","Value":16},{"Caption":"Run circularly, 7 colors with blue background, 1point from start to end","Color":-1,"IconActive":false,"IconValue":"","Value":17},{"Caption":"Run circularly, 7 colors with yellow background, 1point from start to end","Color":-1,"IconActive":false,"IconValue":"","Value":18},{"Caption":"Run circularly, 7 colors with purple background, 1point from start to end","Color":-1,"IconActive":false,"IconValue":"","Value":19},{"Caption":"Run circularly, 7 colors with cyan background, 1point from start to end","Color":-1,"IconActive":false,"IconValue":"","Value":20},{"Caption":"Run circularly, 7 colors with white background, 1point from start to end","Color":-1,"IconActive":false,"IconValue":"","Value":21},{"Caption":"Run circularly, 7 colors with black background, 1point from end to start","Color":-1,"IconActive":false,"IconValue":"","Value":22},{"Caption":"Run circularly, 7 colors with red background, 1point from end to start","Color":-1,"IconActive":false,"IconValue":"","Value":23},{"Caption":"Run circularly, 7 colors with green background, 1point from end to start","Color":-1,"IconActive":false,"IconValue":"","Value":24},{"Caption":"Run circularly, 7 colors with blue background, 1point from end to start","Color":-1,"IconActive":false,"IconValue":"","Value":25},{"Caption":"Run circularly, 7 colors with yellow background, 1point from end to start","Color":-1,"IconActive":false,"IconValue":"","Value":26},{"Caption":"Run circularly, 7 colors with purple background, 1point from end to start","Color":-1,"IconActive":false,"IconValue":"","Value":27},{"Caption":"Run circularly, 7 colors with cyan background, 1point from end to start","Color":-1,"IconActive":false,"IconValue":"","Value":28},{"Caption":"Run circularly, 7 colors with white background, 1point from end to start","Color":-1,"IconActive":false,"IconValue":"","Value":29},{"Caption":"Run circularly, 7 colors with black background, 1point from start to end and return back","Color":-1,"IconActive":false,"IconValue":"","Value":30},{"Caption":"Run circularly, 7 colors with red background, 1point from start to end and return back","Color":-1,"IconActive":false,"IconValue":"","Value":31},{"Caption":"Run circularly, 7 colors with green background, 1point from start to end and return back","Color":-1,"IconActive":false,"IconValue":"","Value":32},{"Caption":"Run circularly, 7 colors with blue background, 1point from start to end and return back","Color":-1,"IconActive":false,"IconValue":"","Value":33},{"Caption":"Run circularly, 7 colors with yellow background, 1point from start to end and return back","Color":-1,"IconActive":false,"IconValue":"","Value":34},{"Caption":"Run circularly, 7 colors with purple background, 1point from start to end and return back","Color":-1,"IconActive":false,"IconValue":"","Value":35},{"Caption":"Run circularly, 7 colors with cyan background, 1point from start to end and return back","Color":-1,"IconActive":false,"IconValue":"","Value":36},{"Caption":"Run circularly, 7 colors with white background, 1point from start to end and return back","Color":-1,"IconActive":false,"IconValue":"","Value":37},{"Caption":"Run circularly, 7 colors with black background, 1point from middle to both ends","Color":-1,"IconActive":false,"IconValue":"","Value":38},{"Caption":"Run circularly, 7 colors with red background, 1point from middle to both ends","Color":-1,"IconActive":false,"IconValue":"","Value":39},{"Caption":"Run circularly, 7 colors with green background, 1point from middle to both ends","Color":-1,"IconActive":false,"IconValue":"","Value":40},{"Caption":"Run circularly, 7 colors with blue background, 1point from middle to both ends","Color":-1,"IconActive":false,"IconValue":"","Value":41},{"Caption":"Run circularly, 7 colors with yellow background, 1point from middle to both ends","Color":-1,"IconActive":false,"IconValue":"","Value":42},{"Caption":"Run circularly, 7 colors with purple background, 1point from middle to both ends","Color":-1,"IconActive":false,"IconValue":"","Value":43},{"Caption":"Run circularly, 7 colors with cyan background, 1point from middle to both ends","Color":-1,"IconActive":false,"IconValue":"","Value":44},{"Caption":"Run circularly, 7 colors with white background, 1point from middle to both ends","Color":-1,"IconActive":false,"IconValue":"","Value":45},{"Caption":"Run circularly, 7 colors with black background, 1point from both ends to middle","Color":-1,"IconActive":false,"IconValue":"","Value":46},{"Caption":"Run circularly, 7 colors with red background, 1point from both ends to middle","Color":-1,"IconActive":false,"IconValue":"","Value":47},{"Caption":"Run circularly, 7 colors with green background, 1point from both ends to middle","Color":-1,"IconActive":false,"IconValue":"","Value":48},{"Caption":"Run circularly, 7 colors with blue background, 1point from both ends to middle","Color":-1,"IconActive":false,"IconValue":"","Value":49},{"Caption":"Run circularly, 7 colors with yellow background, 1point from both ends to middle","Color":-1,"IconActive":false,"IconValue":"","Value":50},{"Caption":"Run circularly, 7 colors with purple background, 1point from both ends to middle","Color":-1,"IconActive":false,"IconValue":"","Value":51},{"Caption":"Run circularly, 7 colors with cyan background, 1point from both ends to middle","Color":-1,"IconActive":false,"IconValue":"","Value":52},{"Caption":"Run circularly, 7 colors with white background, 1point from both ends to middle","Color":-1,"IconActive":false,"IconValue":"","Value":53},{"Caption":"Run circularly, 7 colors with black background, 1point from middle to both ends and return back","Color":-1,"IconActive":false,"IconValue":"","Value":54},{"Caption":"Run circularly, 7 colors with red background, 1point from middle to both ends and return back","Color":-1,"IconActive":false,"IconValue":"","Value":55},{"Caption":"Run circularly, 7 colors with green background, 1point from middle to both ends and return back","Color":-1,"IconActive":false,"IconValue":"","Value":56},{"Caption":"Run circularly, 7 colors with blue background, 1point from middle to both ends and return back","Color":-1,"IconActive":false,"IconValue":"","Value":57},{"Caption":"Run circularly, 7 colors with yellow background, 1point from middle to both ends and return back","Color":-1,"IconActive":false,"IconValue":"","Value":58},{"Caption":"Run circularly, 7 colors with purple background, 1point from middle to both ends and return back","Color":-1,"IconActive":false,"IconValue":"","Value":59},{"Caption":"Run circularly, 7 colors with cyan background, 1point from middle to both ends and return back","Color":-1,"IconActive":false,"IconValue":"","Value":60},{"Caption":"Run circularly, 7 colors with white background, 1point from middle to both ends and return back","Color":-1,"IconActive":false,"IconValue":"","Value":61},{"Caption":"Fading out run circularly, 7 colors each in red fading from start to end","Color":-1,"IconActive":false,"IconValue":"","Value":203},{"Caption":"Fading out run circularly, 7 colors each in green fading from start to end","Color":-1,"IconActive":false,"IconValue":"","Value":204},{"Caption":"Fading out run circularly, 7 colors each in blue fading from start to end","Color":-1,"IconActive":false,"IconValue":"","Value":205},{"Caption":"Fading out run circularly, 7 colors each in yellow fading from start to end","Color":-1,"IconActive":false,"IconValue":"","Value":206},{"Caption":"Fading out run circularly, 7 colors each in purple fading from start to end","Color":-1,"IconActive":false,"IconValue":"","Value":207},{"Caption":"Fading out run circularly, 7 colors each in cyan fading from start to end","Color":-1,"IconActive":false,"IconValue":"","Value":208},{"Caption":"Fading out run circularly, 7 colors each in white fading from start to end","Color":-1,"IconActive":false,"IconValue":"","Value":209},{"Caption":"Fading out run circularly, 7 colors each in red fading from end to start","Color":-1,"IconActive":false,"IconValue":"","Value":210},{"Caption":"Fading out run circularly, 7 colors each in green fading from end to start","Color":-1,"IconActive":false,"IconValue":"","Value":211},{"Caption":"Fading out run circularly, 7 colors each in blue fading from end to start","Color":-1,"IconActive":false,"IconValue":"","Value":212},{"Caption":"Fading out run circularly, 7 colors each in yellow fading from end to start","Color":-1,"IconActive":false,"IconValue":"","Value":213},{"Caption":"Fading out run circularly, 7 colors each in purple fading from end to start","Color":-1,"IconActive":false,"IconValue":"","Value":214},{"Caption":"Fading out run circularly, 7 colors each in cyan fading from end to start","Color":-1,"IconActive":false,"IconValue":"","Value":215},{"Caption":"Fading out run circularly, 7 colors each in white fading from end to start","Color":-1,"IconActive":false,"IconValue":"","Value":216},{"Caption":"Fading out run circularly, 7 colors each in red fading from start to end and return back","Color":-1,"IconActive":false,"IconValue":"","Value":217},{"Caption":"Fading out run circularly, 7 colors each in green fading from start to end and return back","Color":-1,"IconActive":false,"IconValue":"","Value":218},{"Caption":"Fading out run circularly, 7 colors each in blue fading from start to end and return back","Color":-1,"IconActive":false,"IconValue":"","Value":219},{"Caption":"Fading out run circularly, 7 colors each in yellow fading from start to end and return back","Color":-1,"IconActive":false,"IconValue":"","Value":220},{"Caption":"Fading out run circularly, 7 colors each in purple fading from start to end and return back","Color":-1,"IconActive":false,"IconValue":"","Value":221},{"Caption":"Fading out run circularly, 7 colors each in cyan fading from start to end and return back","Color":-1,"IconActive":false,"IconValue":"","Value":222},{"Caption":"Fading out run circularly, 7 colors each in white fading from start to end and return back","Color":-1,"IconActive":false,"IconValue":"","Value":223},{"Caption":"7 colors each in red run circularly, multi points from start to end","Color":-1,"IconActive":false,"IconValue":"","Value":224},{"Caption":"7 colors each in green run circularly, multi points from start to end","Color":-1,"IconActive":false,"IconValue":"","Value":225},{"Caption":"7 colors each in blue run circularly, multi points from start to end","Color":-1,"IconActive":false,"IconValue":"","Value":226},{"Caption":"7 colors each in yellow run circularly, multi points from start to end","Color":-1,"IconActive":false,"IconValue":"","Value":227},{"Caption":"7 colors each in purple run circularly, multi points from start to end","Color":-1,"IconActive":false,"IconValue":"","Value":228},{"Caption":"7 colors each in cyan run circularly, multi points from start to end","Color":-1,"IconActive":false,"IconValue":"","Value":229},{"Caption":"7 colors each in white run circularly, multi points from start to end","Color":-1,"IconActive":false,"IconValue":"","Value":230},{"Caption":"7 colors each in red run circularly, multi points from end to start","Color":-1,"IconActive":false,"IconValue":"","Value":231},{"Caption":"7 colors each in green run circularly, multi points from end to start","Color":-1,"IconActive":false,"IconValue":"","Value":232},{"Caption":"7 colors each in blue run circularly, multi points from end to start","Color":-1,"IconActive":false,"IconValue":"","Value":233},{"Caption":"7 colors each in yellow run circularly, multi points from end to start","Color":-1,"IconActive":false,"IconValue":"","Value":234},{"Caption":"7 colors each in purple run circularly, multi points from end to start","Color":-1,"IconActive":false,"IconValue":"","Value":235},{"Caption":"7 colors each in cyan run circularly, multi points from end to start","Color":-1,"IconActive":false,"IconValue":"","Value":236},{"Caption":"7 colors each in white run circularly, multi points from end to start","Color":-1,"IconActive":false,"IconValue":"","Value":237},{"Caption":"7 colors each in red run circularly, multi points from start to end and return back","Color":-1,"IconActive":false,"IconValue":"","Value":238},{"Caption":"7 colors each in green run circularly, multi points from start to end and return back","Color":-1,"IconActive":false,"IconValue":"","Value":239},{"Caption":"7 colors each in blue run circularly, multi points from start to end and return back","Color":-1,"IconActive":false,"IconValue":"","Value":240},{"Caption":"7 colors each in yellow run circularly, multi points from start to end and return back","Color":-1,"IconActive":false,"IconValue":"","Value":241},{"Caption":"7 colors each in purple run circularly, multi points from start to end and return back","Color":-1,"IconActive":false,"IconValue":"","Value":242},{"Caption":"7 colors each in cyan run circularly, multi points from start to end and return back","Color":-1,"IconActive":false,"IconValue":"","Value":243},{"Caption":"7 colors each in white run circularly, multi points from start to end and return back","Color":-1,"IconActive":false,"IconValue":"","Value":244},{"Caption":"7 colors run with black background from start to end","Color":-1,"IconActive":false,"IconValue":"","Value":266},{"Caption":"7 colors run with red background from start to end","Color":-1,"IconActive":false,"IconValue":"","Value":267},{"Caption":"7 colors run with green background from start to end","Color":-1,"IconActive":false,"IconValue":"","Value":268},{"Caption":"7 colors run with blue background from start to end","Color":-1,"IconActive":false,"IconValue":"","Value":269},{"Caption":"7 colors run with yellow background from start to end","Color":-1,"IconActive":false,"IconValue":"","Value":270},{"Caption":"7 colors run with purple background from start to end","Color":-1,"IconActive":false,"IconValue":"","Value":271},{"Caption":"7 colors run with cyan background from start to end","Color":-1,"IconActive":false,"IconValue":"","Value":272},{"Caption":"7 colors run with white background from start to end","Color":-1,"IconActive":false,"IconValue":"","Value":273},{"Caption":"7 colors run with black background from end to start","Color":-1,"IconActive":false,"IconValue":"","Value":274},{"Caption":"7 colors run with red background from end to start","Color":-1,"IconActive":false,"IconValue":"","Value":275},{"Caption":"7 colors run with green background from end to start","Color":-1,"IconActive":false,"IconValue":"","Value":276},{"Caption":"7 colors run with blue background from end to start","Color":-1,"IconActive":false,"IconValue":"","Value":277},{"Caption":"7 colors run with yellow background from end to start","Color":-1,"IconActive":false,"IconValue":"","Value":278},{"Caption":"7 colors run with purple background from end to start","Color":-1,"IconActive":false,"IconValue":"","Value":279},{"Caption":"7 colors run with cyan background from end to start","Color":-1,"IconActive":false,"IconValue":"","Value":280},{"Caption":"7 colors run with white background from end to start","Color":-1,"IconActive":false,"IconValue":"","Value":281},{"Caption":"7 colors run gradually + 7 colors change quickly","Color":-1,"IconActive":false,"IconValue":"","Value":291},{"Caption":"7 colors run gradually + 7 colors flash","Color":-1,"IconActive":false,"IconValue":"","Value":292},{"Caption":"7 colors change quickly + 7 colors flash","Color":-1,"IconActive":false,"IconValue":"","Value":295},{"Caption":"7 colors run gradually + 7 colors change quickly + 7 colors flash","Color":-1,"IconActive":false,"IconValue":"","Value":298},{"Caption":"7 colors run in olivary + 7 colors change quickly + 7 colors flash","Color":-1,"IconActive":false,"IconValue":"","Value":299},{"Caption":"7 colors run gradually + 7 colors run in olivary + 7 colors change quickly + 7 color flash","Color":-1,"IconActive":false,"IconValue":"","Value":300}]',
        'PRESENTATION' => VARIABLE_PRESENTATION_ENUMERATION,
    ];

    /**
     * @var array<string,mixed> Addressable Presentation (Enumeration)
     */
    private const MHC_PRESENTATION_ADDRESSABLE = [
        'ICON'         => 'wand-magic-sparkles',
        'OPTIONS'      => '[{"Caption":"Manually","Color":-1,"IconActive":false,"IconValue":"","Value":0},{"Caption":"RBM 1","Color":-1,"IconActive":false,"IconValue":"","Value":1},{"Caption":"RBM 2","Color":-1,"IconActive":false,"IconValue":"","Value":2},{"Caption":"RBM 3","Color":-1,"IconActive":false,"IconValue":"","Value":3},{"Caption":"RBM 4","Color":-1,"IconActive":false,"IconValue":"","Value":4},{"Caption":"RBM 5","Color":-1,"IconActive":false,"IconValue":"","Value":5},{"Caption":"RBM 6","Color":-1,"IconActive":false,"IconValue":"","Value":6},{"Caption":"RBM 7","Color":-1,"IconActive":false,"IconValue":"","Value":7},{"Caption":"RBM 8","Color":-1,"IconActive":false,"IconValue":"","Value":8},{"Caption":"RBM 9","Color":-1,"IconActive":false,"IconValue":"","Value":9},{"Caption":"RBM 10","Color":-1,"IconActive":false,"IconValue":"","Value":10},{"Caption":"RBM 11","Color":-1,"IconActive":false,"IconValue":"","Value":11},{"Caption":"RBM 12","Color":-1,"IconActive":false,"IconValue":"","Value":12},{"Caption":"RBM 13","Color":-1,"IconActive":false,"IconValue":"","Value":13},{"Caption":"RBM 14","Color":-1,"IconActive":false,"IconValue":"","Value":14},{"Caption":"RBM 15","Color":-1,"IconActive":false,"IconValue":"","Value":15},{"Caption":"RBM 16","Color":-1,"IconActive":false,"IconValue":"","Value":16},{"Caption":"RBM 17","Color":-1,"IconActive":false,"IconValue":"","Value":17},{"Caption":"RBM 18","Color":-1,"IconActive":false,"IconValue":"","Value":18},{"Caption":"RBM 19","Color":-1,"IconActive":false,"IconValue":"","Value":19},{"Caption":"RBM 20","Color":-1,"IconActive":false,"IconValue":"","Value":20},{"Caption":"RBM 21","Color":-1,"IconActive":false,"IconValue":"","Value":21},{"Caption":"RBM 22","Color":-1,"IconActive":false,"IconValue":"","Value":22},{"Caption":"RBM 23","Color":-1,"IconActive":false,"IconValue":"","Value":23},{"Caption":"RBM 24","Color":-1,"IconActive":false,"IconValue":"","Value":24},{"Caption":"RBM 25","Color":-1,"IconActive":false,"IconValue":"","Value":25},{"Caption":"RBM 26","Color":-1,"IconActive":false,"IconValue":"","Value":26},{"Caption":"RBM 27","Color":-1,"IconActive":false,"IconValue":"","Value":27},{"Caption":"RBM 28","Color":-1,"IconActive":false,"IconValue":"","Value":28},{"Caption":"RBM 29","Color":-1,"IconActive":false,"IconValue":"","Value":29},{"Caption":"RBM 30","Color":-1,"IconActive":false,"IconValue":"","Value":30},{"Caption":"RBM 31","Color":-1,"IconActive":false,"IconValue":"","Value":31},{"Caption":"RBM 32","Color":-1,"IconActive":false,"IconValue":"","Value":32},{"Caption":"RBM 33","Color":-1,"IconActive":false,"IconValue":"","Value":33},{"Caption":"RBM 34","Color":-1,"IconActive":false,"IconValue":"","Value":34},{"Caption":"RBM 35","Color":-1,"IconActive":false,"IconValue":"","Value":35},{"Caption":"RBM 36","Color":-1,"IconActive":false,"IconValue":"","Value":36},{"Caption":"RBM 37","Color":-1,"IconActive":false,"IconValue":"","Value":37},{"Caption":"RBM 38","Color":-1,"IconActive":false,"IconValue":"","Value":38},{"Caption":"RBM 39","Color":-1,"IconActive":false,"IconValue":"","Value":39},{"Caption":"RBM 40","Color":-1,"IconActive":false,"IconValue":"","Value":40},{"Caption":"RBM 41","Color":-1,"IconActive":false,"IconValue":"","Value":41},{"Caption":"RBM 42","Color":-1,"IconActive":false,"IconValue":"","Value":42},{"Caption":"RBM 43","Color":-1,"IconActive":false,"IconValue":"","Value":43},{"Caption":"RBM 44","Color":-1,"IconActive":false,"IconValue":"","Value":44},{"Caption":"RBM 45","Color":-1,"IconActive":false,"IconValue":"","Value":45},{"Caption":"RBM 46","Color":-1,"IconActive":false,"IconValue":"","Value":46},{"Caption":"RBM 47","Color":-1,"IconActive":false,"IconValue":"","Value":47},{"Caption":"RBM 48","Color":-1,"IconActive":false,"IconValue":"","Value":48},{"Caption":"RBM 49","Color":-1,"IconActive":false,"IconValue":"","Value":49},{"Caption":"RBM 50","Color":-1,"IconActive":false,"IconValue":"","Value":50},{"Caption":"RBM 51","Color":-1,"IconActive":false,"IconValue":"","Value":51},{"Caption":"RBM 52","Color":-1,"IconActive":false,"IconValue":"","Value":52},{"Caption":"RBM 53","Color":-1,"IconActive":false,"IconValue":"","Value":53},{"Caption":"RBM 54","Color":-1,"IconActive":false,"IconValue":"","Value":54},{"Caption":"RBM 55","Color":-1,"IconActive":false,"IconValue":"","Value":55},{"Caption":"RBM 56","Color":-1,"IconActive":false,"IconValue":"","Value":56},{"Caption":"RBM 57","Color":-1,"IconActive":false,"IconValue":"","Value":57},{"Caption":"RBM 58","Color":-1,"IconActive":false,"IconValue":"","Value":58},{"Caption":"RBM 59","Color":-1,"IconActive":false,"IconValue":"","Value":59},{"Caption":"RBM 60","Color":-1,"IconActive":false,"IconValue":"","Value":60},{"Caption":"RBM 61","Color":-1,"IconActive":false,"IconValue":"","Value":61},{"Caption":"RBM 62","Color":-1,"IconActive":false,"IconValue":"","Value":62},{"Caption":"RBM 63","Color":-1,"IconActive":false,"IconValue":"","Value":63},{"Caption":"RBM 64","Color":-1,"IconActive":false,"IconValue":"","Value":64},{"Caption":"RBM 65","Color":-1,"IconActive":false,"IconValue":"","Value":65},{"Caption":"RBM 66","Color":-1,"IconActive":false,"IconValue":"","Value":66},{"Caption":"RBM 67","Color":-1,"IconActive":false,"IconValue":"","Value":67},{"Caption":"RBM 68","Color":-1,"IconActive":false,"IconValue":"","Value":68},{"Caption":"RBM 69","Color":-1,"IconActive":false,"IconValue":"","Value":69},{"Caption":"RBM 70","Color":-1,"IconActive":false,"IconValue":"","Value":70},{"Caption":"RBM 71","Color":-1,"IconActive":false,"IconValue":"","Value":71},{"Caption":"RBM 72","Color":-1,"IconActive":false,"IconValue":"","Value":72},{"Caption":"RBM 73","Color":-1,"IconActive":false,"IconValue":"","Value":73},{"Caption":"RBM 74","Color":-1,"IconActive":false,"IconValue":"","Value":74},{"Caption":"RBM 75","Color":-1,"IconActive":false,"IconValue":"","Value":75},{"Caption":"RBM 76","Color":-1,"IconActive":false,"IconValue":"","Value":76},{"Caption":"RBM 77","Color":-1,"IconActive":false,"IconValue":"","Value":77},{"Caption":"RBM 78","Color":-1,"IconActive":false,"IconValue":"","Value":78},{"Caption":"RBM 79","Color":-1,"IconActive":false,"IconValue":"","Value":79},{"Caption":"RBM 80","Color":-1,"IconActive":false,"IconValue":"","Value":80},{"Caption":"RBM 81","Color":-1,"IconActive":false,"IconValue":"","Value":81},{"Caption":"RBM 82","Color":-1,"IconActive":false,"IconValue":"","Value":82},{"Caption":"RBM 83","Color":-1,"IconActive":false,"IconValue":"","Value":83},{"Caption":"RBM 84","Color":-1,"IconActive":false,"IconValue":"","Value":84},{"Caption":"RBM 85","Color":-1,"IconActive":false,"IconValue":"","Value":85},{"Caption":"RBM 86","Color":-1,"IconActive":false,"IconValue":"","Value":86},{"Caption":"RBM 87","Color":-1,"IconActive":false,"IconValue":"","Value":87},{"Caption":"RBM 88","Color":-1,"IconActive":false,"IconValue":"","Value":88},{"Caption":"RBM 89","Color":-1,"IconActive":false,"IconValue":"","Value":89},{"Caption":"RBM 90","Color":-1,"IconActive":false,"IconValue":"","Value":90},{"Caption":"RBM 91","Color":-1,"IconActive":false,"IconValue":"","Value":91},{"Caption":"RBM 92","Color":-1,"IconActive":false,"IconValue":"","Value":92},{"Caption":"RBM 93","Color":-1,"IconActive":false,"IconValue":"","Value":93},{"Caption":"RBM 94","Color":-1,"IconActive":false,"IconValue":"","Value":94},{"Caption":"RBM 95","Color":-1,"IconActive":false,"IconValue":"","Value":95},{"Caption":"RBM 96","Color":-1,"IconActive":false,"IconValue":"","Value":96},{"Caption":"RBM 97","Color":-1,"IconActive":false,"IconValue":"","Value":97},{"Caption":"RBM 98","Color":-1,"IconActive":false,"IconValue":"","Value":98},{"Caption":"RBM 99","Color":-1,"IconActive":false,"IconValue":"","Value":99},{"Caption":"RBM 100","Color":-1,"IconActive":false,"IconValue":"","Value":100},{"Caption":"RBM 101","Color":-1,"IconActive":false,"IconValue":"","Value":101},{"Caption":"RBM 102","Color":-1,"IconActive":false,"IconValue":"","Value":102},{"Caption":"Circulate all modes","Color":-1,"IconActive":false,"IconValue":"","Value":255}]',
        'PRESENTATION' => VARIABLE_PRESENTATION_ENUMERATION,
    ];

    // -------------------------------------------------------------------------
    // Methods
    // -------------------------------------------------------------------------

    /**
     * In contrast to Construct, this function is called only once when creating the instance and starting Symcon.
     * Therefore, status variables and module properties which the module requires permanently should be created here.
     *
     * @return void
     */
    public function Create(): void
    {
        // Never delete this line!
        parent::Create();

        // Device properties
        $this->RegisterPropertyInteger('TYPE', 51);
        $this->RegisterPropertyString('MODEL', 'Unknown');
        $this->RegisterPropertyString('TCPIP', '');
        $this->RegisterPropertyString('MAC', '');
        $this->RegisterPropertyString('RGB', '012');

        // Status variables
        $this->RegisterVariableBoolean('Power', $this->Translate('Power'), self::MHC_PRESENTATION_SWITCH, 0);
        $this->EnableAction('Power');
        $this->RegisterVariableInteger('Color', $this->Translate('Color'), self::MHC_PRESENTATION_COLOR, 1);
        $this->EnableAction('Color');
        $this->RegisterVariableInteger('Speed', $this->Translate('Speed'), self::MHC_PRESENTATION_SPEED, 2);
        $this->EnableAction('Speed');
        $this->RegisterVariableInteger('Brightness', $this->Translate('Brightness'), self::MHC_PRESENTATION_BRIGHTNESS, 3);
        $this->EnableAction('Brightness');
        $this->RegisterVariableInteger('Mode', $this->Translate('Mode'), $this->GetPatternProfile(0), 4);
        $this->EnableAction('Mode');

        // Set visualization type to 1, as we want to offer HTML
        $this->SetVisualizationType(1);
    }

    /**
     * This function is called when deleting the instance during operation and when updating via "Module Control".
     * The function is not called when exiting Symcon.
     *
     * @return void
     */
    public function Destroy(): void
    {
        // Never delete this line!
        parent::Destroy();
    }

    /**
     * The content can be overwritten in order to transfer a self-created configuration page.
     * This way, content can be generated dynamically.
     * In this case, the "form.json" on the file system is completely ignored.
     *
     * @return string Content of the configuration page.
     */
    public function GetConfigurationForm(): string
    {
        $form = json_decode(file_get_contents(__DIR__ . '/form.json'), true);
        // Extract version
        $ins = IPS_GetInstance($this->InstanceID);
        $mod = IPS_GetModule($ins['ModuleInfo']['ModuleID']);
        $lib = IPS_GetLibrary($mod['LibraryID']);
        $this->ModifyFormElement($form['actions'], 'Version', function (array &$element) use ($lib): void
        {
            $element['caption'] = sprintf('v%s.%d', $lib['Version'], $lib['Build']);
        });
        // Test functions with current values
        $options = json_decode($this->GetPatternProfile($this->ReadPropertyInteger('TYPE'))['OPTIONS'], true);
        $this->ModifyFormElement($form['actions'], 'TestMode', function (array &$element) use ($options): void
        {
            $element['options'] = array_map(fn (array $option): array => ['caption' => $option['Caption'], 'value' => $option['Value']], $options);
            $element['value'] = $this->GetValue('Mode');
        });
        foreach (['TestColor' => 'Color', 'TestBrightness' => 'Brightness', 'TestSpeed' => 'Speed'] as $name => $ident) {
            $this->ModifyFormElement($form['actions'], $name, function (array &$element) use ($ident): void
            {
                $element['value'] = $this->GetValue($ident);
            });
        }
        return json_encode($form);
    }

    /**
     * Is executed when "Apply" is pressed on the configuration page and immediately after the instance has been created.
     *
     * @return void
     */
    public function ApplyChanges(): void
    {
        // Never delete this line!
        parent::ApplyChanges();

        // Values
        $type = $this->ReadPropertyInteger('TYPE');
        $tcpip = $this->ReadPropertyString('TCPIP');
        $rgb = $this->ReadPropertyString('RGB');

        // Mode presentation depends on the controller type
        $this->RegisterVariableInteger('Mode', $this->Translate('Mode'), $this->GetPatternProfile($type), 4);

        // Debug message
        $this->LogDebug(__FUNCTION__, 'TYPE=0x' . dechex($type) . ', IP=' . $tcpip . ', RGB=' . $rgb);

        // Send a complete update message to the display, as parameters may have changed
        $this->UpdateVisualizationValue($this->GetFullUpdateMessage());

        // Configuration check
        if ($tcpip === '') {
            $this->SetStatus(IS_INACTIVE);
        } elseif (filter_var($tcpip, FILTER_VALIDATE_IP) === false) {
            $this->SetStatus(self::STATUS_WRONG_IP);
        } elseif (!isset(self::MAGIC_HOME_CONTROLLER[$type])) {
            $this->SetStatus(self::STATUS_WRONG_TYPE);
        } else {
            $this->SetStatus(IS_ACTIVE);
        }
    }

    /**
     * Is called when, for example, a button is clicked in the visualization.
     *
     * @param string $ident Ident of the variable
     * @param mixed $value The value to be set
     * @return void
     */
    public function RequestAction(string $ident, mixed $value): void
    {
        // Debug
        $this->LogDebug(__FUNCTION__, $ident . ' => ' . var_export($value, true));
        switch ($ident) {
            case 'Power':
                if ($this->SendPower((bool) $value)) {
                    $this->SetValueBoolean($ident, (bool) $value);
                }
                break;
            case 'Color':
            case 'Brightness':
            case 'Speed':
            case 'Mode':
                // The send functions read the new value from the variable, so restore it on failure
                $old = $this->GetValue($ident);
                $this->SetValueInteger($ident, (int) $value);
                if ($this->GetValue('Mode') == 0) {
                    // Speed only affects effects, nothing to send in manual mode
                    $sent = ($ident === 'Speed') || $this->SendColor();
                } else {
                    $sent = $this->SendFunction();
                }
                if (!$sent) {
                    $this->SetValueInteger($ident, $old);
                }
                $this->UpdateVariableStates();
                break;
            case 'Synchronize':
                $this->SendSync();
                break;
            default:
                throw new Exception('Invalid Ident');
        }
        // Mode list is static, so only the current state is sent
        $this->UpdateVisualizationValue($this->GetFullUpdateMessage(false));
    }

    /**
     * If the HTML-SDK is to be used, this function must be overwritten in order to return the HTML content.
     *
     * @return string Initial display of a representation via HTML SDK
     */
    public function GetVisualizationTile(): string
    {
        // Add a script to set the values when loading, analogous to changes at runtime
        // Although the return from GetFullUpdateMessage is already JSON-encoded, json_encode is still executed a second time
        // This adds quotation marks to the string and any quotation marks within it are escaped correctly
        $initialHandling = '<script>handleMessage(' . json_encode($this->GetFullUpdateMessage()) . ');</script>';
        // Add static HTML from file
        $module = file_get_contents(__DIR__ . '/module.html');
        // Important: $initialHandling at the end, as the handleMessage function is only defined in the HTML
        return $module . $initialHandling;
    }

    /**
     * This function will be available automatically after the module is imported with the module control.
     * Using the custom prefix this function will be callable from PHP and JSON-RPC through:.
     *
     * MHC_SetBrightness(int $InstanceID, int $Brightness);
     *
     * @param int $brightness The brightness value to set.
     *
     * @return void
     */
    public function SetBrightness(int $brightness): void
    {
        $this->RequestAction('Brightness', $brightness);
    }

    /**
     * This function will be available automatically after the module is imported with the module control.
     * Using the custom prefix this function will be callable from PHP and JSON-RPC through:.
     *
     * MHC_SetColor(int $InstanceID, int $Color);
     *
     * @param int $color The color value to set.
     *
     * @return void
     */
    public function SetColor(int $color): void
    {
        $this->RequestAction('Color', $color);
    }

    /**
     * This function will be available automatically after the module is imported with the module control.
     * Using the custom prefix this function will be callable from PHP and JSON-RPC through:.
     *
     * MHC_SetMode(int $InstanceID, int $Mode);
     *
     * @param int $mode The mode to set.
     *
     * @return void
     */
    public function SetMode(int $mode): void
    {
        $this->RequestAction('Mode', $mode);
    }

    /**
     * This function will be available automatically after the module is imported with the module control.
     * Using the custom prefix this function will be callable from PHP and JSON-RPC through:.
     *
     * MHC_SetPower(int $InstanceID, bool $Power);
     *
     * @param bool $power The power state to set.
     *
     * @return void
     */
    public function SetPower(bool $power): void
    {
        $this->RequestAction('Power', $power);
    }

    /**
     * This function will be available automatically after the module is imported with the module control.
     * Using the custom prefix this function will be callable from PHP and JSON-RPC through:.
     *
     * MHC_SetSpeed(int $InstanceID, int $Speed);
     *
     * @param int $speed The speed value to set.
     *
     * @return void
     */
    public function SetSpeed(int $speed): void
    {
        $this->RequestAction('Speed', $speed);
    }

    /**
     * Creates the protocol handler for the configured controller type.
     *
     * @return ProtocolBase|null Protocol handler or null for an unknown controller type.
     */
    private function GetProtocol(): ?ProtocolBase
    {
        $type = $this->ReadPropertyInteger('TYPE');
        if (!isset(self::MAGIC_HOME_CONTROLLER[$type])) {
            $this->LogDebug(__FUNCTION__, 'Unknown controller type: 0x' . dechex($type));
            return null;
        }
        $class = self::CLASS_PROTOCOL[self::MAGIC_HOME_CONTROLLER[$type][1]];
        return new $class();
    }

    /**
     * Checks if the configured controller supports dimmable effects.
     *
     * @return bool True if the brightness can be changed during an effect.
     */
    private function HasDimmableEffects(): bool
    {
        $type = $this->ReadPropertyInteger('TYPE');
        $protocol = self::MAGIC_HOME_CONTROLLER[$type][1] ?? -1;
        return in_array($protocol, self::BRIGHTNESS_EFFECTS_PROTOCOLS, true);
    }

    /**
     * Enables or disables the variables depending on the current mode.
     *
     * @return void
     */
    private function UpdateVariableStates(): void
    {
        $effect = $this->GetValue('Mode') > 0;
        $this->SetVariableDisabled('Speed', !$effect);
        $this->SetVariableDisabled('Color', $effect);
        $this->SetVariableDisabled('Brightness', $effect && !$this->HasDimmableEffects());
    }

    /**
     * Send power state data.
     *
     * @param bool $value The power state to send.
     *
     * @return bool True if the data could be sent.
     */
    private function SendPower(bool $value): bool
    {
        $protocol = $this->GetProtocol();
        if ($protocol === null) {
            return false;
        }
        return $this->SendData($protocol->ConstructStateChange($value)) !== null;
    }

    /**
     * Send function (effect) data.
     *
     * @return bool True if the data could be sent.
     */
    private function SendFunction(): bool
    {
        $pattern = $this->GetValue('Mode');
        if ($pattern == 0) {
            return true; // Nothing to do in manual mode
        }
        $protocol = $this->GetProtocol();
        if ($protocol === null) {
            return false;
        }
        $data = $protocol->ConstructPresetPattern($pattern, $this->GetValue('Speed'), $this->GetValue('Brightness'));
        return $this->SendData($data) !== null;
    }

    /**
     * Send color data.
     *
     * @return bool True if the data could be sent.
     */
    private function SendColor(): bool
    {
        if ($this->GetValue('Mode') != 0) {
            return true; // Nothing to do in effect mode
        }
        $protocol = $this->GetProtocol();
        if ($protocol === null) {
            return false;
        }
        $brightness = $this->GetValue('Brightness') / 100;
        $color = $this->GetValue('Color');
        $rgb = [($color >> 16) & 0xFF, ($color >> 8) & 0xFF, $color & 0xFF];
        // Each output pin gets the color channel defined by the pin assignment
        $channel = $this->ReadPropertyString('RGB');
        $levels = [];
        for ($pin = 0; $pin < 3; $pin++) {
            $levels[$pin] = (int) floor($rgb[(int) $channel[$pin]] * $brightness);
        }
        $this->LogDebug(__FUNCTION__, $levels);
        $data = $protocol->ConstructLevelsChange(true, $levels[0], $levels[1], $levels[2], 0x00, 0x00, 0x00);
        return $this->SendData($data) !== null;
    }

    /**
     * Sync controller state to variables.
     *
     * @return void
     */
    private function SendSync(): void
    {
        $protocol = $this->GetProtocol();
        if ($protocol === null) {
            return;
        }
        $type = $this->ReadPropertyInteger('TYPE');
        $data = $this->SendData($protocol->ConstructStateQuery(), $protocol->StateResponseLength());
        if ($data === null) {
            return;
        }
        $this->LogDebug(__FUNCTION__, bin2hex($data));
        if (strlen($data) != $protocol->StateResponseLength()) {
            $this->LogDebug(__FUNCTION__, 'No sync possible!');
            return;
        }

        // Convert to array (ONE based)
        $rx = unpack('C*', $data);
        $this->LogDebug(__FUNCTION__, $rx);
        // Check controller type
        if ($rx[2] != $type) {
            $this->LogMessage(sprintf('Wrong controller type: 0x%02X', $rx[2]), KL_ERROR);
        }

        // Check power state
        $this->SetValueBoolean('Power', ($rx[3] == 0x23));

        // Check mode
        $pattern = $rx[4];
        $this->LogDebug(__FUNCTION__, 'Pattern: ' . $pattern);
        if (in_array($type, self::MAGIC_HOME_SWITCHES, true)) {
            $this->LogDebug(__FUNCTION__, 'Sync for switches not full supported!');
            return;
        }
        if ($pattern == self::EFFECT_CUSTOM_CODE) {
            $this->LogDebug(__FUNCTION__, 'Controller in custom effect mode - not supported!');
            return;
        }
        if ($pattern == self::PRESET_MUSIC_MODE) {
            $this->LogDebug(__FUNCTION__, 'Controller in music mode - not supported!');
            return;
        }
        $mode = 0;
        if (in_array($pattern, [0x41, 0x61])) {
            // Manual color mode
        } elseif (($pattern >= 0x25) && ($pattern <= 0x38)) {
            if (in_array($protocol->Id(), self::ORIGINAL_EFFECTS_PROTOCOLS, true)) {
                $mode = ($pattern << 8) + $rx[5] - 99;
            } elseif (in_array($protocol->Id(), self::ADDRESSABLE_EFFECTS_PROTOCOLS, true)) {
                $mode = $rx[5];
            } else {
                $mode = $pattern;
            }
        } elseif (in_array($protocol->Id(), self::ADDRESSABLE_PROTOCOLS, true)) {
            $mode = ($rx[5] == 0x61) ? 0 : $rx[5] - 99;
        }
        $this->LogDebug(__FUNCTION__, 'Mode = ' . $mode);
        $this->SetValueInteger('Mode', $mode);
        $this->UpdateVariableStates();

        // Check speed
        $speed = in_array($protocol->Id(), self::ADDRESSABLE_PROTOCOLS, true) ? $rx[6] : $protocol::DelayToSpeed($rx[6]);
        $this->LogDebug(__FUNCTION__, 'Speed = ' . $speed);
        $this->SetValueInteger('Speed', $speed);

        // Check brightness & color
        $max = max($rx[7], $rx[8], $rx[9]);
        if ($mode == 0) {
            // Brightness is the strongest channel, the color is scaled back to full brightness
            $brightness = min(100, (int) round($max / 255 * 100));
            $color = 0;
            if ($max > 0) {
                // Each color channel was sent on the pin which is assigned to it
                $channel = $this->ReadPropertyString('RGB');
                foreach ([16, 8, 0] as $index => $shift) {
                    $level = $rx[7 + (int) strpos($channel, (string) $index)];
                    $color |= min(255, (int) round($level * 255 / $max)) << $shift;
                }
            }
            $this->LogDebug(__FUNCTION__, sprintf('Brightness = %d, Color = 0x%06X', $brightness, $color));
            $this->SetValueInteger('Brightness', $brightness);
            $this->SetValueInteger('Color', $color);
        } elseif ($this->HasDimmableEffects()) {
            // The red byte holds the brightness during an effect
            $brightness = min(100, $rx[7]);
            $this->LogDebug(__FUNCTION__, 'Brightness = ' . $brightness);
            $this->SetValueInteger('Brightness', $brightness);
        }
    }

    /**
     * Send data array to controller.
     *
     * @param array<int> $values Configuration Data
     * @param int        $read   Number of bytes to read as response
     *
     * @return string|null Received data or null if the controller is not reachable
     */
    private function SendData(array $values, int $read = 0): ?string
    {
        // Only send with a valid configuration
        $status = $this->GetStatus();
        if (($status !== IS_ACTIVE) && ($status !== self::STATUS_UNREACHABLE)) {
            $this->LogDebug(__FUNCTION__, 'Invalid configuration, status: ' . $status);
            return null;
        }
        $path = 'tcp://' . $this->ReadPropertyString('TCPIP');
        $socket = @fsockopen($path, self::SOCKET_PORT, $errno, $errstr, self::SOCKET_TIME);
        if ($socket === false) {
            $this->LogDebug(__FUNCTION__, $path . " -> $errstr ($errno)");
            $this->SetStatus(self::STATUS_UNREACHABLE);
            return null;
        }
        $this->LogDebug(__FUNCTION__, 'Connection established: ' . $path);
        if ($status === self::STATUS_UNREACHABLE) {
            $this->SetStatus(IS_ACTIVE);
        }

        $send = '';
        foreach ($values as $value) {
            $send .= chr((int) $value);
        }
        fwrite($socket, $send);
        $this->LogDebug(__FUNCTION__, 'Send=' . bin2hex($send));

        // Read until the expected response length or timeout
        $data = '';
        if ($read > 0) {
            stream_set_timeout($socket, self::SOCKET_TIME);
            while (strlen($data) < $read) {
                $recv = fread($socket, $read - strlen($data));
                if (($recv === false) || ($recv === '')) {
                    break;
                }
                $data .= $recv;
            }
            $this->LogDebug(__FUNCTION__, 'Read=' . bin2hex($data));
        }

        fclose($socket);
        return $data;
    }

    /**
     * Generate a message that updates all elements in the HTML display.
     *
     * @param bool $modes Include the list of selectable modes
     *
     * @return string JSON encoded message information
     */
    private function GetFullUpdateMessage(bool $modes = true): string
    {
        $result = [
            'power'      => $this->GetValue('Power'),
            'color'      => sprintf('#%06x', $this->GetValue('Color') & 0xFFFFFF),
            'brightness' => $this->GetValue('Brightness'),
            'speed'      => $this->GetValue('Speed'),
            'mode'       => $this->GetValue('Mode'),
            'dimmable'   => $this->HasDimmableEffects(),
            'reachable'  => $this->GetStatus() !== self::STATUS_UNREACHABLE,
        ];

        if ($modes) {
            $options = json_decode($this->GetPatternProfile($this->ReadPropertyInteger('TYPE'))['OPTIONS'], true);
            $result['modes'] = array_map(fn (array $option): array => ['value' => $option['Value'], 'caption' => $option['Caption']], $options);
        }

        $json = json_encode($result, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $this->LogDebug(__FUNCTION__, $json);

        return $json;
    }

    /**
     * Extract translated mode presentation for a controller type.
     *
     * @param int $value Controller type
     *
     * @return array<string,mixed> Mode Presentation.
     */
    private function GetPatternProfile(int $value): array
    {
        $presentation = self::MHC_PRESENTATION_PRESET;
        foreach (self::MHC_PRESENTATION_PATTERN as $profile) {
            if (in_array($value, $profile['protocols'], true)) {
                $presentation = $profile['presentation'];
                break;
            }
        }

        return $this->TranslatePresentation($presentation, 'OPTIONS', 'Caption');
    }
}
