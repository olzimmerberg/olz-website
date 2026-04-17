<?php

namespace Olz\Repository\Snippets;

enum PredefinedSnippet: int {
    /** @deprecated Use AngebotTrainings, AngebotKarten, etc. instead! */
    case Angebot = 1;
    case AngebotTrainings = 13;
    case AngebotStarterpack = 14;
    case AngebotKarten = 15;
    case AngebotKleider = 16;
    case AngebotMaterial = 17;
    case AngebotDienstleistungen = 18;
    case TermineDownloadsLinks = 2;
    case TermineNewsletter = 3;
    case AnniversaryHoehenmeter = 9;
    case AnniversaryZielsprint = 10;
    case KartenVerkauf = 12;
    case StartseiteBanner = 22;
    case StartseiteCustomTile = 24;
}
