Leag CMS - AOS-inspirierter Style

Installation:
1. Dateien auf den Webspace laden.
2. Datenbank anlegen und install.sql importieren.
3. app/config.php mit deinen Datenbankdaten anpassen.
4. Ersten Benutzer registrieren.
5. In phpMyAdmin den Benutzer auf Admin setzen:
   UPDATE users SET role='admin' WHERE username='DEINNAME';

Hinweise:
- Der neue Style ist ein eigenständiger, dunkler Gaming-/Community-Look, inspiriert vom Aufbau moderner Projektseiten wie AOS.
- Hintergrundgrafik liegt unter assets/img/hero-bg.svg.
- Farben und Layout kannst du in assets/css/style.css anpassen.


Update: Admin-Editor
- News und Seiten haben jetzt einen eingebauten HTML-Editor mit Toolbar und Vorschau.
- Der Editor ist lokal unter /assets/js/editor.js eingebunden und braucht keine externe CDN-Datei.
