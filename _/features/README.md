# `features/`: Feature-Switches

Hier wird die Möglichkeit geschaffen, mit `localStorage` gewisse Features zu aktivieren, die anderen Benutzern verborgen bleiben.

Um also ein gewisses HTML Element zu verbergen, wird diesem `class="feature <feature-name>"` hinzugefügt.

Um Zugriff auf ein so definiertes Feature (`<feature-name>`) zu erlangen, muss in der `localStorage` unter dem Schlüssel `FEATURES` eine komma-separierte Liste von Feature-Namen eingetragen werden, die unter anderem `<feature-name>` enthält.