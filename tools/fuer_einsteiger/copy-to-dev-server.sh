# Skript, um die Bilder für die "Für Einsteiger" Seite in den dev server zu kopieren.

rm -R ../../public/img/fuer_einsteiger

mkdir ../../public/img/fuer_einsteiger
cp -r ./img ../../public/img/fuer_einsteiger/img
cp -r ./thumb ../../public/img/fuer_einsteiger/thumb
