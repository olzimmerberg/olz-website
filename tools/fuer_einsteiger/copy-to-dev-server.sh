# Skript, um die Bilder für die "Für Einsteiger" Seite in den dev server zu kopieren.

rm -R ../../dev-server/img/fuer_einsteiger

mkdir ../../dev-server/img/fuer_einsteiger
cp -r ./img ../../dev-server/img/fuer_einsteiger/img
cp -r ./thumb ../../dev-server/img/fuer_einsteiger/thumb
