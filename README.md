# MHC - Magic Home Controller

[![Version](https://img.shields.io/badge/Symcon-PHP--Modul-red.svg)](https://www.symcon.de/service/dokumentation/entwicklerbereich/sdk-tools/sdk-php/)
[![Version](https://img.shields.io/badge/Symcon%20Version-5.2-blue.svg)](https://www.symcon.de/produkt/)
[![Version](https://img.shields.io/badge/Modul%20Version-2.0.20210701-orange.svg)](https://github.com/Wilkware/IPSymconMHC)
[![Version](https://img.shields.io/badge/License-CC%20BY--NC--SA%204.0-green.svg)](https://creativecommons.org/licenses/by-nc-sa/4.0/)
[![Actions](https://github.com/Wilkware/IPSymconMHC/workflows/Check%20Style/badge.svg)](https://github.com/Wilkware/IPSymconMHC/actions)

IP-Symcon Modul für die Ansteuerung von WiFi LED Controller der Firma _Magic Home_.

## Inhaltverzeichnis

1. [Funktionsumfang](#1-funktionsumfang)
2. [Voraussetzungen](#2-voraussetzungen)
3. [Installation](#3-installation)
4. [Einrichten der Instanzen in IP-Symcon](#4-einrichten-der-instanzen-in-ip-symcon)
5. [Statusvariablen und Profile](#5-statusvariablen-und-profile)
6. [WebFront](#6-webfront)
7. [PHP-Befehlsreferenz](#7-php-befehlsreferenz)
8. [Versionshistorie](#8-versionshistorie)

### 1. Funktionsumfang

Das Modul dient zur Ansteuerung von LED Stripes mittels eines WiFi LED Controllers des Herstellers Magic Home.
Das sind Controller vom Typ LD382 bzw. LD382A.

Wenn jemand noch weitere kennt, bitte einfach bei mir melden!

### 2. Voraussetzungen

* IP-Symcon ab Version 5.2

### 3. Installation

* Über den Modul Store das Modul 'Magic Home Controller' installieren.
* Alternativ Über das Modul-Control folgende URL hinzufügen.  
`https://github.com/Wilkware/IPSymconMHC` oder `git://github.com/Wilkware/IPSymconMHC.git`

### 4. Einrichten der Instanzen in IP-Symcon

* Unter "Instanz hinzufügen" ist das *Magic Home Controller*-Modul (Alias: *LED Controller*) unter dem Hersteller _'(Geräte)'_ aufgeführt.

__Konfigurationsseite__:

Die Konfiguration beinhaltet die Zuweisung der IP-Adresse und der Auswahl der Farbkanal-Reihenfolge (RGB-Pins).  
Derzeit werden Stripes mit der Reihenfolge GRB (Gelb, Rot, Blau) und BRG (Blau, Rot, Gelb) unterstützt.  
Die Reihenfolge der Pins beginnt immer ausgehend vom GND-Pin (12V Pin).

Einstellungsbereich:

> Einstellungen ...

Name               | Beschreibung
------------------ | ---------------------------------
WiFi Controller IP | IP-Adresse des Controlers im lokalen WLAN (zb. 192.168.0.10)
RGB Pin Belegung   | Reihenfolge der Farb-Pins (GRB oder BRG), Standard ist GRB

### 5. Statusvariablen und Profile

Ident         | Name                | Typ       |  Profil                      | Beschreibung
------------- | ------------------- | --------- | ---------------------------- | -------------------------------------------------------
Brightness    | Helligkeit          | Integer   | ~Intensity.100               | Helligkeitswert von 0 bis 100%
Color         | Farbe               | Integer   | ~HexColor                    | Farbwert
Mode          | Modus               | Integer   | MHC.ModeGRB oder MHC.ModeBRG | Manueller Farbmodus oder vordefinierter Funktionsmodus
Power         | Aktiv               | Boolean   | ~Switch                      | An/Aus Schalter
Speed         | Geschwindigkeit     | Integer   | ~Intensity.100               | Geschwindigkeitswert von 0 bis 100%

### 6. WebFront

Man kann die Statusvariaben direkt im WebFront verlinken.

### 7. PHP-Befehlsreferenz

```php
void MHC_SetBrightness(int $InstanzID, int $Brightness);
```

Setzt die Helligkeit auf $Brightness. Die Funktion liefert keinerlei Rückgabewert.

```php
void MHC_SetColor(int $InstanzID, int $Color);
```

Setzt den Farbwert auf $Color. Die Funktion liefert keinerlei Rückgabewert.

```php
void MHC_SetMode(int $InstanzID, int $Mode);
```

Setzt den Anzeigemodus auf auf $Mode. Die Funktion liefert keinerlei Rückgabewert.

```php
void MHC_Power(int $InstanzID, bool $Power);
```

Schaltet den Controler Ein(true) bzw Aus(false). Die Funktion liefert keinerlei Rückgabewert.

### 8. Versionshistorie

v2.0.20210701

* _NEU_: Konfigurationsformular vereinheitlicht
* _FIX_: Berechnung der Geschwindigkeit bei Programmen
* _FIX_: Code optimiert und Übersetzungen nachgezogen
* _FIX_: Interne Bibliotheken überarbeitet und vereinheitlicht
* _FIX_: Debug Meldungen überarbeitet
* _FIX_: Dokumentation überarbeitet

v1.2.20190812

* _NEU_: Anpassungen für Module Store
* _NEU_: Vereinheitlichungen, Umstellung auf Libs
* _FIX_: Fehler in Profilen korrigiert

v1.1.20190225

* _NEU_: Umbenennungen, Vereinheitlichungen, StyleCI uvm.

v1.0.20180415

* _NEU_: Initialversion

## Danksagung

Dieses Modul basiert auf dem Modul von ...

* _Spoosie_ : Modul _KH\_LEDWiFiController_ <https://github.com/Spoosie/KH_LEDWiFiController>

Vielen Dank für die hervorragende und tolle Arbeit!

## Entwickler

Seit nunmehr über 10 Jahren fasziniert mich das Thema Haussteuerung. In den letzten Jahren betätige ich mich auch intensiv in der IP-Symcon Community und steuere dort verschiedenste Skript und Module bei. Ihr findet mich dort unter dem Namen @pitti ;-)

[![GitHub](https://img.shields.io/badge/GitHub-@wilkware-blueviolet.svg?logo=github)](https://wilkware.github.io/)

## Spenden

Die Software ist für die nicht kommzerielle Nutzung kostenlos, über eine Spende bei Gefallen des Moduls würde ich mich freuen.

[![PayPal](https://img.shields.io/badge/PayPal-spenden-blue.svg?logo=paypal)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=8816166)

## Lizenz

[![Licence](https://licensebuttons.net/i/l/by-nc-sa/transparent/00/00/00/88x31-e.png)](https://creativecommons.org/licenses/by-nc-sa/4.0/)
