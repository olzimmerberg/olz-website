# `components/`: Wiederverwendbare visuelle Komponenten

Die Idee ist, dass jeder Komponent folgende Dateien enthält:

- `{$component_name}.php`: Enthält eine PHP Funktion namens `{$component_name}`, die das HTML für den Komponenten zurückgibt.
- `{$component_name}.js`: Enthält Skripte für den Browser, die für diesen Komponenten benötigt werden.
- `{$component_name}.scss`: Enthält die CSS-Stil-Definitionen, die für diesen Komponenten benötigt werden.
- `{$component_name}_test.php`: Testet den Komponenten. Bildschirmfoto-Tests wären toll.
