<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Domain\Repository;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Extbase\Persistence\Repository;
use WerkraumMedia\Events\Domain\Model\Organizer;

final class OrganizerRepository extends Repository
{
    public function findOneByName(string $name): ?Organizer
    {
        $organizer = $this->findOneBy(['name' => $name]);

        if ($organizer instanceof Organizer) {
            return $organizer;
        }

        return null;
    }
}
