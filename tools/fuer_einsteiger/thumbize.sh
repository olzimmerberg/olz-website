# Skript, um die quadratischen, verkleinerten Bilder für die "Für Einsteiger" Seite zu generieren

thumbize () {
    IDENT=$1
    WIDTH=$2
    HEIGHT=$3
    XSIGN=$4
    XOFF=$5
    YSIGN=$6
    YOFF=$7
    TWO_WIDTHS=$((WIDTH + WIDTH))
    TWO_HEIGHTS=$((HEIGHT + HEIGHT))
    TWO_XOFFS=$((XOFF + XOFF))
    TWO_YOFFS=$((YOFF + YOFF))
    CROP_SMALL="${WIDTH}x$HEIGHT$XSIGN$XOFF$YSIGN$YOFF"
    CROP_BIG="${TWO_WIDTHS}x$TWO_HEIGHTS$XSIGN$TWO_XOFFS$YSIGN$TWO_YOFFS"
    echo "$IDENT $CROP_SMALL $CROP_BIG"
    convert "./img/$IDENT.jpg" -resize "$WIDTH^" -gravity center -crop $CROP_SMALL +repage "./thumb/$IDENT.jpg"
    convert "./img/$IDENT.jpg" -resize "$TWO_WIDTHS^" -gravity center -crop $CROP_BIG +repage "./thumb/$IDENT@2x.jpg"
}

thumbize "ansprechperson_001" 200 200 + 0 + 0
thumbize "ansprechperson_002" 200 200 + 0 - 20
thumbize "ansprechperson_003" 200 200 + 10 + 0
thumbize "ansprechperson_004" 200 200 + 0 + 0
thumbize "ol_zimmerberg_001" 100 100 + 0 + 0
thumbize "ol_zimmerberg_002" 100 100 - 10 + 0
thumbize "ol_zimmerberg_003" 100 100 + 0 + 0
thumbize "ol_zimmerberg_004" 100 100 + 0 + 0
thumbize "ol_zimmerberg_005" 100 100 + 0 + 0
thumbize "ol_zimmerberg_006" 100 100 + 0 + 0
thumbize "ol_zimmerberg_007" 100 100 + 0 + 0
thumbize "ol_zimmerberg_008" 100 100 + 0 + 0
thumbize "ol_zimmerberg_009" 100 100 + 0 + 0
thumbize "ol_zimmerberg_010" 100 100 + 0 + 0
thumbize "ol_zimmerberg_011" 100 100 + 0 + 0
thumbize "ol_zimmerberg_012" 100 100 + 0 - 15
thumbize "ol_zimmerberg_013" 100 100 + 0 + 0
thumbize "ol_zimmerberg_014" 100 100 + 0 + 0
thumbize "ol_zimmerberg_015" 100 100 + 0 + 0
thumbize "ol_zimmerberg_016" 100 100 + 0 + 0
thumbize "orientierungslauf_001" 200 200 + 45 + 0
thumbize "orientierungslauf_002" 200 200 + 0 + 30
thumbize "orientierungslauf_003" 200 200 + 0 + 0
thumbize "orientierungslauf_004" 200 200 + 0 + 32
thumbize "pack_die_chance_001" 400 400 + 0 + 20
thumbize "trainings_001" 100 100 - 12 + 0
thumbize "trainings_002" 100 100 + 0 + 0
thumbize "trainings_003" 100 100 - 5 + 0
thumbize "trainings_004" 100 100 + 0 + 0
thumbize "trainings_005" 100 100 + 0 + 0
thumbize "trainings_006" 100 100 + 0 + 0
thumbize "trainings_007" 100 100 + 0 + 0
thumbize "trainings_008" 100 100 - 15 + 0
thumbize "trainings_009" 100 100 - 10 + 0
thumbize "trainings_010" 100 100 + 0 + 0
thumbize "trainings_011" 100 100 - 5 + 0
thumbize "trainings_012" 100 100 + 0 + 0
thumbize "trainings_013" 100 100 - 10 + 0
thumbize "trainings_014" 100 100 - 10 + 0
thumbize "trainings_015" 100 100 - 20 + 0
thumbize "trainings_016" 100 100 + 0 + 0
thumbize "was_ist_ol_001" 400 400 + 0 + 0
thumbize "wie_anfangen_001" 200 200 + 0 + 0
thumbize "wie_anfangen_002" 200 200 + 0 + 0
thumbize "wie_anfangen_003" 200 200 + 0 + 0
thumbize "wie_anfangen_004" 200 200 + 0 + 0
