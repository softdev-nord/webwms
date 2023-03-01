/**
 *  Light Switch @version v0.1.4
 */
$(document).ready(function(){
  (function () {
    const lightSwitch = document.getElementById('lightSwitch');
    if (!lightSwitch) {
      return;
    }

    /**
     * @function Dunkler Modus
     * @summary: Ändert das Design in den "dunklen Modus" und speichert die Einstellungen im lokalen Speicher.
     * Ersetzt jede CSS-Klasse, die die Klasse '-light' hat, durch '-dark' und schaltet sie um.
     */
    function darkMode() {
      document.querySelectorAll('.bg-light').forEach((element) => {
        element.className = element.className.replace(/-light/g, '-dark');
      });

      document.body.classList.add('bg-dark');

      if (document.body.classList.contains('text-dark')) {
        document.body.classList.replace('text-dark', 'text-light');
      } else {
        document.body.classList.add('text-light');
      }

      // Tables
      const tables = document.querySelectorAll('table');
      for (let i = 0; i < tables.length; i++) {
        if (tables[i].classList.contains('table-light')) {
          // Ersetzt die Klasse table-light durch table-dark in jeder Tabelle
          tables[i].classList.replace('table-light', 'table-dark');
        }
      }

      if (!lightSwitch.checked) {
        lightSwitch.checked = false;
      }
      localStorage.setItem('lightSwitch', 'dark');
    }

    /**
     * @function light-mode
     * @summary: Ändert das Design in "hellen Modus"
     * und speichert die Einstellungen im lokalen Speicher.
     */
    function lightMode() {
      document.querySelectorAll('.bg-dark').forEach((element) => {
        element.className = element.className.replace(/-dark/g, '-light');
      });

      document.body.classList.add('bg-light');

      if (document.body.classList.contains('text-light')) {
        document.body.classList.replace('text-light', 'text-dark');
      } else {
        document.body.classList.add('text-dark');
      }

      // Tables
      const tables = document.querySelectorAll('table');
      for (let i = 0; i < tables.length; i++) {
        if (tables[i].classList.contains('table-dark')) {
          // Ersetzt in jeder Tabelle die Klasse table-dark durch table-light
          tables[i].classList.replace('table-dark', 'table-light');
        }
      }

      if (lightSwitch.checked) {
        lightSwitch.checked = true;
      }
      localStorage.setItem('lightSwitch', 'light');
    }

    /**
     * @function onToggleMode
     * @summary: Der an den Switch angehängte event handler,
     * der je nach Kontrollstatus @darkMode oder @lightMode aufruft.
     */
    function onToggleMode() {
      if (!lightSwitch.checked) {
        darkMode();
      } else {
        lightMode();
      }
    }

    /**
     * @function getSystemDefaultTheme
     * @summary: System-Standard Design per Medienabfrage abrufen
     */
    function getSystemDefaultTheme() {
      const darkThemeMq = window.matchMedia('(prefers-color-scheme: dark)');
      if (darkThemeMq.matches) {
        return 'dark';
      }
      return 'light';
    }

    function setup() {
      let settings = localStorage.getItem('lightSwitch');
      if (settings == null) {
        settings = getSystemDefaultTheme();
      }

      if (settings === 'dark') {
        lightSwitch.checked = false;
      }

      lightSwitch.addEventListener('change', onToggleMode);
      onToggleMode();
    }

    setup();
  })();
});
