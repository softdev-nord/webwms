# Datenaustausch und Integrationen

Im Menü **Integration & Technik → Datenaustausch & Integrationen** stehen die
Importhistorie, Feldzuordnungen, Commerce-Verbindungen und importierte
Kanalaufträge bereit.

Über **Daten importieren** können JSON-, XML-, CSV-, XLSX- und SAP-IDoc-Dateien
verarbeitet werden. Für normale Dateien wird der Ressourcentyp angegeben. Bei
SAP-IDocs ermittelt WebWMS den Nachrichtentyp und die Belegnummer aus dem
Kontrollsatz.

Über **Daten exportieren** werden tabellarische Datensätze als JSON, XML, CSV
oder XLSX heruntergeladen. Die Exportmaske erwartet eine JSON-Liste mit
gleichartig aufgebauten Datensätzen und protokolliert den Export als
Austauschjob.

Mappings ordnen Quell- und Zielfelder für ERP, SAP oder Commerce zu. Unterstützt
werden Kopieren, Trimmen, Groß-/Kleinschreibung sowie Zahlenkonvertierungen.
Commerce-Zugangsdaten werden nicht gespeichert; einzutragen ist ausschließlich
der Name der dafür vorgesehenen Umgebungsvariable.
