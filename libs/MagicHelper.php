<?php

/**
 * MagicHelper.php
 *
 * PHP Wrapper for Magic Home Controllers.
 *
 * @package       traits
 * @author        Heiko Wilknitz <heiko@wilkware.de>
 * @copyright     2022 Heiko Wilknitz
 * @link          https://wilkware.de
 * @license       https://creativecommons.org/licenses/by-nc-sa/4.0/ CC BY-NC-SA 4.0
 *
 */

declare(strict_types=1);

# Temperature
const MIN_TEMP = 2700;
const MAX_TEMP = 6500;

# Transitions
const TRANSITION_JUMP = 'jump';
const TRANSITION_STROBE = 'strobe';
const TRANSITION_GRADUAL = 'gradual';

const TRANSITION_BYTES = [
    TRANSITION_JUMP    => 0x3B,
    TRANSITION_STROBE  => 0x3C,
    TRANSITION_GRADUAL => 0x3A,
];

#Response length
const LEDENET_POWER_RESPONSE_LEN = 4;
const LEDENET_STATE_RESPONSE_LEN = 14;
const LEDENET_STATE_ORIGINAL_RESPONSE_LEN = 11;
const LEDENET_STATE_ADDRESSABLE_RESPONSE_LEN = 25;

# Message state
const MSG_ORIGINAL_POWER_STATE = 'original_power_state';
const MSG_ORIGINAL_STATE = 'original_state';
const MSG_POWER_STATE = 'power_state';
const MSG_STATE = 'state';
const MSG_ADDRESSABLE_STATE = 'addressable_state';

const MSG_FIRST_BYTE = [
    0xF0 => MSG_POWER_STATE,
    0x00 => MSG_POWER_STATE,
    0x0F => MSG_POWER_STATE,
    0x78 => MSG_ORIGINAL_POWER_STATE,
    0x66 => MSG_ORIGINAL_STATE,
    0x81 => MSG_STATE,
    0xB0 => MSG_ADDRESSABLE_STATE,
];

const MSG_LENGTHS = [
    MSG_POWER_STATE          => LEDENET_POWER_RESPONSE_LEN,
    MSG_ORIGINAL_POWER_STATE => LEDENET_POWER_RESPONSE_LEN,
    MSG_ORIGINAL_STATE       => LEDENET_STATE_ORIGINAL_RESPONSE_LEN,
    MSG_STATE                => LEDENET_STATE_RESPONSE_LEN,
    MSG_ADDRESSABLE_STATE    => LEDENET_STATE_ADDRESSABLE_RESPONSE_LEN,
];

const EFFECT_CUSTOM_CODE = 0x60;
const PRESET_MUSIC_MODE = 0x62;

# Protocol id's
const PROTOCOL_LEDENET_ORIGINAL = 0;
const PROTOCOL_LEDENET_9BYTE = 1;
const PROTOCOL_LEDENET_9BYTE_DIMMABLE_EFFECTS = 2;
const PROTOCOL_LEDENET_8BYTE = 3;
const PROTOCOL_LEDENET_8BYTE_DIMMABLE_EFFECTS = 4;
const PROTOCOL_LEDENET_ADDRESSABLE_A1 = 5;
const PROTOCOL_LEDENET_ADDRESSABLE_A2 = 6;
const PROTOCOL_LEDENET_ADDRESSABLE_A3 = 7;
const PROTOCOL_LEDENET_CCT = 8;

# Class names
const CLASS_PROTOCOL = [
    PROTOCOL_LEDENET_ORIGINAL               => 'ProtocolLEDENETOriginal',
    PROTOCOL_LEDENET_9BYTE                  => 'ProtocolLEDENET9Byte',
    PROTOCOL_LEDENET_9BYTE_DIMMABLE_EFFECTS => 'ProtocolLEDENET8ByteDimmableEffects',
    PROTOCOL_LEDENET_8BYTE                  => 'ProtocolLEDENET8Byte',
    PROTOCOL_LEDENET_8BYTE_DIMMABLE_EFFECTS => 'ProtocolLEDENET9ByteDimmableEffects',
    PROTOCOL_LEDENET_ADDRESSABLE_A1         => 'ProtocolLEDENETAddressableA1',
    PROTOCOL_LEDENET_ADDRESSABLE_A2         => 'ProtocolLEDENETAddressableA2',
    PROTOCOL_LEDENET_ADDRESSABLE_A3         => 'ProtocolLEDENETAddressableA3',
    PROTOCOL_LEDENET_CCT                    => 'ProtocolLEDENETCCT',
];

# Adressable Protocolls
const ADDRESSABLE_PROTOCOLS = [
    PROTOCOL_LEDENET_ADDRESSABLE_A1,
    PROTOCOL_LEDENET_ADDRESSABLE_A2,
    PROTOCOL_LEDENET_ADDRESSABLE_A3,
];

const ORIGINAL_EFFECTS_PROTOCOLS = [
    PROTOCOL_LEDENET_ADDRESSABLE_A1,
];

const ADDRESSABLE_EFFECTS_PROTOCOLS = [
    PROTOCOL_LEDENET_ADDRESSABLE_A2,
    PROTOCOL_LEDENET_ADDRESSABLE_A3,
];

const BRIGHTNESS_EFFECTS_PROTOCOLS = [
    PROTOCOL_LEDENET_9BYTE_DIMMABLE_EFFECTS,
    PROTOCOL_LEDENET_8BYTE_DIMMABLE_EFFECTS,
    PROTOCOL_LEDENET_ADDRESSABLE_A2,
    PROTOCOL_LEDENET_ADDRESSABLE_A3,
];

# Controller models (model => <name>, <protocol>, <write-white>)
const MAGIC_HOME_CONTROLLER = [
    0x01 => ['Original LEDENET', PROTOCOL_LEDENET_ORIGINAL, false],
    0x04 => ['UFO LED WiFi Controller', PROTOCOL_LEDENET_8BYTE, true],
    0x06 => ['RGBW Controller', PROTOCOL_LEDENET_8BYTE_DIMMABLE_EFFECTS, false],
    0x07 => ['RGBCW Controller', PROTOCOL_LEDENET_9BYTE_DIMMABLE_EFFECTS, false],
    0x08 => ['RGB Controller with MIC', PROTOCOL_LEDENET_8BYTE_DIMMABLE_EFFECTS, true],
    0x09 => ['CCT Ceiling Light', PROTOCOL_LEDENET_8BYTE, false],
    0x0B => ['Smart Switch 1c', PROTOCOL_LEDENET_8BYTE, false],
    0x0E => ['Floor Lamp', PROTOCOL_LEDENET_9BYTE, false],
    0x10 => ['Christmas Light', PROTOCOL_LEDENET_8BYTE, false],
    0x16 => ['Magnetic Light CCT', PROTOCOL_LEDENET_8BYTE, false],
    0x17 => ['Magnetic Light Dimable', PROTOCOL_LEDENET_8BYTE, false],
    0x18 => ['Plant Light', PROTOCOL_LEDENET_8BYTE, false],
    0x19 => ['Smart Socket 2 USB', PROTOCOL_LEDENET_8BYTE, false],
    0x1A => ['Christmas Light', PROTOCOL_LEDENET_8BYTE, false],
    0x1B => ['Spray Light', PROTOCOL_LEDENET_8BYTE, false],
    0x1C => ['Table Light CCT', PROTOCOL_LEDENET_CCT, false],
    0x21 => ['Smart Bulb Dimmable', PROTOCOL_LEDENET_8BYTE, true],
    0x25 => ['RGB/WW/CW Controller', PROTOCOL_LEDENET_9BYTE, false],
    0x33 => ['RGB Controller', PROTOCOL_LEDENET_8BYTE, true],
    0x35 => ['Smart Bulb RGBCW', PROTOCOL_LEDENET_9BYTE, false],
    0x41 => ['Single Channel Controller', PROTOCOL_LEDENET_8BYTE, false],
    0x44 => ['Smart Bulb RGBW', PROTOCOL_LEDENET_8BYTE, false],
    0x45 => ['Unknown', PROTOCOL_LEDENET_8BYTE, false],
    0x52 => ['Smart Bulb CCT', PROTOCOL_LEDENET_8BYTE, false],
    0x54 => ['Downlight RGBW', PROTOCOL_LEDENET_8BYTE, false],
    0x62 => ['CCT Controller', PROTOCOL_LEDENET_8BYTE, false],
    0x81 => ['Unknown', PROTOCOL_LEDENET_8BYTE, true],
    0x93 => ['Smart Switch 1C', PROTOCOL_LEDENET_8BYTE, false],
    0x94 => ['Smart Switch 1c Watt', PROTOCOL_LEDENET_8BYTE, false],
    0x95 => ['Smart Switch 2c', PROTOCOL_LEDENET_8BYTE, false],
    0x96 => ['Smart Switch 4c', PROTOCOL_LEDENET_8BYTE, false],
    0x97 => ['Smart Socket 1c', PROTOCOL_LEDENET_8BYTE, false],
    0xA1 => ['RGB Symphony v1', PROTOCOL_LEDENET_ADDRESSABLE_A1, false],
    0xA2 => ['RGB Symphony v2', PROTOCOL_LEDENET_ADDRESSABLE_A2, false],
    0xA3 => ['RGB Symphony v3', PROTOCOL_LEDENET_ADDRESSABLE_A3, false],
    0xD1 => ['Digital Light', PROTOCOL_LEDENET_8BYTE, false],
    0xE1 => ['Ceiling Light', PROTOCOL_LEDENET_8BYTE, false],
    0xE2 => ['Ceiling Light Assist', PROTOCOL_LEDENET_8BYTE, false],
];

# Non light device models
const MAGIC_HOME_SWITCHES = [0x19, 0x93, 0x0B, 0x93, 0x94, 0x95, 0x96, 0x97];

# Supported Present Pattern by model
const MAGIC_HOME_PATTERN = [
    'MHC.Preset'        => [0x01, 0x04, 0x06, 0x07, 0x08, 0x09, 0x0B, 0x0E, 0x10, 0x16, 0x17, 0x18, 0x19, 0x1A, 0x1B, 0x1C, 0x21, 0x25, 0x33, 0x35, 0x41, 0x44, 0x45, 0x52, 0x54, 0x62, 0x81, 0x93, 0x94, 0x95, 0x96, 0x97, 0xD1, 0xE1, 0xE2],
    'MHC.Original'      => [0xA1],
    'MHC.Addressable'   => [0xA2, 0xA3],
];

// The base protocol.
abstract class ProtocolBase
{
    // Power On
    protected $powerOn = 0x23;
    // Power Off
    protected $powerOff = 0x24;
    // Counter
    protected $counter = 0;

    // Constructor
    public function __construct()
    {
        $this->counter = 0;
    }

    // Protocol supports dimmable effects.
    public function DimmableEffects()
    {
        return false;
    }

    // The length of the query response.
    public function StateResponseLength()
    {
        return LEDENET_POWER_RESPONSE_LEN;
    }

    // The bytes to send for a preset pattern.
    public function ConstructPresetPattern($pattern, $speed, $brightness)
    {
        $delay = self::SpeedToDelay($speed);
        $msg = [0x61, $pattern, $delay, 0x0F];
        return $this->ConstructMessage($msg);
    }

    // The bytes to send for a custom effect.
    public function ConstructCustomEffect($rgblist, $speed, $transtype)
    {
        $msg = [];
        $lead_byte = 0x00;
        $first_color = true;
        foreach ($rgb as $rgblist) {
            if ($irst_color) {
                $lead_byte = 0x51;
                $first_color = false;
            } else {
                $lead_byte = 0;
            }
            list($r, $g, $b) = $rgb;

            $msg[] = $lead_byte;
            $msg[] = $r;
            $msg[] = $g;
            $msg[] = $b;
        }
        // pad out empty slots
        if (count($rgblist) != 16) {
            $len = 16 - count($rgblist);
            for ($i = 0; $i < $len; $i++) {
                $msg[] = 0;
                $msg[] = 1;
                $msg[] = 2;
                $msg[] = 3;
            }
        }
        $msg[] = 0x00;
        $msg[] = self::SpeedToDelay($speed);
        $msg[] = isset(TRANSITION_BYTES[$transtype]) ? TRANSITION_BYTES[$transtype] : TRANSITION_BYTES[TRANSITION_GRADUAL]; # default to "gradual"
        $msg[] = 0xFF;
        $msg[] = 0x0F;
        return $this->ConstructMessage($msg);
    }

    // The name of the protocol.
    public function Name()
    {
        return CLASS_PROTOCOL[$this->Id()];
    }

    // The ID of the protocol.
    abstract public function Id();

    // The bytes to send for a query request.
    abstract public function ConstructStateQuery();

    // The bytes to send for a state change request.
    abstract public function ConstructStateChange($turn);

    // The bytes to send for a level change request.
    abstract public function ConstructLevelsChange($persist, $red, $green, $blue, $warmwhite, $coolwhite, $colormask);

    // Check if a state response is valid.
    abstract public function IsValidStateResponse($raw);

    // Returns delay
    public static function SpeedToDelay($speed)
    {
        # speed is 0-100, delay is 1-31
        $speed = max(0, min(100, $speed));
        $inv_speed = 100 - $speed;
        $delay = intval(($inv_speed * (0x1F - 1)) / 100);
        # translate from 0-30 to 1-31
        $delay = $delay + 1;
        return $delay;
    }

    // Returns spped
    public static function DelayToSpeed($delay)
    {
        # speed is 0-100, delay is 1-31
        # 1st translate delay to 0-30
        $delay = $delay - 1;
        $delay = max(0, min(0x1F - 1, $delay));
        $inv_speed = intval(($delay * 100) / (0x1F - 1));
        $speed = 100 - $inv_speed;
        return $speed;
    }

    // Returns Scaled Color Temparture
    protected function WhiteLevelsToScaledColorTemp($warmwhite, $coolwhite)
    {
        if (($warmwhite <= 0) || ($warmwhite >= 255)) {
            throw new Exception('Warm White of {warm_white} is not valid and must be between 0 and 255');
        }
        if (($coolwhite <= 0) || ($coolwhite >= 255)) {
            throw new Exception('Cool White of {cool_white} is not valid and must be between 0 and 255');
        }
        $warm = $warmwhite / 255;
        $cold = $coolwhite / 255;
        $brightness = $warm + $cold;
        if ($brightness == 0) {
            $temperature = 0;
        } else {
            $temperature = ($cold / $brightness) * 100;
        }
        return [round($temperature), min(100, round($brightness * 100))];
    }

    // Increment the counter byte.
    protected function IncrementCounter()
    {
        $this->counter += 1;
        return $this->counter;
    }

    // Check if a message is the start of an addressable state response.
    protected function IsStartOfAddressableResponse($data)
    {
        return false;
    }

    // Check if a message is a valid addressable state response.
    protected function IsValidAddressableResponse($data)
    {
        return false;
    }

    // Return the number of bytes expected in the response.
    // If the response is unknown, we assume the response is
    // a complete message since we have no way of knowing otherwise.
    protected function ExpectedResponseLength($data)
    {
        return MSG_FIRST_BYTE[$data[0]];
    }

    // Check a checksum of a message.
    protected function IsChecksumCorrect($msg)
    {
        $expected = array_sum(array_slice($msg, 0, -1)) & 0xFF;
        if ($expected != $msg[count($msg) - 1]) {
            return false;
        }
        return true;
    }

    // Original protocol uses no checksum.
    abstract protected function ConstructMessage($raw);

    // Check if a message is the start of a state response.
    abstract protected function IsStartOfStateResponse($data);

    // Check if a power state response is valid.
    abstract protected function IsValidPowerStateResponse($msg);

    // Check if a message is the start of a power response.
    abstract protected function IsStartOfPowerStateResponse($data);
}

// The original LEDENET protocol with no checksums.
class ProtocolLEDENETOriginal extends ProtocolBase
{
    // The id of the protocol.
    public function Id()
    {
        return PROTOCOL_LEDENET_ORIGINAL;
    }

    // The length of the query response.
    public function StateResponseLength()
    {
        return LEDENET_STATE_ORIGINAL_RESPONSE_LEN;
    }

    // The bytes to send for a query request.
    public function ConstructStateQuery()
    {
        $msg = [0xEF, 0x01, 0x77];
        return $this->ConstructMessage($msg);
    }

    // The bytes to send for a state change request.
    public function ConstructStateChange($turn)
    {
        $msg = [0xCC, $turn ? $this->powerOn : $this->powerOff, 0x33];
        return $this->ConstructMessage($msg);
    }

    // The bytes to send for a level change request.
    # sample message for original LEDENET protocol (w/o checksum at end)
    #  0  1  2  3  4
    # 56 90 fa 77 aa
    #  |  |  |  |  |
    #  |  |  |  |  terminator
    #  |  |  |  blue
    #  |  |  green
    #  |  red
    #  head
    public function ConstructLevelsChange($persist, $red, $green, $blue, $warmwhite, $coolwhite, $colormask)
    {
        $msg = [0x56, $red, $green, $blue, 0xAA];
        return $this->ConstructMessage($msg);
    }

    // Check if a state response is valid.
    public function IsValidStateResponse($raw)
    {
        return (count($raw) == $this->StateResponseLength()) && ($raw[0] == 0x66) && ($raw[1] == 0x01);
    }

    // Check if a power state response is valid.
    protected function IsValidPowerStateResponse($msg)
    {
        return (count(msg) == $this->StateResponseLength()) && ($msg[0] == 0x78);
    }

    // Check if a message is the start of a state response.
    protected function IsStartOfStateResponse($data)
    {
        return $data[0] == 0x66;
    }

    // Check if a message is the start of a state response.
    protected function IsStartOfPowerStateResponse($data)
    {
        return $data[0] == 0x78;
    }

    // Original protocol uses no checksum.
    protected function ConstructMessage($raw)
    {
        return $raw;
    }
}

// The newer LEDENET protocol with checksums that uses 8 bytes to set state.
class ProtocolLEDENET8Byte extends ProtocolBase
{
    private $ADDRESSABLE_HEADER = [0xB0, 0xB1, 0xB2, 0xB3, 0x00, 0x01, 0x01];
    private $addressableResponseLength = LEDENET_STATE_ADDRESSABLE_RESPONSE_LEN;

    // The id of the protocol.
    public function Id()
    {
        return PROTOCOL_LEDENET_8BYTE;
    }

    // The length of the query response.
    public function StateResponseLength()
    {
        return LEDENET_STATE_RESPONSE_LEN;
    }

    // The bytes to send for a query request.
    public function ConstructStateQuery()
    {
        $msg = [0x81, 0x8A, 0x8B];
        return $this->ConstructMessage($msg);
    }

    // The bytes to send for a state change request.
    # Alternate messages
    # Off 3b 24 00 00 00 00 00 00 00 32 00 00 91
    # On  3b 23 00 00 00 00 00 00 00 32 00 00 90
    public function ConstructStateChange($turn)
    {
        $msg = [0x71, $turn ? $this->powerOn : $this->powerOff, 0x0F];
        return $this->ConstructMessage($msg);
    }

    // The bytes to send for a level change request.
    # sample message for 8-byte protocols (w/ checksum at end)
    #  0  1  2  3  4  5  6
    # 31 90 fa 77 00 00 0f
    #  |  |  |  |  |  |  |
    #  |  |  |  |  |  |  terminator
    #  |  |  |  |  |  write mask / white2 (see below)
    #  |  |  |  |  white
    #  |  |  |  blue
    #  |  |  green
    #  |  red
    #  persistence (31 for true / 41 for false)
    #
    # byte 5 can have different values depending on the type
    # of device:
    # For devices that support 2 types of white value (warm and cold
    # white) this value is the cold white value. These use the LEDENET
    # protocol. If a second value is not given, reuse the first white value.
    #
    # For devices that cannot set both rbg and white values at the same time
    # (including devices that only support white) this value
    # specifies if this command is to set white value (0f) or the rgb
    # value (f0).
    #
    # For all other rgb and rgbw devices, the value is 00
    public function ConstructLevelsChange($persist, $red, $green, $blue, $warmwhite, $coolwhite, $writemode)
    {
        $msg = [$persist ? 0x31 : 0x41, $red, $green, $blue, $warmwhite, $writemode, 0x0F];
        return $this->ConstructMessage($msg);
    }

    // Check if a state response is valid.
    public function IsValidStateResponse($raw)
    {
        if (count($raw) != $this->StateResponseLength()) {
            return false;
        }
        if (!$tist->IsStartOfStateResponse($raw)) {
            return false;
        }
        return $this->IsChecksumCorrect($raw);
    }

    // Check if a power state response is valid.
    protected function IsValidPowerStateResponse($msg)
    {
        if ((count($msg) != $this->powerStateResponseLength) || !($this->IsStartOfPowerStateResponse($msg)) || ($msg[1] != 0x71) || (($msg[2] != $this->powerOn) && ($msg[2] != $this->powerOff))) {
            return false;
        }
        return $this->IsChecksumCorrect($msg);
    }

    // Check if a message is the start of a state response.
    protected function IsStartOfPowerStateResponse($data)
    {
        return (count($data) >= 1) && (MSG_FIRST_BYTE[$data[0]] == MSG_POWER_STATE);
    }

    // Check if a message is the start of a state response.
    protected function IsStartOfStateResponse($data)
    {
        return $data[0] == 0x81;
    }

    // Calculate checksum of byte array and add to end.
    protected function ConstructMessage($raw)
    {
        $checksum = array_sum($raw) & 0xFF;
        $raw[] = $checksum;
        return $raw;
    }

    // Check if a message is the start of an addressable state response.
    protected function IsStartOfAddressableResponse($data)
    {
        $i = 0;
        for ($i = 0; $i < count($this->ADDRESSABLE_HEADER); $i++) {
            if ($data[$i] != $this->ADDRESSABLE_HEADER($i)) {
                return false;
            }
        }
        return true;
    }

    // Check if a message is a valid addressable state response.
    protected function IsValidAddressableResponse($data)
    {
        if (count($data) != $this->addressableResponseLength) {
            return false;
        }
        if (!$this->IsStartOfAddressableResponse($data)) {
            return false;
        }
        return $this->IsChecksumCorrect($data);
    }
}

// The LEDENET protocol with checksums that uses 9 bytes to set state.
class ProtocolLEDENET8ByteDimmableEffects extends ProtocolLEDENET8Byte
{
    // The id of the protocol.
    public function Id()
    {
        return PROTOCOL_LEDENET_8BYTE_DIMMABLE_EFFECTS;
    }

    // Protocol supports dimmable effects.
    public function DimmableEffects()
    {
        return true;
    }

    // The bytes to send for a preset pattern.
    public function ConstructPresetPattern($pattern, $speed, $brightness)
    {
        $delay = self::SpeedToDelay($speed);
        $msg = [0x38, $pattern, $delay, $brightness];
        return $this->ConstructMessage($msg);
    }
}

// The newer LEDENET protocol with checksums that uses 9 bytes to set state.
class ProtocolLEDENET9Byte extends ProtocolLEDENET8Byte
{
    // The id of the protocol.
    public function Id()
    {
        return PROTOCOL_LEDENET_9BYTE;
    }

    // The bytes to send for a level change request.
    # sample message for 9-byte LEDENET protocol (w/ checksum at end)
    #  0  1  2  3  4  5  6  7
    # 31 bc c1 ff 00 00 f0 0f
    #  |  |  |  |  |  |  |  |
    #  |  |  |  |  |  |  |  terminator
    #  |  |  |  |  |  |  write mode (f0 colors, 0f whites, 00 colors & whites)
    #  |  |  |  |  |  cold white
    #  |  |  |  |  warm white
    #  |  |  |  blue
    #  |  |  green
    #  |  red
    #  persistence (31 for true / 41 for false)
    #
    public function ConstructLevelsChange($persist, $red, $green, $blue, $warmwhite, $coolwhite, $writemode)
    {
        $msg = [$persist ? 0x31 : 0x41, $red, $green, $blue, $warmwhite, $coolwhite, $writemode, 0x0F];
        return $this->ConstructMessage($msg);
    }
}

// The newer LEDENET protocol with checksums that uses 9 bytes to set state.
class ProtocolLEDENET9ByteDimmableEffects extends ProtocolLEDENET9Byte
{
    // The id of the protocol.
    public function Id()
    {
        return PROTOCOL_LEDENET_9BYTE_DIMMABLE_EFFECTS;
    }

    // Protocol supports dimmable effects.
    public function DimmableEffects()
    {
        return true;
    }

    // The bytes to send for a preset pattern.
    public function ConstructPresetPattern($pattern, $speed, $brightness)
    {
        $delay = self::SpeedToDelay($speed);
        $msg = [0x38, $pattern, $delay, $brightness];
        return $this->ConstructMessage($msg);
    }
}

// The newer LEDENET addressable protocol A1
class ProtocolLEDENETAddressableA1 extends ProtocolLEDENET9Byte
{
    // The id of the protocol.
    public function Id()
    {
        return PROTOCOL_LEDENET_ADDRESSABLE_A1;
    }

    // Protocol supports dimmable effects.
    public function DimmableEffects()
    {
        return false;
    }

    // The bytes to send for a preset pattern.
    public function ConstructPresetPattern($pattern, $speed, $brightness)
    {
        $effect = $pattern + 99;
        $msg = [0x61, $effect >> 8, $effect & 0xFF, $speed, 0x0F];
        return $this->ConstructMessage($msg);
    }
}

// The newer LEDENET addressable protocol A2
class ProtocolLEDENETAddressableA2 extends ProtocolLEDENET9Byte
{
    // The id of the protocol.
    public function Id()
    {
        return PROTOCOL_LEDENET_ADDRESSABLE_A2;
    }

    // Protocol supports dimmable effects.
    public function DimmableEffects()
    {
        return true;
    }

    // The bytes to send for a level change request.
    # white  41 01 ff ff ff 00 00 00 60 ff 00 00 9e
    public function ConstructLevelsChange($persist, $red, $green, $blue, $warmwhite, $coolwhite, $writemode)
    {
        $preset_number = 0x01;  # aka fixed color
        $msg = [0x41, $preset_number, $red, $green, $blue, 0x00, 0x00, 0x00, 0x60, 0xFF, 0x00, 0x00];
        return $this->ConstructMessage($msg);
    }

    // The bytes to send for a preset pattern.
    public function ConstructPresetPattern($pattern, $speed, $brightness)
    {
        $msg = [0x42, $pattern, $speed, $brightness];
        return $this->ConstructMessage($msg);
    }
}

// The newer LEDENET addressable protocol A3
class ProtocolLEDENETAddressableA3 extends ProtocolLEDENET9Byte
{
    // The id of the protocol.
    public function Id()
    {
        return PROTOCOL_LEDENET_ADDRESSABLE_A3;
    }

    // Protocol supports dimmable effects.
    public function DimmableEffects()
    {
        return true;
    }

    // The bytes to send for a level change request.
    # b0 [unknown static?] b1 [unknown static?] b2 [unknown static?] b3 [unknown static?] 00 [unknown static?] 01 [unknown static?] 01 [unknown static?] 6a [incrementing sequence number] 00 [unknown static?] 0d [unknown, sometimes 0c] 41 [unknown static?] 02 [preset number] ff [foreground r] 00 [foreground g] 00 [foreground b] 00 [background red] ff [background green] 00 [background blue] 06 [speed or direction?] 00 [unknown static?] 00 [unknown static?] 00 [unknown static?] 47 [speed or direction?] cd [check sum]
    # Known messages
    # b0 b1 b2 b3 00 01 01 01 00 0c 10 14 15 0a 0b 0e 12 06 01 00 0f 84 dd - preset 1
    # b0 b1 b2 b3 00 01 01 03 00 0d 41 02 00 ff ff 00 00 00 06 00 00 00 47 66 - preset 2
    # b0 b1 b2 b3 00 01 01 04 00 0d 41 03 00 ff ff 00 00 00 06 00 00 00 48 69 - preset 3
    # b0 b1 b2 b3 00 01 01 02 00 0d 41 01 00 ff ff 00 00 00 06 ff 00 00 45 61 - preset 4
    # b0 b1 b2 b3 00 01 01 1f 00 0d 41 01 ff 00 00 00 00 00 06 ff 00 00 46 80 - preset 1 red or green
    # b0 b1 b2 b3 00 01 01 27 00 0d 41 01 00 ff 00 00 00 00 06 ff 00 00 46 88 - preset 1 red or green
    # b0 b1 b2 b3 00 01 01 2e 00 0d 41 01 ff 00 00 00 00 00 06 ff 00 00 46 8f - preset 1 red (foreground)
    # b0 b1 b2 b3 00 01 01 27 00 0d 41 01 00 ff 00 00 00 00 06 ff 00 00 46 88 - preset 1 green (foreground)
    # b0 b1 b2 b3 00 01 01 3e 00 0d 41 01 00 00 ff 00 00 00 06 ff 00 00 46 9f - preset 1 blue (foreground)
    # b0 b1 b2 b3 00 01 01 54 00 0d 41 02 00 ff 00 00 00 00 06 00 00 00 48 b9 - preset 2 green (foreground)
    # b0 b1 b2 b3 00 01 01 55 00 0d 41 02 ff 00 00 00 00 00 06 00 00 00 48 ba - preset 2 red (foreground)
    # b0 b1 b2 b3 00 01 01 67 00 0d 41 02 ff 00 00 ff 00 00 06 00 00 00 47 ca - preset 2 red (foreground), red (background)
    # b0 b1 b2 b3 00 01 01 67 00 0d 41 02 ff 00 00 ff 00 00 06 00 00 00 47 ca - preset 2 red (foreground), red (background)
    # b0 b1 b2 b3 00 01 01 69 00 0d 41 02 ff 00 00 ff 00 00 06 00 00 00 47 cc - preset 2 red (foreground), red (background)
    # b0 b1 b2 b3 00 01 01 6a 00 0d 41 02 ff 00 00 00 ff 00 06 00 00 00 47 cd - preset 2 red (foreground), green (background)
    # b0 b1 b2 b3 00 01 01 77 00 0d 41 02 ff 00 00 00 ff 00 06 00 00 00 47 da - preset 2 red (foreground), green (background) - direction RTL
    # b0 b1 b2 b3 00 01 01 7d 00 0d 41 02 ff 00 00 00 ff 00 06 00 00 00 47 e0 - preset 2 red (foreground), green (background) - direction RTL
    # b0 b1 b2 b3 00 01 01 7d 00 0d 41 02 ff 00 00 00 ff 00 06 00 00 00 47 e0 - preset 2 red (foreground), green (background) - direction RTL
    # b0 b1 b2 b3 00 01 01 7c 00 0d 41 02 ff 00 00 00 ff 00 06 01 00 00 48 e1 - preset 2 red (foreground), green (background) - direction LTR
    # b0 b1 b2 b3 00 01 01 89 00 0d 41 02 ff 00 00 00 ff 00 00 00 00 00 41 e0 - preset 2 red (foreground), green (background) - direction LTR - speed 0
    # b0 b1 b2 b3 00 01 01 8a 00 0d 41 02 ff 00 00 00 ff 00 64 00 00 00 a5 a9 - preset 2 red (foreground), green (background) - direction LTR - speed 64
    # b0 b1 b2 b3 00 01 01 8b 00 0d 41 02 ff 00 00 00 ff 00 00 00 00 00 41 e2 - preset 2 red (foreground), green (background) - direction LTR - speed 0?
    # b0 b1 b2 b3 00 01 01 8c 00 0d 41 02 ff 00 00 00 ff 00 64 00 00 00 a5 ab - preset 2 red (foreground), green (background) - direction LTR - speed 64?
    # Set Blue
    # b0b1b2b30001010b0034a0000600010000ff0000ff0002ffff000000ff00030000ff0000ff0004ffff000000ff00050000ff0000ff0006ffff000000ffac5f
    # Query
    # b0b1b2b30001010c0004818a8b9604
    # b0b1b2b30001010c000e811a23280000640f000001000660a2
    # Set Red
    # b0b1b2b30001010d0034a0000600010000ff0000ff0002ff00000000ff00030000ff0000ff0004ff00000000ff00050000ff0000ff0006ff00000000ffaf67
    public function ConstructLevelsChange($persist, $red, $green, $blue, $warmwhite, $coolwhite, $writemode)
    {
        $counter_byte = $this->IncrementCounter();
        $preset_number = 0x01;  # aka fixed color
        $msg = [0x41, $preset_number, $red, $green, $blue, 0x00, 0x00, 0x00, 0x06, 0x01, 0x00, 0x00];
        $inner_message = $this->ConstructMessage($msg);
        $msg = array_merge($this->ADDRESSABLE_HEADER, [$counter_byte, 0x00, 0x0D]);
        $msg = array_merge($msg, $inner_message);
        return $this->ConstructMessage($msg);
    }

    // The bytes to send for a preset pattern.
    public function ConstructPresetPattern($pattern, $speed, $brightness)
    {
        $counter_byte = $this->IncrementCounter();
        $msg[] = array_merge($this->ADDRESSABLE_HEADER, [$counter_byte, 0x00, 0x05, 0x42, $pattern, $speed, $brightness, 0x00]);
        return $this->ConstructMessage($msg);
    }
}

// The newer LEDENET protocol CCT
class ProtocolLEDENETCCT extends ProtocolLEDENET9Byte
{
    private $MIN_BRIGHTNESS = 2;

    // The id of the protocol.
    public function Id()
    {
        return PROTOCOL_LEDENET_CCT;
    }

    // Protocol supports dimmable effects.
    public function DimmableEffects()
    {
        return false;
    }

    // The bytes to send for a level change request.
    # b0 b1 b2 b3 00 01 01 52 00 09 35 b1 00 64 00 00 00 03 4d bd - 100% warm
    # b0 b1 b2 b3 00 01 01 72 00 09 35 b1 64 64 00 00 00 03 b1 a5 - 100% cool
    # b0 b1 b2 b3 00 01 01 9f 00 09 35 b1 64 32 00 00 00 03 7f 6e - 100% cool - dim 50%
    public function ConstructLevelsChange($persist, $red, $green, $blue, $warmwhite, $coolwhite, $writemode)
    {
        $counter_byte = $this->IncrementCounter();
        $tb = $this->WhiteLevelsToScaledColorTemp($warmwhite, $coolwhite);
        $scaled_temp = $tb[0];
        $brightness = $tb[1];
        # If the brightness goes below the precision the device
        # will flip from cold to warm
        $msg = [0x35, 0xB1, $scaled_temp, max($this->MIN_BRIGHTNESS, $brightness), 0x00, 0x00, 0x00, 0x03];
        $inner_message = $this->ConstructMessage($msg);
        $msg = array_merge($this->ADDRESSABLE_HEADER, [$counter_byte, 0x00, 0x09]);
        $msg = array_merge($msg, $inner_message);
        return $this->ConstructMessage($msg);
    }
}

/**
 * Helper class for the debug output.
 */
trait MagicHelper
{
    /**
     * Extract preset profile name from protocol.
     *
     * @param int $value protocol number
     * @return string Preset Profile Name.
     */
    private function GetPatternProfile(int $value): string
    {
        $pattern = 'MHC.Preset';
        foreach (MAGIC_HOME_PATTERN as $profile => $protocols) {
            if (in_array($value, $protocols)) {
                $pattern = $profile;
                break;
            }
        }
        return $pattern;
    }
}