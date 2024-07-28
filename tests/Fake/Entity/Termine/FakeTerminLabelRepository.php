<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity\Termine;

use Olz\Entity\Termine\TerminLabel;
use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;

/**
 * @extends FakeOlzRepository<TerminLabel>
 */
class FakeTerminLabelRepository extends FakeOlzRepository {
    public string $olzEntityClass = TerminLabel::class;
    public string $fakeOlzEntityClass = FakeTerminLabel::class;

    public function findOneBy(array $criteria, ?array $orderBy = null): ?object {
        $is_ident_valid = [
            'programm' => true,
            'weekend' => true,
            'training' => true,
            'trophy' => true,
            'ol' => true,
            'club' => true,
        ];
        $ident = $criteria['ident'] ?? '';
        if ($is_ident_valid[$ident] ?? false) {
            $termin_label = FakeTerminLabel::maximal();
            $termin_label->setIdent($ident);
            return $termin_label;
        }
        return parent::findOneBy($criteria);
    }
}
