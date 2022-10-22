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
     * @function dark mode
     * @summary: changes the theme to 'dark mode' and save settings to local storage.
     * Basically, replaces/toggles every CSS class that has '-light' class with '-dark'
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
          // Replace the class table-light with table-dark in each table
          tables[i].classList.replace('table-light', 'table-dark');
        }
      }

      // set light switch input to true
      if (!lightSwitch.checked) {
        lightSwitch.checked = false;
      }
      localStorage.setItem('lightSwitch', 'dark');
    }

    /**
     * @function light-mode
     * @summary: changes the theme to 'light mode' and save settings to local storage.
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
          // Replace the class table-dark with table-light in each table
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
     * @summary: the event handler attached to the switch. calling @darkMode or @lightMode depending on the checked state.
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
     * @summary: get system default theme by media query
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
