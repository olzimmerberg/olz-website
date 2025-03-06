<?php

namespace Olz\Apps\Logs\Utils;

enum HybridState: string {
    case PREFER_PLAIN = 'plain';
    case PREFER_GZ = 'gz';
    case PREFER_BOTH = 'both';
    case KEEP = 'keep';
}
