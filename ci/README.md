# `ci/`: CI-Hilfsskripte

Skripte für die Continuous-Integration-Pipeline (siehe `.github/`).

- `screenshot-modifications.php`: vergleicht die von den System-Tests erzeugten
  Screenshots (HTML) und stellt Änderungen als Diff dar (siehe
  [`../tests/SystemTests/README.md`](../tests/SystemTests/README.md)).
- `screenshot-upload.php`: lädt die Screenshots hoch.
