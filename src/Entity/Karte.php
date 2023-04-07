<?php

namespace Olz\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'karten')]
#[ORM\Entity]
class Karte {
    #[ORM\Id]
    #[ORM\Column(type: 'integer', nullable: false)]
    #[ORM\GeneratedValue]
    private $id;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $position;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $kartennr;

    #[ORM\Column(type: 'string', nullable: true)]
    private $name;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $center_x;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $center_y;

    #[ORM\Column(type: 'string', nullable: true)]
    private $jahr;

    #[ORM\Column(type: 'string', nullable: true)]
    private $massstab;

    #[ORM\Column(type: 'string', nullable: true)]
    private $ort;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $zoom;

    #[ORM\Column(type: 'string', nullable: true)]
    private $typ;

    #[ORM\Column(type: 'string', nullable: true)]
    private $vorschau;
    // PRIMARY KEY (`id`)
}
