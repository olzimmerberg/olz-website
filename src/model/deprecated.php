<?php

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="anm_felder")
 */
class AnmFelder {
    /**
     * @ORM\Id @ORM\Column(type="integer", nullable=false) @ORM\GeneratedValue
     */
    private $id;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $event_id;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $position;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $zeigen;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $label;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $typ;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $info;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $standard;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $test;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $test_result;
    // PRIMARY KEY (`id`)
}

/**
 * @ORM\Entity
 * @ORM\Table(name="anmeldung")
 */
class Anmeldung {
    /**
     * @ORM\Id @ORM\Column(type="integer", nullable=false) @ORM\GeneratedValue
     */
    private $id;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $event_id;
    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $datum;
    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $zeit;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $anzahl;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $email;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $uid;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $on_off;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $name;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $feld1;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $feld2;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $feld3;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $feld4;
    // PRIMARY KEY (`id`)
}

/**
 * @ORM\Entity
 * @ORM\Table(name="event")
 */
class Event {
    /**
     * @ORM\Id @ORM\Column(type="integer", nullable=false) @ORM\GeneratedValue
     */
    private $id;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $name_kurz;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $name;
    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $datum;
    /**
     * @ORM\Column(type="integer", options={"default": 0})
     */
    private $counter_ip_lan;
    /**
     * @ORM\Column(type="integer", options={"default": 0})
     */
    private $counter_hit_lan;
    /**
     * @ORM\Column(type="integer", options={"default": 0})
     */
    private $counter_ip_web;
    /**
     * @ORM\Column(type="integer", options={"default": 0})
     */
    private $counter_hit_web;
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $stand;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $kat_gruppen;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $karten;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $locked;
    // PRIMARY KEY (`id`)
}

/**
 * @ORM\Entity
 * @ORM\Table(name="facebook_settings")
 */
class FacebookSetting {
    /**
     * @ORM\Id @ORM\Column(type="string", nullable=false)
     */
    private $k;
    /**
     * @ORM\Column(type="text", nullable=false)
     */
    private $v;
    // PRIMARY KEY (`k`)
}

/**
 * @ORM\Entity
 * @ORM\Table(name="images")
 */
class Image {
    /**
     * @ORM\Id @ORM\Column(type="integer", nullable=false) @ORM\GeneratedValue
     */
    private $id;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $id_parent;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $table_parent;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $pfad;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $bild_name;
    // PRIMARY KEY (`id`)
}

/**
 * @ORM\Entity
 * @ORM\Table(name="jwoc")
 */
class Jwoc {
    /**
     * @ORM\Id @ORM\Column(type="integer", nullable=false) @ORM\GeneratedValue
     */
    private $id;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $nr;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $name;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $nation;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $pos;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $time1;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $time2;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $time3;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $time4;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $time5;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $diff;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $starttime;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $cat;
    // PRIMARY KEY (`id`)
}

/**
 * @ORM\Entity
 * @ORM\Table(name="olz_result")
 */
class OlzResult {
    /**
     * @ORM\Id @ORM\Column(type="integer", nullable=false) @ORM\GeneratedValue
     */
    private $id;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $rang;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $name;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $club;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $jg;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $zeit;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $kat;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $stand;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $anzahl;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $event;
    // PRIMARY KEY (`id`)
}

/**
 * @ORM\Entity
 * @ORM\Table(name="termine_go2ol")
 */
class TerminGo2ol {
    /**
     * @ORM\Id @ORM\Column(type="integer", nullable=false)
     */
    private $solv_uid;
    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $link;
    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $ident;
    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $name;
    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $post;
    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $verein;
    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $datum;
    /**
     * @ORM\Column(type="date", nullable=false)
     */
    private $meldeschluss1;
    /**
     * @ORM\Column(type="date", nullable=false)
     */
    private $meldeschluss2;
    // PRIMARY KEY (`solv_uid`)
}

/**
 * @ORM\Entity
 * @ORM\Table(name="termine_solv")
 */
class TerminSolv {
    /**
     * @ORM\Id @ORM\Column(type="integer", nullable=true)
     */
    private $solv_uid;
    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $date;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $duration;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $kind;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $day_night;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $national;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $region;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $type;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $event_name;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $event_link;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $club;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $map;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $location;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $coord_x;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $coord_y;
    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $deadline;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $entryportal;
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $last_modification;
}

/**
 * @ORM\Entity
 * @ORM\Table(name="trainingsphotos")
 */
class Trainingsphoto {
    /**
     * @ORM\Id @ORM\Column(type="integer", nullable=false) @ORM\GeneratedValue
     */
    private $id;
    /**
     * @ORM\Column(type="text", nullable=false)
     */
    private $name;
    /**
     * @ORM\Column(type="date", nullable=false)
     */
    private $datum;
    /**
     * @ORM\Column(type="text", nullable=false)
     */
    private $pfad;
    // PRIMARY KEY (`id`)
}
