# Magic Home Discovery

[![Home](https://img.shields.io/badge/Home-wilkware.de-0b1830.svg?style=flat-square)](https://wilkware.de/module/mhc/)
[![Version](https://img.shields.io/badge/Symcon-PHP--Modul-red.svg?style=flat-square)](https://www.symcon.de/service/dokumentation/entwicklerbereich/sdk-tools/sdk-php/)
[![Product](https://img.shields.io/badge/Symcon%20Version-8.1-blue.svg?style=flat-square)](https://www.symcon.de/produkt/)
[![Version](https://img.shields.io/badge/Modul%20Version-4.0.20261021-orange.svg?style=flat-square)](https://github.com/Wilkware/MagicHomeController)
[![License](https://img.shields.io/badge/License-CC%20BY--NC--SA%204.0-green.svg?style=flat-square)](https://creativecommons.org/licenses/by-nc-sa/4.0/)
[![Actions](https://img.shields.io/github/actions/workflow/status/wilkware/MagicHomeController/ci.yml?branch=main&label=CI&style=flat-square)](https://github.com/Wilkware/MagicHomeController/actions)

Ermöglicht die Gerätesuche für alle im Netzwerk befindlichen _Magic Home_ WiFi LED Controller.

## Inhaltverzeichnis

1. [Funktionsumfang](#user-content-1-funktionsumfang)
2. [Voraussetzungen](#user-content-2-voraussetzungen)
3. [Installation](#user-content-3-installation)
4. [Einrichtung](#user-content-4-einrichtung)
5. [Statusvariablen](#user-content-5-statusvariablen)
6. [Darstellungen](#user-content-6-darstellungen)
7. [Visualisierung](#user-content-7-visualisierung)
8. [Befehlsreferenz](#user-content-8-befehlsreferenz)
9. [Versionshistorie](#user-content-9-versionshistorie)

### 1. Funktionsumfang

* Suchen der im lokalen Netzwerk befindlichen Magic Home Controller (UDP-Broadcast)
* Anzeige von IP-Adresse, ID (MAC), Modell, Typ, Version und Firmware
* Erstellen von _Magic Home Controller_-Instanzen inkl. vorbelegter Konfiguration

### 2. Voraussetzungen

* Symcon ab Version 8.1

### 3. Installation

* Über den Modul Store die Bibliothek _Magic Home Controller_ installieren.
* Alternativ über das Modul Control folgende URL hinzufügen.  
`https://github.com/Wilkware/MagicHomeController` oder `git://github.com/Wilkware/MagicHomeController.git`

### 4. Einrichtung

* Die Gerätesuche ist über die Glocke oben rechts in der Konsole aufrufbar. Dort kann über 'SYSTEM AUSWÄHLEN' das _Magic Home Discovery_-Modul ausgewählt und installiert werden.

__Konfigurationsseite__:

Innerhalb der Geräteliste werden alle im Netzwerk gefundenen Controller sowie bereits angelegte Instanzen aufgeführt.
Man kann pro Controller eine Instanz anlegen.

_Aktionsbereich:_

Name                    | Beschreibung
----------------------- | ---------------------------------
Magic Home Steuergeräte | Liste zum Verwalten der entsprechenden Controller-Instanzen

### 5. Statusvariablen

Es werden keine Statusvariablen angelegt.

### 6. Darstellungen

Es werden keine Darstellungen oder Profile benötigt.

### 7. Visualisierung

Es ist keine weitere Steuerung oder gesonderte Darstellung integriert.

### 8. Befehlsreferenz

Das Modul bietet keine direkten Funktionsaufrufe.

### 9. Versionshistorie

v4.0.20261021

* _NEU_: Kompatibilität auf Symcon 8.1 hoch gesetzt
* _FIX_: Gerätesuche robuster gegenüber unvollständigen Antworten und unbekannten Typen
* _FIX_: Konfigurationsformular vereinheitlicht
* _FIX_: Übersetzungen nachgezogen
* _FIX_: Dokumentation überarbeitet

v3.0.20221231

* _NEU_: Initialversion

## Entwickler

Seit nunmehr über 10 Jahren fasziniert mich das Thema Haussteuerung. In den letzten Jahren betätige ich mich auch intensiv in der Symcon Community und steuere dort verschiedenste Skript und Module bei. Ihr findet mich dort unter dem Namen @pitti ;-)

[![GitHub](https://img.shields.io/badge/GitHub-@wilkware-181717.svg?style=for-the-badge&logo=github)](https://wilkware.github.io/)

## Spenden

Die Software ist für die nicht kommerzielle Nutzung kostenlos, über eine Spende bei Gefallen des Moduls würde ich mich freuen.

[![PayPal](https://img.shields.io/badge/PayPal-spenden-00457C.svg?style=for-the-badge&logo=paypal)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=8816166)

## Lizenz

Namensnennung - Nicht-kommerziell - Weitergabe unter gleichen Bedingungen 4.0 International

[![Licence](https://img.shields.io/badge/License-CC_BY--NC--SA_4.0-EF9421.svg?style=for-the-badge&logo=creativecommons)](https://creativecommons.org/licenses/by-nc-sa/4.0/)
